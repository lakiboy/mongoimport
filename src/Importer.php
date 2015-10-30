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
     * @var string
     */
    private $defaultDatabase;

    /**
     * @var Loader[]
     */
    private $loaders = [];

    /**
     * @var array
     */
    private $defaultOptions = [
        'drop' => false,
    ];

    public function __construct(Connection $mongo, $defaultDatabase, array $loaders, array $defaultOptions = [])
    {
        $this->mongo = $mongo;
        $this->defaultDatabase = $defaultDatabase;
        $this->defaultOptions = array_replace($this->defaultOptions, $defaultOptions);
        array_map([$this, 'addLoader'], $loaders);
    }

    /**
     * @param string       $filePath
     * @param string|array $name
     * @param string|array $db
     * @param array        $options
     *
     * @return int
     *
     * @throws UnsupportedFileFormatException
     * @throws ImportException
     */
    public function importCollection($filePath, $name = null, $db = null, array $options = [])
    {
        $data = $this->importFile($filePath);

        if ($name === null || is_array($name)) {
            $name = $this->getCollectionName($filePath);
        }
        if ($db === null || is_array($db)) {
            $db = $this->defaultDatabase;
        }
        if (is_array($name)) {
            $options = $name;
        } elseif (is_array($db)) {
            $options = $db;
        }

        $options = array_replace($this->defaultOptions, $options);

        $col = $this->mongo->selectCollection($db, $name);

        if ($options['drop']) {
            $col->drop();
        }

        $result = $col->batchInsert($data, ['safe' => 1]);

        if ($result['err']) {
            throw ImportException::fromServerError($result['err']);
        }

        return count($data);
    }

    /**
     * @param Loader $loader
     */
    private function addLoader(Loader $loader)
    {
        $this->loaders[$loader->getName()] = $loader;
    }

    /**
     * @param string $filePath
     *
     * @return array
     *
     * @throws UnsupportedFileFormatException
     */
    private function importFile($filePath)
    {
        $format = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!isset($this->loaders[$format])) {
            throw UnsupportedFileFormatException::forFormat($format);
        }

        $data = $this->loaders[$format]->loadFile($filePath);

        return array_map('Devmachine\MongoImport\ExtendedJson::fromStrict', $data);
    }

    /**
     * @param string $filePath
     *
     * @return mixed|string
     */
    private function getCollectionName($filePath)
    {
        $basename = pathinfo($filePath, PATHINFO_BASENAME);

        if (false === ($pos = strrpos($basename, '.'))) {
            return $basename;
        }

        return substr($basename, 0, $pos);
    }
}
