<?php

namespace OgConsumer;

/**
 * Types as defined by OGP standard and transcripted from http://ogp.me/
 *
 * Data structure this class handle are basic ones described there.
 *
 * This class does not support (yet) schema introspection and data validation
 */
final class TypeHelper
{
    /**
     * Boolean
     */
    const DATATYPE_BOOLEAN = 1;

    /**
     * ISO 8601 date
     */
    const DATATYPE_DATETIME = 2;

    /**
     * Enum of string values
     */
    const DATATYPE_ENUM = 3;

    /**
     * Float
     */
    const DATATYPE_FLOAT = 4;

    /**
     * Integer
     */
    const DATATYPE_INTEGER = 5;

    /**
     * String
     */
    const DATATYPE_STRING = 6;

    /**
     * Structure object
     */
    const DATATYPE_STRUCTURED = 100;

    /**
     * URL
     */
    const DATATYPE_URL = 7;

    /**
     * Invalid or unknown type
     */
    const DATATYPE_UNKNOWN = 0;

    /**
     * Find datatype to apply depending on the property name and structure
     * type if any
     *
     * @param string $propertyName  Property name
     * @param string $structureType Structured data type, if none given
     *                              consider the property as a top level
     *                              metadata value
     */
    static public function getPropertyDataType($propertyName, $structureType = null)
    {
        // @todo No usage of $structuredType yet because we have no ambiguities
        // yet in various property names inside structure properties
        switch ($propertyName) {

            case 'secure_url':
            case 'url':
                return self::DATATYPE_URL;

            case 'description':
            // "locale:alternate" is violating the standard because it is using
            // the structured object operator for properties
            case 'locale:alternate':
            case 'locale':
            case 'site_name':
            case 'title':
            case 'type':
                return self::DATATYPE_STRING;

            case 'height':
            case 'width':
                return self::DATATYPE_INTEGER;

            case 'audio':
            case 'image':
            case 'video':
                return self::DATATYPE_STRUCTURED;

            case 'determiner':
                return self::DATATYPE_ENUM;
        }
    }

    /**
     * Parse value and return a valid PHP typed data
     *
     * @param string $propertyName Property name
     * @param string $value        Raw string value
     * @param int $type            Type
     *
     * @return mixed               PHP value
     */
    static public function parseValue($value, $dataType = self::DATATYPE_UNKNOWN)
    {
        switch ($dataType) {

            case self::DATATYPE_BOOLEAN:
                return is_numeric($value)
                    ? (bool)$value
                    : "true" === strtolower($value);

            case self::DATATYPE_DATETIME:
                return \DateTime::createFromFormat(\DateTime::ISO8601, $value);

            case self::DATATYPE_FLOAT:
                return (float)$value;

            case self::DATATYPE_INTEGER:
                return (int)$value;

            case self::DATATYPE_STRUCTURED:
            // Enum are strings
            case self::DATATYPE_ENUM:
            case self::DATATYPE_STRING:
            // Let unknown type pass
            case self::DATATYPE_UNKNOWN:
            // URL is a string
            case self::DATATYPE_URL:
                return (string)$value;

        }
    }
}
