<?php

namespace APubSub\Tests;

use OgConsumer\Service;

class RealLifeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $service;

    protected function setUp()
    {
        $this->service = new Service();
    }

    public function testFetchSingle()
    {
        $node = $this
            ->service
            ->fetch("https://www.youtube.com/watch?v=rTnNwLaTGFI");
    }

    public function testFetchMultiple()
    {
        $urls = array(
            "https://www.youtube.com/watch?v=upZuJcnQTAw",
            "https://www.youtube.com/watch?v=rTnNwLaTGFI",
            "http://www.nytimes.com/",
            "http://www.nytimes.com/2013/07/29/us/detroit-looks-to-health-law-to-ease-costs.html?hp",
            "http://9gag.com/",
            "http://9gag.com/gag/aOqmnzE",
        );

        $nodes = $this->service->fetchAll($urls);

        foreach ($urls as $key => $value) {
            $this->assertTrue(isset($nodes[$key]));
            // All of the previous should work.
            $this->assertInstanceOf('\OgConsumer\Node', $nodes[$key]);
        }

    }
}
