<?php

namespace OgConsumer;

/**
 * Node data retriever
 */
class Service
{
    /**
     * Default registered types
     *
     * @var string[]
     */
    static protected $registeredTypes = array(
        'default' => '\OgConsumer\Node',
        'article' => '\OgConsumer\Object\Article',
        'blog'    => '\OgConsumer\Object\Blog',
        'image'   => '\OgConsumer\Object\Image',
        'video'   => '\OgConsumer\Object\Video',
        'website' => '\OgConsumer\Object\Website',
    );

    /**
     * Register new node types
     *
     * @param array $types Key value pairs: keys are type machine name from
     *                     the og:type property while values are valid class
     *                     names that should derivate from OgConsumer\Node
     */
    static public function register(array $types)
    {
        foreach ($types as $type => $class) {
            if (!class_exists($class)) {
                throw new \LogicException(
                    "Class %s does not exists", $class);
            }

            // Allow defaults override
            self::$registeredTypes[$type] = $class;
        }
    }

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
     * Get node instance from data
     *
     * @param array $nodeData Parsed node data
     *
     * @return Node           New node instance
     */
    public function getInstanceFromData(array $nodeData)
    {
        if (isset($nodeData['type'])) {
            $type = $nodeData['type'];
        } else {
            $type = 'default';
        }

        if (isset(self::$registeredTypes[$type])) {
            $class = self::$registeredTypes[$type];
        } else {
            $class = self::$registeredTypes['default'];
        }

        return new $class($nodeData);
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

        foreach ($this->fetcher->fetchAll($urlList) as $key => $data) {
            if (false === $data || empty($data)) {
                $ret[$key] = false;
            } else {
                $ret[$key] = $this->getInstanceFromData(
                    $this->parser
                        ->parse($data)
                );
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
        return $this
            ->getInstanceFromData(
                $this
                    ->parser
                    ->parse($data)
            );
    }
}
