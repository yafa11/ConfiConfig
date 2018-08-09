<?php

namespace IntegrationTests\ConfiConfig;

use ConfiConfig\ConfiConfig;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamContainer;
use Psr\Cache\CacheItemPoolInterface;

class confiConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var  CacheItemPoolInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $cache;
    /** @var  vfsStreamContainer */
    private $fsRoot;

    public function setUp() {
        $this->fsRoot = vfsStream::setup();
    }

    public function testGetConfigReturnArray(){
        $sut = new ConfiConfig();
        $result = $sut->getConfig();

        self::assertTrue(is_array($result));
    }

}
