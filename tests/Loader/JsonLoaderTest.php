<?php

namespace Doctrine\MongoDB\Importer\Tests\Loader;

use Doctrine\MongoDB\Importer\Loader\JsonLoader;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Doctrine\MongoDB\Importer\Exception\FileNotFoundException
     * @expectedExceptionMessage File "__missing__" not found.
     */
    public function it_throws_exception_on_missing_file()
    {
        $loader = new JsonLoader();
        $loader->loadFile('__missing__');
    }

    /**
     * @expectedException \Doctrine\MongoDB\Importer\Exception\InvalidImportDataException
     * @expectedExceptionCode 4
     * @expectedExceptionMessage Unable to decode json: Syntax error
     */
    public function it_throws_exception_on_invalid_json()
    {
        $loader = new JsonLoader();
        $loader->loadFile(__DIR__.'/fixtures/invalid.json');
    }

    /**
     * @test
     *
     * @dataProvider getFiles
     *
     * @param string $fileName
     * @param int    $count
     */
    public function it_reads_export_file_as_array($fileName, $count)
    {
        $loader = new JsonLoader();
        $data = $loader->loadFile(__DIR__.'/fixtures/'.$fileName);

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
