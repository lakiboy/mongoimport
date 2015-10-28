<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\Importer;
use Devmachine\MongoImport\ImporterBuilder;

class ImporterBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_builds_importer()
    {
        $builder = new ImporterBuilder();
        $importer = $builder->setHost('test')->setPort(28017)->setDrop(true)->getImporter();

        $this->assertInstanceOf('Devmachine\MongoImport\Importer', $importer);

        $this->assertEquals('mongodb://test:28017', $this->getProperty($importer, 'mongo')->getServer());
        $this->assertTrue($this->getProperty($importer, 'drop'));
    }

    /**
     * Use this hack to avoid exposing stage of Importer.
     *
     * @param Importer $importer
     * @param string   $name
     *
     * @return mixed
     */
    private function getProperty(Importer $importer, $name)
    {
        $ro = new \ReflectionObject($importer);

        $rp = $ro->getProperty($name);
        $rp->setAccessible(true);

        return $rp->getValue($importer);
    }
}
