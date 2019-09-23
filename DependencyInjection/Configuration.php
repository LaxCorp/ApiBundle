<?php

namespace LaxCorp\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @inheritdoc
 */
class Configuration implements ConfigurationInterface
{

    const ROOT = 'api';

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this::ROOT);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->end();

        return $treeBuilder;
    }
}