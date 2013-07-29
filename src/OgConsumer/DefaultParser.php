<?php

namespace OgConsumer;

/**
 * Default parser implementation using PHP libxml support
 *
 * libxml is probably the fastest and the most flexible HTML parser in PHP,
 * using it ensures that we are able to parse even malformed HTML in a very
 * liberal way (and ensures we do it fast!)
 *
 * This parser contains some bits of customizations from
 * https://github.com/scottmac/opengraph regarding some malformed well known
 * sites data. Credits goes to their author. 
 */
class DefaultParser implements ParserInterface
{
    /**
     * Parse boolean from value
     *
     * @param string $value
     *
     * @return bool
     */
    static public function parseBoolean($value)
    {
        if (is_numeric($value)) {
            return (bool)$value;
        } else if (is_string($value)) {
            return 'true' === strtolower($value);
        }
        return true;
    }

    /**
     * Parse datetime from value
     *
     * @param string $value
     *
     * @return DateTime
     */
    static public function parseDateTime($value)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * Parse enum from value
     *
     * @param string $value
     *
     * @return string[]
     */
    static public function parseEnum($value)
    {
        return (string)$value;
    }

    /**
     * Parse float from value
     *
     * @param string $value
     *
     * @return float
     */
    static public function parseFloat($value)
    {
        return (float)$value;
    }

    /**
     * Parse integer from value
     *
     * @param string $value
     *
     * @return integer
     */
    static public function parseInteger($value)
    {
        return (int)$value;
    }

    /**
     * Parse string from value
     *
     * @param string $value
     *
     * @return string
     */
    static public function parseString($value)
    {
        return (string)$value;
    }

    /**
     * Parse URL from value
     *
     * @param string $value
     *
     * @return string
     */
    public static function parseUrl($value)
    {
        return (string)$url;
    }

    /**
     * Parse single element and populate data using it
     *
     * @param string $propertyName Property name extracted from the property
     *                             attribuge of the given node
     * @param \DOMElement $element Meta DOM node containing Open Graph data
     *
     * @return mixed               Parsed value
     */
    protected function parseElement($propertyName, \DOMElement $element)
    {
        // First parse value if any
        if ($element->hasAttribute("content")) {
            $value = $element->getAttribute("content");
        } else if ($element->hasAttribute("value")) {
            // From https://github.com/scottmac/opengraph some sites might have
            // malformed open graph data.
            $value = $element->getAttribute("value");
        }

        if (empty($value)) {
            // Invalid data given
            return;
        }

        // FIXME: Parse type. Note: parsing type depends on the schema.

        return $value;
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\ParserInterface::parse()
     */
    public function parse($data)
    {
        $nodeData = array();

        // Disable libxml error handling temporarily
        $status = libxml_use_internal_errors(true);

        $d = new \DOMDocument();
        $d->loadHTML($data);

        $meta = $d->getElementsByTagName('meta');

        // Enable back libxml error handling if different
        if (!$status) {
            libxml_use_internal_errors(false);
        }

        if (!$meta || 0 === $meta->length) {
            throw new \RuntimeException(
                sprintf("Invalid HTML given: document has no meta"));
        }

        foreach ($meta as $node) {
            if ($node->hasAttribute("property") &&
                ($propertyName = $node->getAttribute('property')) &&
                // Only treat properties that are in the og: namespace
                'og:' === substr($propertyName, 0, 3))
            {
                $name  = substr($propertyName, 3);
                $value = $this->parseElement($name, $node);

                // Empty values have no use for us
                if (empty($value)) {
                    continue;
                }

                // Handle enum and array values
                if (isset($nodeData[$name])) {
                    if (is_array($name)) {
                        $nodeData[$name][] = $value;
                    } else {
                        $nodeData[$name] = array(
                            $nodeData[$name],
                            $value,
                        );
                    }
                } else {
                    $nodeData[$name] = $value;
                }
            }
        }

        return $nodeData;
    }
}
