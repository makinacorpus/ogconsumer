<?php

namespace OgConsumer;

/**
 * Open graph data parser interface.
 */
interface ParserInterface
{
    /**
     * Parse data from content
     *
     * @param string $content Fetched HTML content
     *
     * @return Node           Open graph node data
     */
    public function parse($content);
}
