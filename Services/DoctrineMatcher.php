<?php

namespace LaxCorp\ApiBundle\Services;

use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use LaxCorp\ApiBundle\Helper\DoctrineMatcherResult;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DoctrineMatcher
 *
 * @package LaxCorp\ApiBundle\Service
 */
class DoctrineMatcher
{

    const EQ = '=';
    const NEQ = '<>';
    const LT = '<';
    const LTE = '<=';
    const GT = '>';
    const GTE = '>=';
    const IN = 'IN';
    const CONTAINS = 'CONTAINS';
    const NOTCONTAINS = 'NOT CONTAINS';
    const ISNULL = 'IS NULL';
    const ISNOTNULL = 'IS NOT NULL';

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var array
     */
    private $orderings;

    /**
     * @var int
     */
    private $firstResult;

    /**
     * @var int
     */
    private $maxResults;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $databaseTimezone;

    /**
     * @inheritDoc
     */
    public function __construct(RegistryInterface $doctrine, ContainerInterface $container)
    {
        $this->doctrine         = $doctrine;
        $this->databaseTimezone = $container->getParameter('database_timezone');
    }

    /**
     * @param EntityRepository $repository
     * @param array            $fields
     * @param array            $orderings
     * @param int              $firstResult
     * @param int              $maxResults
     * @param string           $alias
     *
     * @return DoctrineMatcherResult
     * @throws \Doctrine\DBAL\DBALException
     */
    public function matching(
        EntityRepository $repository, array $fields = [], array $orderings = null, $firstResult = 0,
        $maxResults = null, $alias = 't'
    ) {
        $this->orderings   = $orderings;
        $this->firstResult = $firstResult;
        $this->maxResults  = $maxResults;
        $this->alias       = $alias;

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->doctrine->getManager()->getClassMetadata($repository->getClassName());

        /*        $fields = array_filter($fields, function ($value) {
                    return !empty($value);
                });*/

        $queryBuilder = $repository->createQueryBuilder($alias);
        $expr         = $queryBuilder->expr();

        foreach ($fields as $field => $value) {
            if ($classMetadata->hasField($field)) {
                $fieldType = $classMetadata->getTypeOfField($field);
                $type      = Type::getType($fieldType);

                $operator = null;
                if (is_array($value)) {
                    $operator = self::IN;
                }

                $values = $this->getValues($value);
                foreach ($values as $mvalue) {
                    //if (!$operator) {
                    list($operator, $value) = $this->separateOperator($mvalue);
                    //}

                    $this->andWhere($queryBuilder, $operator, $field, $type, $value, $expr, $alias);
                }

            } elseif ($classMetadata->hasAssociation($field)) {
                $associationMapping = $classMetadata->getAssociationMapping($field);
                $type               = $associationMapping['type'];

                if ($type == ClassMetadataInfo::MANY_TO_MANY && !is_array($value)) {
                    $queryBuilder
                        ->andWhere($expr->isMemberOf(":$field", "$alias.$field"))
                        ->setParameter($field, $value);

                } else {
                    $className = $classMetadata->getAssociationTargetClass($field);

                    $manager = $this->doctrine->getManager();

                    /** @var EntityRepository $assoc_repository */
                    $assoc_repository = $manager->getRepository($className);

                    $assoc_result = $this->matching($assoc_repository, $value);

                    if (!$assoc_list = $assoc_result->getList()) {
                        return $assoc_result;
                    }

                    $assoc_id = [];
                    foreach ($assoc_list as $assoc) {
                        $assoc_id[] = $assoc->getId();
                    }
                    $queryBuilder->andWhere($expr->in($alias . '.' . $field, $assoc_id));
                }

            }
        }

        $result = new DoctrineMatcherResult($queryBuilder, $orderings, $firstResult, $maxResults, $alias);

        return $result;
    }

    /**
     * @return array
     */
    public function getOrderings()
    {
        return $this->orderings;
    }

