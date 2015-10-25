<?php

namespace Doctrine\MongoDB\Importer\Exception;

class InvalidImportDataException extends \RuntimeException
{
    /**
     * @param int    $code
     * @param string $cause
     *
     * @return static
     */
    public static function fromJson($code, $cause)
    {
        return new static(sprintf('Unable to decode json: %s', $cause), $code);
    }
}
