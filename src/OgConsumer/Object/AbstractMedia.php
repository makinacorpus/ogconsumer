<?php

namespace OgConsumer\Object;

use OgConsumer\Object;

abstract class AbstractMedia extends Object
{
    /**
     * Get "og:MEDIA" property value
     *
     * @return int
     */
    public function getUrl()
    {
        return $this->get('content');
    }

    /**
     * Get "og:MEDIA:secure_url" property value
     *
     * @return int
     */
    public function getSecureUrl()
    {
        return $this->get('secure_url');
    }

    /**
     * Get "og:MEDIA:type" property value
     *
     * @return int
     */
    public function getMimeType()
    {
        return $this->get('type');
    }
}
