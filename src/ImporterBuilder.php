<?php

namespace Devmachine\MongoImport;

use Devmachine\MongoImport\Loader\JsonLoader;
use Doctrine\MongoDB\Connection;

class ImporterBuilder
{
    private $port;
    private $host;
    private $drop = false;

    /**
     * @param string $port
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

        $importer = new Importer($mongo);
        $importer->addLoader(new JsonLoader());
        $importer->setDrop($this->drop);

        return $importer;
    }
}
