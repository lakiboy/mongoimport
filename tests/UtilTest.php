<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_replaces_uid_with_mongo_id()
    {
        $doc = [
            '_id' => ['$oid' => '562d00e89ca450b5018b4567'],
            'foo' => 'bar',
            'baz' => [
                'foo' => 'bar',
                'baz' => ['$oid' => '562d00e99ca450b5018b4568'],
                'qux' => [
                    'baz' => 'bar',
                    'foo' => ['$oid' => '562d00ea9ca450b5018b4569'],
                    'bar' => 'baz',
                ],
            ],
        ];

        $result = Util::replaceMongoIds($doc);

        $this->assertInstanceOf('MongoId', $result['_id']);
        $this->assertInstanceOf('MongoId', $result['baz']['baz']);
        $this->assertInstanceOf('MongoId', $result['baz']['qux']['foo']);

        $this->assertEquals('562d00e89ca450b5018b4567', $result['_id']);
        $this->assertEquals('562d00e99ca450b5018b4568', $result['baz']['baz']);
        $this->assertEquals('562d00ea9ca450b5018b4569', $result['baz']['qux']['foo']);
    }
}