    /**
     * @return int
     */
    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * @return int
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $value
     *
     * @return array
     */
    private function separateOperator($value)
    {
        $operators = [
            '<>' => self::NEQ,
            '<=' => self::LTE,
            '>=' => self::GTE,
            '<'  => self::LT,
            '>'  => self::GT,
            '='  => self::EQ,
            '!'  => self::NOTCONTAINS
        ];

        if (is_bool($value)) {
            return [self::EQ, $value];
        }

        if ($value === "null" || $value === "=null") {
            return [self::ISNULL, null];
        }

        if ($value === "<>null" || $value === "!null" || $value === "!=null") {
            return [self::ISNOTNULL, null];
        }

        if (preg_match('/^(?:\s*(!|<>|<=|>=|<|>|=))?(.*)$/', $value, $matches)) {
            $operator = isset($operators[$matches[1]]) ? $operators[$matches[1]] : self::CONTAINS;
            $value    = $matches[2];

        } elseif (is_string($value)) {
            $operator = self::CONTAINS;
        } else {
            $operator = self::EQ;
        }

        return [$operator, $value];
    }


    /**
     * @param QueryBuilder $queryBuilder
     * @param              $operator
     * @param              $field
     * @param              $type
     * @param              $value
     * @param Expr         $expr
     * @param              $alias
     */
    private function andWhere(QueryBuilder $queryBuilder, $operator, $field, $type, $value, Expr $expr, $alias)
    {

        // if enum type
        if ($type instanceof DecimalType) {
            if ($operator === self::CONTAINS) {
                $operator = Comparison::EQ;
            }
            $value = (double)$value;
        }

        if ($type instanceof IntegerType) {
            if ($operator === self::CONTAINS) {
                $operator = Comparison::EQ;
            }
            $value = (integer)$value;
        }

        if ($type instanceof DateTimeType) {
            if ($operator === self::CONTAINS) {
                $operator = Comparison::EQ;
            }

            $dateTime       = $this->toLocalDateTime($value);
            $stringDateTime = $dateTime->format('Y-m-d H:i:s');
            $value          = $expr->literal($stringDateTime);
        }

        if ($operator == self::CONTAINS) {
            $queryBuilder->andWhere($expr->like($alias . '.' . $field, $expr->literal("%$value%")));

        } elseif ($operator == self::NOTCONTAINS) {
            $queryBuilder->andWhere($expr->notLike($alias . '.' . $field, $expr->literal("%$value%")));

        } elseif ($operator == self::IN) {
            $queryBuilder->andWhere($expr->in($alias . '.' . $field, $value));

        } elseif ($operator == self::LT) {
            $queryBuilder->andWhere($expr->lt($alias . '.' . $field, $value));

        } elseif ($operator == self::LTE) {
            $queryBuilder->andWhere($expr->lte($alias . '.' . $field, $value));

        } elseif ($operator == self::GT) {
            $queryBuilder->andWhere($expr->gt($alias . '.' . $field, $value));

        } elseif ($operator == self::GTE) {
            $queryBuilder->andWhere($expr->gte($alias . '.' . $field, $value));

        } elseif ($operator == self::ISNULL) {
            $queryBuilder->andWhere($expr->isNull($alias . '.' . $field));

        } elseif ($operator == self::ISNOTNULL) {
            $queryBuilder->andWhere($expr->isNotNull($alias . '.' . $field));

        } elseif ($operator == self::NEQ) {
            $queryBuilder->andWhere($expr->neq($alias . '.' . $field, $value));

        } else {
            $comparison = new Comparison($alias . '.' . $field, $operator, $expr->literal($value));
            $queryBuilder->andWhere($comparison);
        }
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getValues($value)
    {
        if (is_array($value)) {
            return [$value];
        }
        if (preg_match('/^\((.*)(?:, |,)(.*)\)$/', $value, $matched)) {
            if (!mb_strlen($matched[2])) {
                return [$value];
            }
            array_shift($matched);

            return $matched;
        }

        return [$value];
    }

    /**
     * @param $value
     *
     * @return \DateTime
     */
    public function toLocalDateTime($value)
    {
        if (!preg_match('/\+\d+:\d+$/', $value)) {
            $value .= '+00:00';
        }

        $inputDateTime = new \DateTime($value);
        $timestamp     = $inputDateTime->getTimestamp();

        $dateTime = new \DateTime(null, new \DateTimeZone($this->databaseTimezone));
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }
}