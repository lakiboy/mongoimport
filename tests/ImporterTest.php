<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\Importer;
use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

class ImporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @expectedException \Devmachine\MongoImport\Exception\UnsupportedFileFormatException
     * @expectedExceptionMessage File of format "txt" is not supported.
     */
    public function it_throws_exception_on_unsupported_file_format()
    {
        $importer = new Importer(new Connection(), 'test_db', [new JsonLoader()]);
        $importer->importCollection('file.txt');
    }

    /**
     * @test
     *
     * @expectedException \Devmachine\MongoImport\Exception\ImportException
     * @expectedExceptionMessage Unable to import data: Something went wrong
     */
    public function it_throws_exception_on_import_error()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 0,
            'err' => 'Something went wrong',
        ], 'offices', 'test_db');

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $importer->importCollection(__DIR__.'/fixtures/offices.json');
    }

    /**
     * @test
     */
    public function it_imports_fixtures_into_default_db_and_collection()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ], 'employees', 'test_db');

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $result = $importer->importCollection(__DIR__.'/fixtures/employees.json');

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function it_imports_fixtures_with_specified_db_and_collection()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ], 'personnel', 'company');

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $result = $importer->importCollection(__DIR__.'/fixtures/employees.json', 'personnel', 'company');

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function it_drops_db_prior_to_import_into_default_db_and_collection()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ], 'employees', 'test_db', true);

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $result = $importer->importCollection(__DIR__.'/fixtures/employees.json', ['drop' => true]);

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function it_drops_db_prior_to_import_info_default_db()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ], 'personnel', 'test_db', true);

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $result = $importer->importCollection(__DIR__.'/fixtures/employees.json', 'personnel', ['drop' => true]);

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function it_drops_db_prior_to_import()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ], 'personnel', 'company', true);

        $importer = new Importer($mongo, 'test_db', [new JsonLoader()]);
        $result = $importer->importCollection(__DIR__.'/fixtures/employees.json', 'personnel', 'company', ['drop' => true]);

        $this->assertSame(10, $result);
    }

    /**
     * @param array  $result
     * @param string $collectionName
     * @param string $dbName
     * @param bool   $drop
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMongoStubWithResult(array $result, $collectionName, $dbName, $drop = false)
    {
        $collection = $this->getMockBuilder('Doctrine\MongoDB\Collection')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $collection
            ->method('batchInsert')
            ->willReturn($result)
        ;
        $mongo = $this->getMockBuilder('Doctrine\MongoDB\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $mongo
            ->method('selectCollection')
            ->with($this->equalTo($dbName), $this->equalTo($collectionName))
            ->willReturn($collection)
        ;

        if ($drop) {
            $collection->method('drop');
        } else {
            $collection->expects($this->never())->method('drop');
        }

        return $mongo;
    }
}
