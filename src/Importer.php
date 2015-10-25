<?php

namespace Doctrine\MongoDB\Importer;

use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Importer\Exception\UnsupportedFileFormatException;
use Doctrine\MongoDB\Importer\Loader\Loader;

class Importer
{
    private $mongo;

    /**
     * @var Loader[]
     */
    private $loaders = [];

    public function __construct(Connection $mongo, array $loaders = [])
    {
        $this->mongo = $mongo;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

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
     * @param string $db
     * @param string $name
     * @param string $filePath
     *
     * @return int
     *
     * @throws UnsupportedFileFormatException
     */
    public function importCollection($db, $name, $filePath)
    {
        $format = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!isset($this->loaders[$format])) {
            throw UnsupportedFileFormatException::forFormat($format);
        }

        $data = $this->loaders[$format]->loadFile($filePath);

        $docs = $this->mongo->selectCollection($db, $name)->batchInsert($data, [
            'safe' => 1,
        ]);

        return count($docs);
    }
}
