<?php

namespace Devmachine\MongoImport\Tests\Bundle\DependencyInjection\Compiler;

use Devmachine\MongoImport\Bundle\DependencyInjection\Compiler\AddImporterPass;
use Devmachine\MongoImport\Bundle\DependencyInjection\DevmachineMongoImportExtension;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\DoctrineMongoDBExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group bundle
 */
class AddImporterPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddImporterPass());
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.environment', 'test');
        $container->setParameter('kernel.root_dir', __DIR__);
        $container->setParameter('kernel.cache_dir', __DIR__.'/tmp');
    }

    /**
     * @test
     */
    public function it_adds_importers()
    {
        $config = [
            'connections' => [
                'foo' => [
                    'server' => 'mongodb://foo:27017',
                ],
                'bar' => [
                    'server' => 'mongodb://bar:27017',
                ],
            ],
            'default_database' => 'baz',
            'document_managers' => [
                'foo' => [],
                'bar' => [],
            ],
        ];

        (new DoctrineMongoDBExtension())->load([$config], $this->container);
        (new DevmachineMongoImportExtension())->load([], $this->container);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.importer.factory',
            2,
            'baz'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'devmachine_mongoimport.foo',
            'devmachine_mongoimport.importer'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.foo',
            'foo'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'devmachine_mongoimport.bar',
            'devmachine_mongoimport.importer'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.bar',
            'bar'
        );

        $this->assertContainerBuilderHasAlias('devmachine_mongoimport', 'devmachine_mongoimport.foo');
    }
}
