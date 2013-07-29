<?php

namespace OgConsumer;

/**
 * Node data
 */
class Node implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Default type if none provided
     */
    const DEFAULT_TYPE = "website";

    /**
     * Default locale if none provided
     */
    const DEFAULT_LOCALE = "en_US";

    /**
     * Value returned for mandatory properties when none set
     */
    const RETURN_ERROR = 'ERROR';

    /**
     * @var array
     */
    private $data = array();

    /**
     * Default constructor
     *
     * @param string $data Arbitrary parsed data from node
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Tell if the node contains all required properties
     *
     * @return boolean
     */
    public function isValid()
    {
        return
            // Remember that type can be defaulted to "website"
            isset($this->data['title']) &&
            isset($this->data['type']) &&
            isset($this->data['image']);
    }

    /**
     * Get "og:type" property value
     *
     * @return string
     */
    public function getType()
    {
        if (isset($this->data['type'])) {
            return $this->data['type'];
        }
        return static::DEFAULT_TYPE;
    }

    /**
     * Get "og:title" property value
     *
     * @return string
     */
    public function getTitle()
    {
        if (isset($this->data['title'])) {
            return $this->data['title'];
        }
        return self::RETURN_ERROR;
    }

    /**
     * Get "og:image" property value
     *
     * @return string
     */
    public function getImage()
    {
        if (isset($this->data['image'])) {
            return $this->data['image'];
        }
        return self::RETURN_ERROR;
    }

    /**
     * Get "og:url" property value
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get "og:audio" property value
     *
     * @return string
     */
    public function getAudio()
    {
        if (isset($this->data['audio'])) {
            $this->data['audio'];
        }
    }

    /**
     * Get "og:description" property value
     *
     * @return string
     */
    public function getDescription()
    {
        if (isset($this->data['description'])) {
            $this->data['description'];
        }
    }

    /**
     * Get "og:determiner" property value
     *
     * @return string Open Graph type
     */
    public function getDeterminer()
    {
        if (isset($this->data['determiner'])) {
            $this->data['determiner'];
        }
    }

    /**
     * Get "og:locale" property value
     *
     * @return string
     */
    public function getLocale()
    {
        if (isset($this->data['locale.alternate'])) {
            $this->data['locale.alternate'];
        }
        return self::DEFAULT_LOCALE;
    }

    /**
     * Get "og:locale:alternate" property value
     *
     * @return string[]
     */
    public function getAlternateLocales()
    {
        if (isset($this->data['locale.alternate'])) {
            return $this->data['locale.alternate'];
        }
        return array();
    }

    /**
     * Get "og:site_name" property value
     *
     * @return string
     */
    public function getSiteName()
    {
        if (isset($this->data['site_name'])) {
            return $this->data['site_name'];
        }
    }

    /**
     * Get "og:video" property value
     *
     * @return string
     */
    public function getVideo()
    {
        if (isset($this->data['video'])) {
            return $this->data['video'];
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
        trigger_error("Object is readonly", E_USER_WARNING);
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset ($offset)
    {
        trigger_error("Object is readonly", E_USER_WARNING);
    }

    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator ()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Convert back this instance to the original parsed data array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Give back valid HTML
     */
    public function __toString()
    {
        throw new \Exception("Not implemented yet");
    }
}
