<?php

namespace Devmachine\MongoImport;

use Assert\Assertion;

/**
 * https://docs.mongodb.org/manual/reference/mongodb-extended-json/.
 */
final class ExtendedJson
{
    /**
     * @param array $data
     *
     * @return \MongoId
     */
    public static function toObjectId(array $data)
    {
        Assertion::keyExists($data, '$oid');

        return new \MongoId($data['$oid']);
    }

    /**
     * @param array $data
     *
     * @return \MongoDate
     */
    public static function toDate(array $data)
    {
        Assertion::keyExists($data, '$date');

        $datetime = new \DateTime($data['$date']);

        return new \MongoDate($datetime->format('U'), $datetime->format('u'));
    }

    /**
     * @param array $data
     *
     * @return \MongoRegex
     */
    public static function toRegex(array $data)
    {
        Assertion::keyExists($data, '$regex');
        Assertion::keyExists($data, '$options');

        return new \MongoRegex('/'.$data['$regex'].'/'.$data['$options']);
    }

    /**
     * @param array $data
     *
     * @return \MongoBinData
     */
    public static function toBinData(array $data)
    {
        Assertion::keyExists($data, '$binary');
        Assertion::keyExists($data, '$type');

        return new \MongoBinData(
            base64_decode($data['$binary']),
            hexdec($data['$type'])
        );
    }

    /**
     * @param array $data
     *
     * @return int|\MongoInt64
     */
    public static function toLong(array $data)
    {
        Assertion::keyExists($data, '$numberLong');

        if (PHP_INT_SIZE === 8) {
            return (int) $data['$numberLong'];
        }

        return new \MongoInt64($data['$numberLong']);
    }

    /**
     * @param array $data
     *
     * @return \MongoDBRef
     */
    public static function toDBRef(array $data)
    {
        Assertion::keyExists($data, '$ref');
        Assertion::keyExists($data, '$id');

        $db = isset($data['$db']) ? $data['$db'] : null;

        return new \MongoDBRef($data['$ref'], $data['$id'], $db);
    }

    /**
     * @param array $data
     *
     * @return \MongoMinKey
     */
    public static function toMinKey(array $data)
    {
        Assertion::keyExists($data, '$minKey');
        Assertion::same($data['$minKey'], 1);

        return new \MongoMinKey();
    }

    /**
     * @param array $data
     *
     * @return \MongoMaxKey
     */
    public static function toMaxKey(array $data)
    {
        Assertion::keyExists($data, '$maxKey');
        Assertion::same($data['$maxKey'], 1);

        return new \MongoMaxKey();
    }

    /**
     * @param array $data
     *
     * @return \MongoTimestamp
     */
    public static function toTimestamp(array $data)
    {
        Assertion::keyExists($data, '$timestamp');
        Assertion::keyExists($data['$timestamp'], 't');
        Assertion::keyExists($data['$timestamp'], 'i');

        return new \MongoTimestamp($data['$timestamp']['t'], $data['$timestamp']['i']);
    }

    /**
     * @param array $data
     */
    public static function toUndefined(array $data)
    {
        Assertion::keyExists($data, '$undefined');
        Assertion::true($data['$undefined']);

        return;
    }

    /**
     * @todo Implement this.
     *
     * @param array $doc
     *
     * @return array
     */
    public static function fromStrict(array $doc)
    {
        return $doc;
    }

    private function __construct()
    {
    }
}
