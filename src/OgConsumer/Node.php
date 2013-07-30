<?php

namespace OgConsumer;

/**
 * Node data
 *
 * og:title, og:image, og:type and og:url are supposed to be mandatory
 * but hey we don't care, nobody respects that standard and we have greater
 * chances for this to work being liberal on what we accept
 */
class Node extends Object
{
    /**
     * Default locale if none provided
     */
    const DEFAULT_LOCALE = "en_US";

    /**
     * Default constructor
     *
     * @param string $type Object type
     * @param string $data Arbitrary parsed data from node
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;

        if (isset($this->data['type'])) {
            $this->type = $this->data['type'];
        }
    }

    /**
     * Get "og:title" property value
     *
     * @return string
     */
    public function getTitle()
    {
        if (isset($this->data['title'])) {
            return $this->data['title'];
        }
    }

    /**
     * Get "og:image" property value
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->get($name);
    }

    /**
     * Get "og:image" property value as array
     *
     * @return Image[]
     */
    public function getAllImages()
    {
        return $this->getAll('image');
    }

    /**
     * Get "og:url" property value
     *
     * @return string
     */
    public function getUrl()
    {
        if (isset($this->data['url'])) {
            return $this->data['url'];
        }
    }

    /**
     * Get "og:audio" property value
     *
     * @return Audio
     */
    public function getAudio()
    {
        return $this->get('audio');
    }

    /**
     * Get "og:audio" property value as array
     *
     * @return Audio[]
     */
    public function getAllAudio()
    {
        return $this->getAll('audio');
    }

    /**
     * Get "og:description" property value
     *
     * @return string
     */
    public function getDescription()
    {
        if (isset($this->data['description'])) {
            return $this->data['description'];
        }
    }

    /**
     * Get "og:determiner" property value
     *
     * @return string Open Graph type
     */
    public function getDeterminer()
    {
        if (isset($this->data['determiner'])) {
            return $this->data['determiner'];
        }
    }

    /**
     * Get "og:locale" property value
     *
     * @return string
     */
    public function getLocale()
    {
        if (isset($this->data['locale'])) {
            return $this->data['locale'];
        }
        return self::DEFAULT_LOCALE;
    }

    /**
     * Get "og:locale:alternate" property value
     *
     * @return string[]
     */
    public function getAlternateLocales()
    {
        return $this->getAll('locale:alternate');
    }

    /**
     * Get "og:site_name" property value
     *
     * @return string
     */
    public function getSiteName()
    {
        if (isset($this->data['site_name'])) {
            return $this->data['site_name'];
        }
    }

    /**
     * Get "og:video" property value
     *
     * @return Video
     */
    public function getAudio()
    {
        return $this->get('video');
    }

    /**
     * Get "og:video" property value as array
     *
     * @return Video[]
     */
    public function getAllAudio()
    {
        return $this->getAll('video');
    }
}
