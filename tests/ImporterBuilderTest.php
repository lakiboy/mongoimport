<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\ImporterBuilder;

class ImporterBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_builds_importer()
    {
        $importer = (new ImporterBuilder())
            ->setHost('test')
            ->setPort(28017)
            ->setDefaultDatabase('default_db')
            ->setDrop(true)
            ->getImporter()
        ;

        $this->assertInstanceOf('Devmachine\MongoImport\Importer', $importer);

        // Test connection was set.
        $this->assertEquals('mongodb://test:28017', $this->readAttribute($importer, 'mongo')->getServer());

        // Check options.
        $this->assertTrue($this->readAttribute($importer, 'defaultOptions')['drop']);

        // Check default database was set.
        $this->assertEquals('default_db', $this->readAttribute($importer, 'defaultDatabase'));

        // Check default loader is registered.
        $this->assertInstanceOf('Devmachine\MongoImport\Loader\JsonLoader', $this->readAttribute($importer, 'loaders')['json']);
    }
}
