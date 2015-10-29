<?php

namespace Devmachine\MongoImport;

use Devmachine\MongoImport\Exception\ImportException;
use Devmachine\MongoImport\Exception\UnsupportedFileFormatException;
use Devmachine\MongoImport\Loader\Loader;
use Doctrine\MongoDB\Connection;

class Importer
{
    private $mongo;

    /**
     * @var Loader[]
     */
    private $loaders = [];

    private $defaultDatabase;

    private $drop = false;

    public function __construct(Connection $mongo, array $loaders = [])
    {
        $this->mongo = $mongo;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * @param Loader $loader
     */
    public function addLoader(Loader $loader)
    {
        $this->loaders[$loader->getName()] = $loader;
    }

    /**
     * @return Loader[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param string $defaultDatabase
     */
    public function setDefaultDatabase($defaultDatabase)
    {
        $this->defaultDatabase = $defaultDatabase;
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
     * @param string $filePath
     * @param string $name
     * @param string $db
     *
     * @return int
     *
     * @throws UnsupportedFileFormatException
     * @throws ImportException
     */
    public function importCollection($filePath, $name, $db = null)
    {
        $format = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!isset($this->loaders[$format])) {
            throw UnsupportedFileFormatException::forFormat($format);
        }

        $data = $this->loaders[$format]->loadFile($filePath);
        $data = array_map('Devmachine\MongoImport\ExtendedJson::fromStrict', $data);

        $col = $this->mongo->selectCollection($db ?: $this->defaultDatabase, $name);

        if ($this->drop) {
            $col->drop();
        }

        $result = $col->batchInsert($data, ['safe' => 1]);

        if ($result['err']) {
            throw ImportException::fromServerError($result['err']);
        }

        return count($data);
    }
}
