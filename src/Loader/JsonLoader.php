<?php

namespace Devmachine\MongoImport\Loader;

use Devmachine\MongoImport\Exception\InvalidImportDataException;

class JsonLoader extends Loader
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'json';
    }

    /**
     * @param string $contents
     *
     * @return array
     */
    protected function load($contents)
    {
        if (null === $data = json_decode($contents, true)) {
            throw InvalidImportDataException::fromError(json_last_error());
        }

        return $data;
    }
}
