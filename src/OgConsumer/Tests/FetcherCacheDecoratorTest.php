<?php

namespace APubSub\Tests;

use OgConsumer\Service;
use OgConsumer\Helper\FetcherCacheDecorator;
use OgConsumer\FallbackFetcher;

/**
 * @todo This needs a mockup fetcher
 * @todo Finish this
 */
class FetcherCacheDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $cache;

    public function setUp()
    {
        parent::setUp();

        $this->cache = array();
    }

    /**
     * Cache getter.
     *
     * @param array|string $keys
     */
    public function cacheGet($keys)
    {
        if (is_array($keys)) {
            $ret = array();
            foreach ($keys as $index => $key) {
                if (isset($this->cache[$key])) {
                    $ret[$index] = $key;
                }
            }
            return $ret;
        } else {
            if (isset($this->cache[$key])) {
                return $this->cache[$key];
            }
        }
    }

    /**
     * Cache setter.
     *
     * @param string $key
     * @param mixed $data
     */
    public function cacheSet($key, $data)
    {
        $this->cache[$key] = $data;
    }

    /**
     * Test it. What else?
     */
    public function testIt()
    {
        $fetcher = new FetcherCacheDecorator(
            new FallbackFetcher(),
            array($this, 'cacheGet'),
            array($this, 'cacheSet'));
    }
}
