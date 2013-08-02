<?php

namespace OgConsumer\Helper;

use OgConsumer\FetcherInterface;

/**
 * Cache decorator for systems that need low latency and that occurs to do
 * the same requests very frequently
 */
class FetcherCacheDecorator implements FetcherInterface
{
    /**
     * @var FetcherInterface
     */
    protected $fetcher;

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var callable
     */
    protected $getCallback;

    /**
     * @var callable
     */
    protected $setCallback;

    /**
     * Default constructor
     *
     * @param FetcherInterface $nested Parent instance
     * @param callable $getCallback    Callback that accepts one parameters:
     *                                  - $key (string|array): the key(s) to
     *                                    fetch
     *                                  - This must return either a single entry
     *                                    or a list of entry depending if the
     *                                    input parameter is a string or an
     *                                    array, or a strict FALSE in case of
     *                                    errors or cache miss. In case of array
     *                                    keys must be preserved
     * @param callable $setCallback    Callback that accepts three parameters
     *                                 (the third being optional):
     *                                  - $key (string): the key to store
     *                                  - $data (mixed): the data to store
     *                                  - $lifetime (int): data lifetime, if
     *                                    null consider item being permanent
     * @param int $lifetime            Default lifetime in seconds
     */
    public function __construct(
        FetcherInterface $nested,
        $getCallback,
        $setCallback,
        $lifetime = null
    )
    {
        if (!is_callable($getCallback)) {
            throw new \InvalidArgumentException("Get callback must be callable");
        }
        if (!is_callable($setCallback)) {
            throw new \InvalidArgumentException("Get callback must be callable");
        }

        $this->getCallback = $getCallback;
        $this->setCallback = $setCallback;
        $this->fetcher = $nested;
        $this->lifetime = $lifetime;
    }

    /**
     * Get key from URL
     *
     * @param string $url URL to shrink
     *
     * @return string     Cache key
     */
    public function getKey($url)
    {
        return md5($url);
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetch()
     */
    public function fetch($url)
    {
        $key = $this->getKey($url);

        if (false === ($ret = call_user_func($this->getCallback, $key))) {
            $ret = $this->fetcher->fetch($url);

            call_user_func($this->setCallback, $key, $ret, $this->lifetime);
        }

        return $ret;
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetchAll()
     */
    public function fetchAll(array $urlList)
    {
        $keys = array();
        foreach ($urlList as $index => $url) {
            $keys[$index] = $this->getKey($url);
        }

        $ret = call_user_func($this->getCallback, $keys);

        if (count($ret) === count($urlList)) {
            return $ret;
        }

        $missing = array_diff_key($urlList, $keys);

        foreach ($this->fetcher->fetchAll($missing) as $index => $content) {
            $ret[$index] = $content;

            call_user_func(
                $this->setCallback,
                $keys[$index],
                $content,
                $this->lifetime);
        }

        return $ret;
    }
}
