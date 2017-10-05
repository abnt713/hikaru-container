<?php

use PHPUnit\Framework\TestCase;

use AlisonBnt\Hikaru\HikaruContainer;
use AlisonBnt\Hikaru\EntryNotFoundException;
use Psr\Container\ContainerInterface;

class ContainerTest extends TestCase
{
    private $entries;
    private $container;

    public function setUp()
    {
        $this->entries = array(
            'Alpha' => 1,
            'Bravo' => null,
            'Charlie' => function (ContainerInterface $container) {
                return array('alpha' => $container->get('Alpha'));
            }
        );

        $this->container = new HikaruContainer($this->entries);
    }

    public function testContainerPlainValue()
    {
        $alpha = $this->container->get('Alpha');
        $this->assertEquals($alpha, 1);
    }

    public function testContainerCallback()
    {
        $charlie = $this->container->get('Charlie');
        $this->assertTrue(is_array($charlie));
        $this->assertEquals($charlie['alpha'], $this->container->get('Alpha'));
    }

    public function testCallbackReassign()
    {
        $charlie = $this->container->get('Charlie');
        $this->assertTrue(is_array($charlie) && !is_callable($charlie));
        $charlieAgain = $this->container->get('Charlie');
        $this->assertTrue(is_array($charlieAgain) && !is_callable($charlieAgain) && $charlie === $charlieAgain);
    }

    public function testNotFound()
    {
        $this->expectException(EntryNotFoundException::class);
        $this->container->get('Zulu');
    }
}
