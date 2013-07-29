<?php

namespace OgConsumer;

/**
 * Fallback implementation using file_get_contents()
 */
class FallbackFetcher implements FetcherInterface
{
    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetch()
     */
    public function fetch($url)
    {
        if ($output = file_get_contents($url) && !empty($content)) {
            return $output;
        }

        throw new \RuntimeException(sprintf(
            "Invalid URL given: %s", $url));
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetchAll()
     */
    public function fetchAll(array $urlList)
    {
        $ret = array();

        foreach ($urlList as $key => $url) {
            if (($output = file_get_contents($url)) && !empty($output)) {
                $ret[$key] = $output;
            } else {
                $ret[$key] = false;
            }
        }

        return $ret;
    }
}
