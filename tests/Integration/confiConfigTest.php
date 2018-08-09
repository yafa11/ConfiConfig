<?php

namespace UnitTests\ConfiConfig;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamContainer;

class confiConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var  vfsStreamContainer */
    private $fsRoot;

    public function setUp() {
        $this->fsRoot = vfsStream::setup();
    }

    public function testConfigBuiltFromTwoConfigFilesInSeparateFolders(){

    }

    public function testConfigBuiltFromTwoConfigFilesOneWithSpecifiedPathAndOneInAFolder(){

    }

    public function testConfigBuiltFromOnlyIniConfigs(){

    }
    
    public function testLocalConfigOverridesGlobal(){

    }

    private function generateConfig(
        $useCompiledConfig = true,
        $configTtl = 100,
        $configSuffixes = array('local'),
        $configPaths = array(),
        $configFolders = array(),
        $cacheKey = 'testCacheKey',
        $compiledConfigLocation = 'testCompiledConfig'){
        return array(
            'use_compiled_config' => $useCompiledConfig,
            'compiled_config_ttl' => $configTtl,
            'compiled_config_location' => $compiledConfigLocation,
            'environment_specific_config_suffixes' => $configSuffixes,
            'config_paths' => $configPaths,
            'config_folders' => $configFolders,
            'cache_key' => $cacheKey,
        );
    }
}
