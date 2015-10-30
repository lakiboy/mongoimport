<?php

namespace Devmachine\MongoImport\Tests\Integration;

use Devmachine\MongoImport\Importer;
use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

/**
 * @group integration
 */
class ImporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private static $mongo;

    public static function setUpBeforeClass()
    {
        $server = null;

        // Check if running in Docker.
        if (($host = getenv('MONGO_PORT_27017_TCP_ADDR')) && ($port = getenv('MONGO_PORT_27017_TCP_PORT'))) {
            $server = sprintf('%s:%d', $host, $port);
        }

        static::$mongo = new Connection($server);
        static::$mongo->dropDatabase('test');
    }

    public static function tearDownAfterClass()
    {
        if (static::$mongo->isConnected()) {
            static::$mongo->close();
            static::$mongo = null;
        }
    }

    /**
     * @test
     */
    public function it_imports_fixtures()
    {
        $importer = new Importer(static::$mongo, 'test', [new JsonLoader()]);

        $result = $importer->importCollection(__DIR__.'/../fixtures/employees.json');
        $this->assertSame(10, $result);
        $this->assertEquals(10, static::employees()->count());

        $result = $importer->importCollection(__DIR__.'/../fixtures/offices.json');
        $this->assertSame(3, $result);
        $this->assertEquals(3, static::offices()->count());

        $employee = self::employees()->findOne();

        $this->assertInstanceOf('MongoId', $employee['_id']);
        $this->assertEquals('562e84e58d20d401008b456a', $employee['_id']);
        $this->assertSame(41, $employee['age']);
        $this->assertInstanceOf('MongoBinData', $employee['avatar']);
        $this->assertEquals('Llewellyn', $employee['first_name']);
        $this->assertEquals('Christiansen', $employee['last_name']);
        $this->assertInstanceOf('MongoDate', $employee['signed_at']);
        $this->assertInternalType('array', $employee['office']);

        $office = self::employees()->getDBRef($employee['office']);

        $this->assertInstanceOf('MongoId', $office['_id']);
        $this->assertEquals('562e84e58d20d401008b4567', $office['_id']);
        $this->assertEquals('1948 Gutmann Parkway', $office['address']);
        $this->assertEquals('Greentown', $office['city']);
        $this->assertEquals('Sri Lanka', $office['country']);
        $this->assertInternalType('array', $office['phones']);

        $this->assertEquals('1-940-002-8852x48648', $office['phones'][0]['number']);
        $this->assertEquals('home', $office['phones'][0]['type']);
        $this->assertInstanceOf('MongoRegex', $office['phones'][0]['validation']);
        $this->assertEquals('^+371 67', $office['phones'][0]['validation']->regex);
        $this->assertEquals('^+371 2', $office['phones'][1]['validation']->regex);
    }

    /**
     * @return \Doctrine\MongoDB\Collection
     */
    private static function employees()
    {
        return static::$mongo->selectCollection('test', 'employees');
    }

    /**
     * @return \Doctrine\MongoDB\Collection
     */
    private static function offices()
    {
        return static::$mongo->selectCollection('test', 'offices');
    }
}
