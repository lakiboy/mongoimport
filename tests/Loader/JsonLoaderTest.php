<?php

namespace Devmachine\MongoImport\Tests\Loader;

use Devmachine\MongoImport\Loader\JsonLoader;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Devmachine\MongoImport\Exception\FileNotFoundException
     * @expectedExceptionMessage File "__missing__" not found.
     */
    public function it_throws_exception_on_missing_file()
    {
        $loader = new JsonLoader();
        $loader->loadFile('__missing__');
    }

    /**
     * @expectedException \Devmachine\MongoImport\Exception\InvalidImportDataException
     * @expectedExceptionCode 4
     * @expectedExceptionMessage Unable to decode json: Syntax error
     */
    public function it_throws_exception_on_invalid_json()
    {
        $loader = new JsonLoader();
        $loader->loadFile(dirname(__DIR__).'/fixtures/invalid.json');
    }

    /**
     * @test
     *
     * @dataProvider getFiles
     *
     * @param string $fileName
     * @param int    $count
     */
    public function it_loads_export_file_as_array($fileName, $count)
    {
        $loader = new JsonLoader();
        $data = $loader->loadFile(dirname(__DIR__).'/fixtures/'.$fileName);

        $this->assertInternalType('array', $data);
        $this->assertInternalType('array', $data[0]);
        $this->assertCount($count, $data);
    }

    public function getFiles()
    {
        return [
            ['movies.pretty.json', 5],
            ['movies.json', 8],
        ];
    }
}
