<?php

namespace Devmachine\MongoImport\Tests\Bundle;
use Devmachine\MongoImport\Bundle\ImporterFactory;
use Devmachine\MongoImport\Loader\CsvLoader;
use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

/**
 * @group bundle
 */
class ImporterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_retrieves_importer()
    {
        // Create stub.
        $mongo = $this->getMock('Doctrine\Common\Persistence\ConnectionRegistry');

        $factory = new ImporterFactory($mongo, [
            new JsonLoader(),
            new CsvLoader(),
        ]);

        $mongo
            ->method('getConnection')
            ->with($this->equalTo('foo'))
            ->willReturn(new Connection())
        ;

        $importer = $factory->getImporter('foo');

        $this->assertInstanceOf('Devmachine\MongoImport\Importer', $importer);
    }
}
