<?php

namespace OgConsumer;

/**
 * Types as defined by OGP standard and transcripted from http://ogp.me/
 *
 * Data structure this class handle are basic ones described there.
 *
 * This class does not support (yet) schema introspection and data validation
 */
final class Type
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
     * Default object class
     */
    const OBJECT_CLASS_DEFAULT = '\OgConsumer\Object';

    /**
     * Registered known structured objects
     *
     * @var array[]
     */
    static protected $types = array(
        'default' => array(
            'class'      => self::OBJECT_CLASS_DEFAULT,
            'properties' => array(),
        ),
        'audio' => array(
            'class'      => '\OgConsumer\Object\Audio',
            'properties' => array(
                'type'       => self::DATATYPE_STRING,
                'secure_url' => self::DATATYPE_URL,
            ),
        ),
        'image' => array(
            'class'      => '\OgConsumer\Object\Image',
            'properties' => array(
                'type'       => self::DATATYPE_STRING,
                'secure_url' => self::DATATYPE_URL,
                'height'     => self::DATATYPE_INTEGER,
                'width'      => self::DATATYPE_INTEGER,
            ),
        ),
        'video' => array(
            'class'      => '\OgConsumer\Object\Video',
            'properties' => array(
                'type'       => self::DATATYPE_STRING,
                'secure_url' => self::DATATYPE_URL,
                'height'     => self::DATATYPE_INTEGER,
                'width'      => self::DATATYPE_INTEGER,
            ),
        ),
    );

    /**
     * Register a single type
     *
     * @param string $type      Type name found while parsing
     * @param string $class     Class name derivating from \OgConsumer\Object
     *                          to use when instanciating the object in graph
     * @param array $properties Key value pairs, keys are propertie names and
     *                          values are associated data type
     * @param array $merge      Set to false if you don't want the already
     *                          existing instance known properties to remain
     */
    static public function registerType(
        $type,
        $class            = null,
        array $properties = array(),
        $merge            = true)
    {
        if (null !== $class && !class_exists($class)) {
            throw new \InvalidArgumentException("Class %s does not exists", $class);
        }
        if (null === $class && !$merge) {
            $class = self::OBJECT_CLASS_DEFAULT;
        }

        if (isset(self::$types[$type]) && $merge) {
            if (null !== $class) {
                self::$types[$type]['class'] = $class;
            }
            if (!empty($properties)) {
                foreach ($properties as $name => $datatype) {
                    self::$types[$type]['properties'][$name] = $datatype;
                }
            }
        } else {
            self::$types[$type] = array(
                'class'      => $class,
                'properties' => $properties,
            );
        }
    }

    /**
     * Register new structured object types
     *
     * This method allow defaults override
     *
     * @param array $types Key value pairs: keys are type machine name from
     *                     the og:type property while values are valid class
     *                     names that should derivate from OgConsumer\Object
     */
    static public function register(array $types)
    {
        foreach ($types as $type => $def) {

            if (is_string($def)) {
                $def = array(
                    'class'      => $def,
                    'properties' => array(),
                );
            } else if (is_array($def)) {
                $def += array(
                    'class'      => null,
                    'properties' => array(),
                );
            } else {
                throw new \InvalidArgumentException(
                    sprintf("Invalid definition for type: %s", $type));
            }

            self::registerType($type, $def['class'], $def['properties']);
        }
    }

    /**
     * Get new object
     *
     * @param string $structureType Structured object type
     * @param array $data           Object properties
     *
     * @return Object
     */
    static public function getObject($type = 'default', array $data = null)
    {
        if (!isset(self::$types[$type])) {
            $type = 'default';
        }

        return new self::$types[$type]['class']($type, $data);
    }

    /**
     * Find datatype to apply depending on the property name and structure
     * type if any
     *
     * @param string $name  Property name
     * @param string $type Structured data type, if none given consider the
     *                     property as a top level metadata value
     */
    static public function getPropertyDataType($name, $type = null)
    {
        if (null !== $type &&
            isset(self::$types[$type]) &&
            isset(self::$types[$type]['properties'][$name]))
        {
            return self::$types[$type]['properties'][$name];
            // Else fallback on default behavior
        }

        // @todo No usage of $structuredType yet because we have no ambiguities
        // yet in various property names inside structure properties
        switch ($name) {

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
