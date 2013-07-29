<?php

namespace OgConsumer\Object;

class Image extends AbstractMedia
{
    /**
     * Get "og:image:width" property value
     *
     * @return int
     */
    public function getWidth()
    {
        if (isset($this->data['image:width'])) {
            return $this->data['image:width'];
        }
    }

    /**
     * Get "og:image:height" property value
     *
     * @return int
     */
    public function getHeight()
    {
        if (isset($this->data['image:height'])) {
            return (int)$this->data['image:height'];
        }
    }
}
