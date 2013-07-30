<?php

namespace OgConsumer;

/**
 * Default parser implementation using PHP libxml support
 *
 * libxml is probably the fastest and the most flexible HTML parser in PHP,
 * using it ensures that we are able to parse even malformed HTML in a very
 * liberal way (and ensures we do it fast!)
 *
 * This parser is very loosly typed except for a few known types, it will
 * transform any structured object or array dynamically during parsing
 */
class DefaultParser implements ParserInterface
{
    /**
     * Push value into given context array
     *
     * This function handle value arrays transparently
     * So sad the http://ogp.me/ spec does not really specifies where
     * or when we should use arrays or single values
     *
     * @param string $name Value property name
     * @param mixed $value Value content
     * @param array $data  Container where to push
     */
    protected function pushValue($name, $value, &$data)
    {
        if (isset($data[$name])) {
            if (is_array($data[$name])) {
                $data[$name][] = $value;
            } else {
                $data[$name] = array(
                    $data[$name],
                    $value,
                );
            }
        } else {
            $data[$name] = $value;
        }
    }

    /**
     * Create node from given meta list
     *
     * @param DOMNode[] &$stack Current meta item stack
     * @param array &$data      Current contextual data
     */
    protected function createNode(array &$stack)
    {
        $data        = array();
        $context     = null;
        $contextData = null;

        while ($node = array_shift($stack)) {

            $propertyName = substr($node->getAttribute('property'), 3);
            $dataType     = TypeHelper::getPropertyDataType($propertyName);
            $value        = TypeHelper::parseValue($node->getAttribute('content'), $dataType);

            if (TypeHelper::DATATYPE_STRUCTURED === $dataType) {

                if (isset($context)) {
                    // Value is a new structured object we therefore need
                    // to close the previous context
                    $this->pushValue($context, new Object($context, $contextData), $data);
                }

                // Create new structured object context
                $context     = $propertyName;
                $contextData = array('content' => $value);

            } else {
                if (isset($context)) {
                    // Deal with existing context
                    if (($pos = strpos($propertyName, ':')) &&
                        (list($target, $objectPropertyName) = explode(':', $propertyName, 2)) &&
                        $target === $context)
                    {
                        // We are still in the same context, push new
                        // properties to the existing data array
                        $this->pushValue($objectPropertyName, $value, $contextData);

                    } else {
                        // We are not in a structured object context anymore
                        // we must close the previous context
                        $this->pushValue($context, new Object($context, $contextData), $data);
                        $context     = null;
                        $contextData = null;

                        // We finished an object, but we still have a value to
                        // process, just push it
                        $this->pushValue($propertyName, $value, $data);
                    }
                } else {
                    $this->pushValue($propertyName, $value, $data);
                }
            }
        }

        // Stack traversal could end parsing a structure object
        if (isset($context)) {
            $this->pushValue($context, new Object($context, $contextData), $data);
        }

        return new Node($data);
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\ParserInterface::parse()
     */
    public function parse($content)
    {
        $data = array();
        $stack = array();

        // Disable libxml error handling temporarily
        $status = libxml_use_internal_errors(true);

        $d = new \DOMDocument();
        $d->loadHTML($content);

        $metaList = $d->getElementsByTagName('meta');

        // Enable back libxml error handling if different
        if (!$status) {
            libxml_use_internal_errors(false);
        }

        if (!$metaList || 0 === $metaList->length) {
            throw new \RuntimeException(
                sprintf("Invalid HTML given: document has no meta"));
        }

        foreach ($metaList as $node) {
            if ($node->hasAttribute("property") &&
                $node->hasAttribute("content") &&
                ($propertyName = $node->getAttribute('property')) &&
                // Only treat properties that are in the og: namespace
                'og:' === substr($propertyName, 0, 3))
            {
                $stack[] = $node;
            }
        }

        return $this->createNode($stack);
    }
}
