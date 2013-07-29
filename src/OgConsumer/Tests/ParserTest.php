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

    public function testParseVideo()
    {
        $node = $this
            ->service
            ->fetch(
                $this->getFile('video'));

        $this->assertInstanceOf('\OgConsumer\Object\Video', $node);
        $this->assertSame("YouTube", $node->getSiteName());
        $this->assertSame("http://www.youtube.com/watch?v=rTnNwLaTGFI", $node->getUrl());
        $this->assertSame("Daft Punk X Pharrell + Patrick Sebastien - \"Les Sardines\"", $node->getTitle());
        $this->assertSame("video", $node->getType());
        $this->assertSame("https://i1.ytimg.com/vi/rTnNwLaTGFI/maxresdefault.jpg?feature=og", $node->getImage());
        $this->assertSame(0, strpos($node->getDescription(), "Quand la French Touch"));
        $this->assertSame("http://www.youtube.com/v/rTnNwLaTGFI?autohide=1&version=3", $node->getVideo());
    }
}
