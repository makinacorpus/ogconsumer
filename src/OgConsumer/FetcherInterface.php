<?php

namespace OgConsumer;

/**
 * Fetches data from outer space
 */
interface FetcherInterface
{
    /**
     * Fetch one single URL content
     *
     * @param string $url URL
     *
     * @return string     Content
     *
     * @throws \Exception In case of any error
     */
    public function fetch($url);

    /**
     * Fetch multiple URL contents
     *
     * @param string[] $urlList List of URL to fetch
     *
     * @return string[]         Fetched content, keys are the same as input
     */
    public function fetchAll(array $urlList);
}
