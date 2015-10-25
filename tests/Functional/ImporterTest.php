<?php

namespace Doctrine\MongoDB\Importer\Tests\Functional;

use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Importer\Importer;
use Doctrine\MongoDB\Importer\Loader\JsonLoader;

/**
 * @group functional
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
            $server = sprintf('mongodb://%s:%d', $host, $port);
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
        $result = $importer->importCollection('test', 'movies', __DIR__.'/../fixtures/movies.json');
        $this->assertTrue($result);
    }
}
