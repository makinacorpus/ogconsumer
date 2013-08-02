<?php

namespace OgConsumer;

/**
 * Node data retriever
 */
class Service
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var FetcherInterface
     */
    private $fetcher;

    /**
     * Default constructor
     *
     * @param string $url             Open graph node URL
     * @param ParserInterface $parser Parser to use if different from default
     */
    public function __construct(
        ParserInterface $parser = null,
        FetcherInterface $fetcher = null)
    {
        if (null === $parser) {
            $this->parser = new DefaultParser();
        } else {
            $this->parser = $parser;
        }

        if (null === $fetcher) {
            if (function_exists('curl_init')) {
                $this->fetcher = new CurlFetcher();
            } else {
                $this->fetcher = new FallbackFetcher();
            }
        } else {
            $this->fetcher = $fetcher;
        }
    }

    /**
     * Get node from URL
     * 
     * @param string $url        Node URL
     *
     * @return Node              Parsed node
     *
     * @throws \RuntimeException In case of any error
     */
    public function fetch($url)
    {
        return $this
            ->parser
            ->parse(
                $this
                  ->fetcher
                  ->fetch($url)
            );
    }

    /**
     * Get multiple nodes from URL
     *
     * This method will be silent when errors happen, return array will be
     * keyed using the incoming array keys, each erroneous fetch will be
     * set to false instead
     *
     * @param string[] $urlList List of URL
     *
     * @return Node[]           Fetched nodes
     */
    public function fetchAll(array $urlList)
    {
        $ret = array();

        if (empty($urlList)) {
            return $ret; // Short-circuit stupid users
        }

        // Most fetchers will be faster with only URL than many let's just
        // do a single call whenever the array has only one entry
        if (1 === count($urlList)) {

            $url = reset($urlList);
            $key = key($urlList);

            try {
                $ret[$key] = $this->fetch($url);
            } catch (\Exception $e) {
                $ret[$key] = false;
            }
        } else {
            foreach ($this->fetcher->fetchAll($urlList) as $key => $data) {
                if (false === $data || empty($data)) {
                    $ret[$key] = false;
                } else {
                    $ret[$key] = $this->parser->parse($data);
                }
            }
        }

        return $ret;
    }

    /**
     * Get node from HTML code
     *
     * @param string $data       Node URL
     *
     * @return Node              Parsed node
     *
     * @throws \RuntimeException In case of any error
     */
    public function getNodeFromHtml($data)
    {
        return $this->parser->parse($data);
    }
}
