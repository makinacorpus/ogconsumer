<?php

namespace OgConsumer\Object;

use OgConsumer\Node;

abstract class AbstractMedia extends Node
{
    /**
     * Get "og:image:secure_url" property value
     *
     * @return int
     */
    public function getSecureUrl()
    {
        if (isset($this->data['image:secure_url'])) {
            return $this->data['image:secure_url'];
        }
    }

    /**
     * Get "og:image:type" property value
     *
     * @return int
     */
    public function getMimeType()
    {
        if (isset($this->data['image:type'])) {
            return $this->data['image:type'];
        }
    }
}
