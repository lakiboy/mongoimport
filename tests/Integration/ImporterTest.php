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
        $importer = new Importer(static::$mongo, [new JsonLoader()]);

        $result = $importer->importCollection('test', 'employees', __DIR__.'/../fixtures/employees.json');
        $this->assertTrue($result);

        $result = $importer->importCollection('test', 'offices', __DIR__.'/../fixtures/offices.json');
        $this->assertTrue($result);
    }
}
