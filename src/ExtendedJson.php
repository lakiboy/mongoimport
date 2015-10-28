<?php

namespace Devmachine\MongoImport;

use Assert\Assertion;

/**
 * https://docs.mongodb.org/manual/reference/mongodb-extended-json/.
 */
final class ExtendedJson
{
    private static $ids = ['$oid', '$binary', '$id', '$date', '$regex', '$numberLong', '$timestamp', '$undefined', '$minKey', '$maxKey'];

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
     * @return array
     */
    public static function toRef(array $data)
    {
        Assertion::keyExists($data, '$ref');
        Assertion::keyExists($data, '$id');

        $id = is_scalar($data['$id']) ? $data['$id'] : self::fromStrict($data['$id']);

        if (!empty($data['$db'])) {
            return \MongoDBRef::create($data['$ref'], $id, $data['$db']);
        }

        return \MongoDBRef::create($data['$ref'], $id);
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
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public static function toPhpValue(array $data)
    {
        if (isset($data['$oid'])) {
            $result = self::toObjectId($data);
        } elseif (isset($data['$binary'])) {
            $result = self::toBinData($data);
        } elseif (isset($data['$id']) && isset($data['$ref'])) {
            $result = self::toRef($data);
        } elseif (isset($data['$date'])) {
            $result = self::toDate($data);
        } elseif (isset($data['$regex'])) {
            $result = self::toRegex($data);
        } elseif (isset($data['$numberLong'])) {
            $result = self::toLong($data);
        } elseif (isset($data['$timestamp'])) {
            $result = self::toTimestamp($data);
        } elseif (isset($data['$undefined'])) {
            $result = self::toUndefined($data);
        } elseif (isset($data['$minKey'])) {
            $result = self::toMinKey($data);
        } elseif (isset($data['$maxKey'])) {
            $result = self::toMaxKey($data);
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * @param array $doc
     *
     * @return array
     */
    public static function fromStrict(array $doc)
    {
        $key = key($doc);

        if (in_array($key, self::$ids, true)) {
            return self::toPhpValue($doc);
        }

        foreach ($doc as $key => $val) {
            if (is_array($val)) {
                $doc[$key] = self::fromStrict($val);
            }
        }

        return $doc;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
