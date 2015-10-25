<?php

namespace Doctrine\MongoDB\Importer\Loader;

use Doctrine\MongoDB\Importer\Exception\InvalidImportDataException;

class JsonLoader extends Loader
{
    private $asArray;

    public function __construct($asArray = true)
    {
        $this->asArray = $asArray;
    }

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
        $contents = static::fixJson($contents);

        if (null === $data = json_decode($contents, $this->asArray)) {
            throw InvalidImportDataException::fromJson(
                json_last_error(),
                json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * @param string $contents
     *
     * @return string
     */
    private static function fixJson($contents)
    {
        return '['.preg_replace('/\}$\n\{/m', '},{', $contents).']';
    }
}
