<?php

namespace APubSub\Tests;

use OgConsumer\Service;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $service;

    protected function getFile($type)
    {
        return "file://" . __DIR__ . '/resources/' . $type . '.html';
    }

    protected function setUp()
    {
        $this->service = new Service();
    }

    public function testParseImageArray()
    {
        $node = $this
            ->service
            ->fetch(
                $this->getFile('image-array'));

        $images = $node->getAllImages();
        $this->assertCount(4, $images);

        $image = array_shift($images);
        $this->assertSame("http://ia.media-imdb.com/images/rock.jpg", $image->getUrl());
        $this->assertNull($image->getHeight());
        $this->assertNull($image->getWidth());

        $image = array_shift($images);
        $this->assertSame("http://example.com/rock.jpg", $image->getUrl());
        // FIXMe: Parser needs better value handling for object properties
        //$this->assertSame(300, $image->getHeight());
        //$this->assertSame(300, $image->getWidth());

        $image = array_shift($images);
        $this->assertSame("http://example.com/rock2.jpg", $image->getUrl());
    }

    public function testParseVideo()
    {
        $node = $this
            ->service
            ->fetch(
                $this->getFile('video'));

        $this->assertSame("YouTube", $node->getSiteName());
        $this->assertSame("http://www.youtube.com/watch?v=rTnNwLaTGFI", $node->getUrl());
        $this->assertSame("Daft Punk X Pharrell + Patrick Sebastien - \"Les Sardines\"", $node->getTitle());
        $this->assertSame("video", $node->getType());
        $this->assertSame(0, strpos($node->getDescription(), "Quand la French Touch"));

        $image = $node->getImage();
        $this->assertInstanceOf('\OgConsumer\Object\Image', $image);
        $this->assertSame("https://i1.ytimg.com/vi/rTnNwLaTGFI/maxresdefault.jpg?feature=og", $image->getUrl());

        $video = $node->getVideo();
        $this->assertInstanceOf('\OgConsumer\Object\Video', $video);
        $this->assertSame("http://www.youtube.com/v/rTnNwLaTGFI?autohide=1&version=3", $video->getUrl());
    }
}
