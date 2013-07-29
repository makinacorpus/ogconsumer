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
    }
}
