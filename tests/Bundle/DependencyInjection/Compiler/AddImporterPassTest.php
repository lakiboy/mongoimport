<?php

namespace Devmachine\MongoImport\Tests\Bundle\DependencyInjection\Compiler;

use Devmachine\MongoImport\Bundle\DependencyInjection\Compiler\AddImportersPass;
use Devmachine\MongoImport\Bundle\DependencyInjection\DevmachineMongoImportExtension;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\DoctrineMongoDBExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group bundle
 */
class AddImporterPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddImportersPass());
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
            'default_database' => 'default_db',
            'document_managers' => [
                'foo' => [],
                'bar' => [],
            ],
        ];

        (new DoctrineMongoDBExtension())->load([$config], $this->container);
        (new DevmachineMongoImportExtension())->load([], $this->container);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'devmachine_mongoimport.foo',
            'devmachine_mongoimport.importer.base'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.foo',
            0,
            new Reference('doctrine_mongodb.odm.foo_document_manager')
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.foo',
            1,
            'default_db'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            'devmachine_mongoimport.bar',
            'devmachine_mongoimport.importer.base'
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.bar',
            0,
            new Reference('doctrine_mongodb.odm.bar_document_manager')
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'devmachine_mongoimport.bar',
            1,
            'default_db'
        );

        $this->assertContainerBuilderHasAlias('devmachine_mongoimport', 'devmachine_mongoimport.foo');
    }
}
