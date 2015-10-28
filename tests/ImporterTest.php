<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\Importer;
use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

class ImporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_registers_loaders()
    {
        $importer = new Importer(new Connection());
        $importer->addLoader($loader = new JsonLoader());

        $loaders = $importer->getLoaders();

        $this->assertArrayHasKey('json', $loaders);
        $this->assertSame($loader, $loaders['json']);
    }

    /**
     * @test
     *
     * @expectedException \Devmachine\MongoImport\Exception\UnsupportedFileFormatException
     * @expectedExceptionMessage File of format "txt" is not supported.
     */
    public function it_throws_exception_on_unsupported_file_format()
    {
        $importer = new Importer(new Connection());
        $importer->importCollection('foo', 'bar', 'file.txt');
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
        ]);

        $importer = new Importer($mongo, [new JsonLoader()]);
        $importer->importCollection('foo', 'bar', __DIR__.'/fixtures/offices.json');
    }

    /**
     * @test
     */
    public function it_imports_fixtures()
    {
        $mongo = $this->getMongoStubWithResult([
            'ok' => 1,
            'err' => null,
        ]);

        $importer = new Importer($mongo, [new JsonLoader()]);
        $result = $importer->importCollection('foo', 'bar', __DIR__.'/fixtures/employees.json');

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function it_drops_db_and_imports_fixtures()
    {
        $mongo = $this->getMongoStubWithResult(['ok' => 1, 'err' => null], true);

        $importer = new Importer($mongo, [new JsonLoader()]);
        $result = $importer->importCollection('foo', 'bar', __DIR__.'/fixtures/employees.json');

        $this->assertSame(10, $result);
    }

    /**
     * @param array $result
     * @param bool  $drop
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMongoStubWithResult(array $result, $drop = false)
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
            ->with($this->equalTo('foo'), $this->equalTo('bar'))
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
