<?php

namespace Devmachine\MongoImport\Tests;

use Devmachine\MongoImport\ExtendedJson;

class ExtendedJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_to_mongo_id()
    {
        $mongoId = ExtendedJson::toObjectId(['$oid' => '562e58130b9df301008b4567']);

        $this->assertInstanceOf('MongoId', $mongoId);
        $this->assertEquals('562e58130b9df301008b4567', (string) $mongoId);
    }

    /**
     * @test
     *
     * @dataProvider getDateData
     *
     * @param string $string
     * @param int    $sec
     * @param int    $usec
     */
    public function it_converts_to_date($string, $sec, $usec)
    {
        $date = ExtendedJson::toDate(['$date' => $string]);

        $this->assertInstanceOf('MongoDate', $date);
        $this->assertEquals($sec, $date->sec);
        $this->assertEquals($usec, $date->usec);
    }

    /**
     * @test
     */
    public function it_converts_to_regex()
    {
        $regex = ExtendedJson::toRegex(['$regex' => '^(foo|bar)$', '$options' => 'imxsu']);

        $this->assertInstanceOf('MongoRegex', $regex);
        $this->assertSame('^(foo|bar)$', $regex->regex);
        $this->assertSame('imxsu', $regex->flags);
    }

    /**
     * @test
     */
    public function it_converts_to_bin_data()
    {
        $binData = ExtendedJson::toBinData([
           '$binary' => 'Zm9v',
           '$type' => '05',
        ]);

        $this->assertInstanceOf('MongoBinData', $binData);
        $this->assertSame('foo', $binData->bin);
        $this->assertSame(\MongoBinData::MD5, $binData->type);
    }

    /**
     * @test
     */
    public function it_converts_to_long()
    {
        if (PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('PHP version expected is 64 bit.');
        }

        $long = ExtendedJson::toLong(['$numberLong' => '12345']);

        $this->assertInternalType('integer', $long);
        $this->assertSame(12345, $long);
    }

    /**
     * @test
     */
    public function it_converts_to_db_ref()
    {
        $ref = ExtendedJson::toDBRef(['$ref' => 'items', '$id' => '562e74680eabe601008b4567']);
        $this->assertInstanceOf('MongoDBRef', $ref);

        $ref = ExtendedJson::toDBRef(['$ref' => 'items', '$id' => '562e74680eabe601008b4567', '$db' => 'test']);
        $this->assertInstanceOf('MongoDBRef', $ref);
    }

    /**
     * @test
     */
    public function it_converts_to_min_key()
    {
        $minKey = ExtendedJson::toMinKey(['$minKey' => 1]);

        $this->assertInstanceOf('MongoMinKey', $minKey);
    }

    /**
     * @test
     */
    public function it_converts_to_max_key()
    {
        $maxKey = ExtendedJson::toMaxKey(['$maxKey' => 1]);

        $this->assertInstanceOf('MongoMaxKey', $maxKey);
    }

    /**
     * @test
     */
    public function it_converts_to_timestamp()
    {
        $timestamp = ExtendedJson::toTimestamp(['$timestamp' => [
            't' => '1445886106',
            'i' => 1,
        ]]);

        $this->assertInstanceOf('MongoTimestamp', $timestamp);
        $this->assertSame(1445886106, $timestamp->sec);
        $this->assertSame(1, $timestamp->inc);
    }

    /**
     * @test
     */
    public function it_converts_to_undefined()
    {
        $undefined = ExtendedJson::toUndefined(['$undefined' => true]);

        $this->assertNull($undefined);
    }

    /**
     * @return array
     */
    public function getDateData()
    {
        return [
            ['2015-10-25T16:18:49.000Z', 1445789929, 0],
            ['2015-10-26T18:19:24.123Z', 1445883564, 123000],
        ];
    }
}
