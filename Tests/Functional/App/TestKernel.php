<?php

namespace Overblog\GraphQLBundle\Tests\Functional\App;

use Overblog\GraphQLBundle\OverblogGraphQLBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel implements CompilerPassInterface
{
    private $testCase;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new OverblogGraphQLBundle(),
        ];
    }

    public function __construct($environment, $debug, $testCase = null)
    {
        $this->testCase = null !== $testCase ? $testCase : false;
        parent::__construct($environment, $debug);
    }

    public function getCacheDir()
    {
        return $this->basePath().'cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->basePath().'logs';
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if ($this->testCase) {
            $loader->load(sprintf(__DIR__.'/config/%s/config.yml', $this->testCase));
        } else {
            $loader->load(__DIR__.'/config/config.yml');
        }

        $loader->load(function (ContainerBuilder $container) {
            $container->addCompilerPass($this);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // disabled http_exception_listener because it flatten exception to html response
        if ($container->has('http_exception_listener')) {
            $container->removeDefinition('http_exception_listener');
        }
    }

    private function basePath()
    {
        return sys_get_temp_dir().'/OverblogGraphQLBundle/'.Kernel::VERSION.'/'.($this->testCase ? $this->testCase.'/' : '');
    }
}
