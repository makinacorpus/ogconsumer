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
     * @param string $data Fetched HTML content
     *
     * @return Node        Open graph node data
     */
    public function parse($data);
}
