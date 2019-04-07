<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\DependencyInjection;

use Symfony\Bundle\MakerBundle\DependencyInjection\CompilerPass\MakeCommandRegistrationPass;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MakerExtension extends Extension
{
    /** @var string[] */
    private static $namespaces = [
        'root_namespace',
        'command_namespace',
        'controller_namespace',
        'entity_namespace',
        'fixtures_namespace',
        'form_namespace',
        'functional_test_namespace',
        'repository_namespace',
        'security_namespace',
        'serializer_namespace',
        'subscriber_namespace',
        'twig_namespace',
        'unit_test_namespace',
        'validator_namespace'
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('makers.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $namespacesHelperDefinition = $container->getDefinition('maker.namespaces_helper');

        foreach (static::$namespaces as $index => $namespace) {
            $namespacesHelperDefinition->replaceArgument($index, \trim($config[$namespace], '\\'));
        }

        $container->registerForAutoconfiguration(MakerInterface::class)
            ->addTag(MakeCommandRegistrationPass::MAKER_TAG);
    }
}
