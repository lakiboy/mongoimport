<?php

namespace Devmachine\MongoImport\Tests\Bundle\DependencyInjection\Compiler;

use Devmachine\MongoImport\Bundle\DependencyInjection\Compiler\AddImporterPass;
use Devmachine\MongoImport\Bundle\DependencyInjection\MongoImportExtension;
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
        $container->setParameter('kernel.cache_dir', __DIR__ . '/tmp');
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
        ];

        (new DoctrineMongoDBExtension())->load([$config], $this->container);
        (new MongoImportExtension())->load([], $this->container);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithParent('mongoimport.importer.foo', 'mongoimport.importer');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('mongoimport.importer.foo', 'foo');

        $this->assertContainerBuilderHasServiceDefinitionWithParent('mongoimport.importer.bar', 'mongoimport.importer');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('mongoimport.importer.bar', 'bar');
    }
}
