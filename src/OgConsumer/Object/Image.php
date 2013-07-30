<?php

namespace OgConsumer\Object;

class Image extends AbstractMedia
{
    /**
     * Get "og:MEDIA:width" property value
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->get('width');
    }

    /**
     * Get "og:MEDIA:height" property value
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->get('height');
    }
}
