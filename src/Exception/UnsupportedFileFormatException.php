<?php

namespace Devmachine\MongoImport\Exception;

class UnsupportedFileFormatException extends \RuntimeException
{
    public static function forFormat($format)
    {
        return new static(sprintf('File of format "%s" is not supported.', $format));
    }
}
