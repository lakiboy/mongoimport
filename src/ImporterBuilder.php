<?php

namespace Devmachine\MongoImport;

use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

class ImporterBuilder
{
    private $port;
    private $host;
    private $defaultDatabase;
    private $drop = false;

    /**
     * @param int $port
     *
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $defaultDatabase
     *
     * @return self
     */
    public function setDefaultDatabase($defaultDatabase)
    {
        $this->defaultDatabase = $defaultDatabase;

        return $this;
    }

    /**
     * @param bool $drop
     *
     * @return self
     */
    public function setDrop($drop)
    {
        $this->drop = $drop;

        return $this;
    }

    /**
     * @return Importer
     */
    public function getImporter()
    {
        $mongo = new Connection(sprintf('mongodb://%s:%s', $this->host, $this->port));

        return new Importer($mongo, $this->defaultDatabase, [new JsonLoader()], ['drop' => $this->drop]);
    }
}
