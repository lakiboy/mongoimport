<?php

namespace Devmachine\MongoImport\Loader;

/**
 * @codeCoverageIgnore
 */
class CsvLoader extends Loader
{
    public function getName()
    {
        return 'csv';
    }

    protected function load($contents)
    {
        throw new \RuntimeException(__METHOD__.' not implemented');
    }
}
