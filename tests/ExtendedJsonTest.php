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
    public function it_converts_to_valid_ref()
    {
        $ref = ExtendedJson::toRef([
            '$ref' => 'items',
            '$id' => [
                '$oid' => '562e74680eabe601008b4567',
            ],
        ]);

        $this->assertInternalType('array', $ref);
        $this->assertInstanceOf('MongoId', $ref['$id']);
        $this->assertEquals('items', $ref['$ref']);

        $ref = ExtendedJson::toRef(['$ref' => 'items', '$id' => '562e74680eabe601008b4567', '$db' => 'test']);

        $this->assertEquals('562e74680eabe601008b4567', $ref['$id']);
        $this->assertEquals('items', $ref['$ref']);
        $this->assertEquals('test', $ref['$db']);
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
     * @test
     */
    public function it_converts_from_strict()
    {
        $data = [
            '_id' => [
                '$oid' => '562e84e58d20d401008b4573',
            ],
            'age' => [
                '$numberLong' => '23',
            ],
            'avatar' => [
                '$binary' => 'X19kYXRhX18=',
                '$type' => '00',
            ],
            'first_name' => 'Kameron',
            'last_name' => 'Weber',
            'office' => [
                '$id' => [
                    '$oid' => '562e84e58d20d401008b4569',
                ],
                '$ref' => 'offices',
            ],
            'signed_at' => [
                '$date' => '2007-10-12T03:24:56.000Z',
            ],
            'nothing' => [
                '$undefined' => true,
            ],
            'validations' => [
                'home' => [
                    '$regex' => '^+371 67',
                    '$options' => '',
                ],
                'mobile' => [
                    '$regex' => '^+371 2',
                    '$options' => '',
                ],
            ],
        ];

        $doc = ExtendedJson::fromStrict($data);

        $this->assertInstanceOf('MongoId', $doc['_id']);
        $this->assertSame(23, $doc['age']);
        $this->assertInstanceOf('MongoBinData', $doc['avatar']);
        $this->assertEquals('Kameron', $doc['first_name']);
        $this->assertEquals('Weber', $doc['last_name']);
        $this->assertInternalType('array', $doc['office']);
        $this->assertInstanceOf('MongoDate', $doc['signed_at']);
        $this->assertArrayHasKey('nothing', $doc);
        $this->assertNull($doc['nothing']);
        $this->assertInstanceOf('MongoRegex', $doc['validations']['home']);
        $this->assertInstanceOf('MongoRegex', $doc['validations']['mobile']);
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
