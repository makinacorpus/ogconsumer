<?php

namespace OgConsumer;

/**
 * Node data
 */
class Object implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Structured object type
     *
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $data;

    /**
     * Default constructor
     *
     * @param string $type Object type
     * @param string $data Arbitrary parsed data from node
     */
    public function __construct($type, array $data = array())
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get object type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get only first value if value is multiple
     *
     * @param string $name Property name
     *
     * @return mixed       Value or null if property not set
     */
    public function get($name)
    {
        if (!empty($this->data[$name])) {
            if (is_array($this->data[$name])) {
                return reset($this->data[$name]);
            } else {
                return $this->data[$name];
            }
        }
    }

    /**
     * Get all values for the given property as array
     *
     * @param string $name Property name
     *
     * @return mixed[]     Values array which can be empty
     */
    public function getAll($name)
    {
        if (!empty($this->data[$name])) {
            if (is_array($this->data[$name])) {
                return $this->data[$name];
            } else {
                return array($this->data[$name]);
            }
        } else {
            return array();
        }
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return !empty($this->data[$offset]);
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        trigger_error("Trying to modify a readonly object", E_USER_ERROR);
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        trigger_error("Trying to modify a readonly object", E_USER_ERROR);
    }

    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->data);
    }
}
