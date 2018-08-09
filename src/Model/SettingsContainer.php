<?php

namespace ConfiConfig\Model;

class SettingsContainer
{
    /** @var  boolean */
    private $use_compiled_config;
    /** @var int */
    private $compiled_config_ttl;
    /** @var  string */
    private $compiled_config_location;
    /** @var  array */
    private $environment_specific_config_suffixes;
    /** @var  array */
    private $config_paths;
    /** @var  array */
    private $config_folders;
    /** @var  string */
    private $cache_key;


    public function __construct()
    {
        $this->use_compiled_config = true;
        $this->compiled_config_ttl = 86400;
        $this->compiled_config_location = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR.'compiledConfig';

        $this->environment_specific_config_suffixes = array();
        $this->config_paths = array();
        $this->config_folders = array();
        $this->cache_key = 'confiConfig_cached_config';
    }

    /**
     * @return boolean
     */
    public function isUseCompiledConfig()
    {
        return $this->use_compiled_config;
    }

    /**
     * @param boolean $use_compiled_config
     */
    public function setUseCompiledConfig($use_compiled_config)
    {
        $this->use_compiled_config = $use_compiled_config;
    }

    /**
     * @return int
     */
    public function getCompiledConfigTtl()
    {
        return $this->compiled_config_ttl;
    }

    /**
     * @param int $compiled_config_ttl
     */
    public function setCompiledConfigTtl($compiled_config_ttl)
    {
        $this->compiled_config_ttl = $compiled_config_ttl;
    }

    /**
     * @return string
     */
    public function getCompiledConfigLocation()
    {
        return $this->compiled_config_location;
    }

    /**
     * @param string $compiled_config_location
     */
    public function setCompiledConfigLocation($compiled_config_location)
    {
        $this->compiled_config_location = $compiled_config_location;
    }

    /**
     * @return array
     */
    public function getEnvironmentSpecificConfigSuffixes()
    {
        return $this->environment_specific_config_suffixes;
    }

    /**
     * @param array $environment_specific_config_suffixes
     */
    public function setEnvironmentSpecificConfigSuffixes($environment_specific_config_suffixes)
    {
        $this->environment_specific_config_suffixes = $environment_specific_config_suffixes;
    }

    /**
     * @return array
     */
    public function getConfigPaths()
    {
        return $this->config_paths;
    }

    /**
     * @param array $config_paths
     */
    public function setConfigPaths($config_paths)
    {
        $this->config_paths = $config_paths;
    }

    /**
     * @return array
     */
    public function getConfigFolders()
    {
        return $this->config_folders;
    }

    /**
     * @param array $config_folders
     */
    public function setConfigFolders($config_folders)
    {
        $this->config_folders = $config_folders;
    }

    /**
     * @return array
     */
    public function getProperties(){
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cache_key;
    }

    /**
     * @param string $cache_key
     */
    public function setCacheKey($cache_key)
    {
        $this->cache_key = $cache_key;
    }
}
