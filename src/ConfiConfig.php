<?php


namespace ConfiConfig;


use ConfiConfig\Model\SettingsContainer;
use Psr\Cache\CacheItemPoolInterface;
use Zend\Config\Factory;

class ConfiConfig
{
    /** @var array */
    private $compiledConfig;
    /** @var SettingsContainer */
    private $confiConfigSettings;
    /** @var  CacheItemPoolInterface */
    private $cache;
    /** @var  boolean */
    private $useCompiledCache = false;
    /** @var  int */
    private $cacheTtl;

    const TTL_KEY = 'confiConfig_Ttl';
    const CACHE_KEY = 'confiConfig_cached_config';

    /**
     * ConfiConfig constructor.
     * @param string $confiConfigPath
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct($confiConfigPath = '', CacheItemPoolInterface $cache = null)
    {
        if($cache instanceof CacheItemPoolInterface){
            $this->cache = $cache;
            $this->sueCompiledCache = true;
        }
        $this->confiConfigSettings = new SettingsContainer();
        if(empty($confiConfigPath)){
            $confiConfigPath = dirname(__FILE__) .DIRECTORY_SEPARATOR.'confiConfig.settings.php';
        }
        $this->generateConfiConfigConfig($confiConfigPath);
    }

    /**
     * @return array
     */
    public function getConfig(){
        if($this->confiConfigSettings->isUseCompiledConfig()){
            $this->loadCompiledConfigFromCache();
            $this->loadCompiledConfigFromDisk();
            if(!empty($this->compiledConfig)){
                return $this->compiledConfig;
            }
        }
        $this->generateCompiledConfig();
        return $this->compiledConfig;
    }

    /**
     * @return void
     */
    protected function loadCompiledConfigFromCache(){
        if($this->useCompiledCache && empty($this->compiledConfig)){
            $cacheItem = $this->cache->getItem(self::CACHE_KEY);
            if($cacheItem->isHit()){
                $config = unserialize($cacheItem->get());
                if($config[self::TTL_KEY]?:0 > time()){
                    $this->compiledConfig = $config;
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function loadCompiledConfigFromDisk(){
        if(empty($this->compiledConfig)){
            if(file_exists($this->confiConfigSettings->getCompiledConfigLocation())){
                $config = unserialize(file_get_contents($this->confiConfigSettings->getCompiledConfigLocation()));
                if($config[self::TTL_KEY]?:0 > time()){
                    $this->compiledConfig = $config;
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function writeCompiledConfigToCache(){
        if($this->useCompiledCache && ! empty($this->compiledConfig)){
            $compiledConfigCacheItem = $this->cache->getItem(self::CACHE_KEY);
            $compiledConfigCacheItem->set(serialize($this->compiledConfig));
            $compiledConfigCacheItem->expiresAfter($this->confiConfigSettings->getCompiledConfigTtl());
            $this->cache->save($compiledConfigCacheItem);
        }
    }

    /**
     * @return void
     */
    protected function writeCompiledConfigToDisk(){
        if(! empty($this->compiledConfig)){
            file_put_contents($this->confiConfigSettings->getCompiledConfigLocation() ,serialize($this->compiledConfig));
        }
    }

    /**
     * @return void
     */
    protected function generateCompiledConfig()
    {
        $this->compiledConfig = $this->loadConfigs();
        $this->compiledConfig[self::TTL_KEY] = time() + $this->confiConfigSettings->getCompiledConfigTtl();
        $this->writeCompiledConfigToDisk();
        $this->writeCompiledConfigToCache();
    }

    /**
     * @return array
     */
    protected function loadConfigs(){
        $configFilePaths = array_merge($this->getConfigPaths(),$this->addGlobPaths());
        $configFilePaths = array_unique(array_merge($configFilePaths, $this->overrideWithLocalConfigs($configFilePaths)));
        return Factory::fromFiles($configFilePaths);
    }

    /**
     * @param $configPath
     */
    protected function generateConfiConfigConfig($configPath){
        if(file_exists($configPath)){
            $configArray = include $configPath;
            $this->updateConfiConfigWithConfigArray($configArray);
        }
    }

    /**
     * @param $configArray
     */
    protected function updateConfiConfigWithConfigArray($configArray){
        $blah = $this->confiConfigSettings->getProperties();
        foreach($this->confiConfigSettings->getProperties() as $key =>$val){
            if(isset($configArray[$key])){
                $propertyArray = explode('_',$key);
                foreach($propertyArray as $index => $value){
                    $propertyArray[$index] = ucfirst($value);
                }
                $method = 'set'.implode('',$propertyArray);
                $this->confiConfigSettings->$method($configArray[$key]);
            }
        }
    }

    /**
     * @return array
     */
    protected function addGlobPaths(){
        $configFolders = $this->confiConfigSettings->getConfigFolders();
        $folderPaths = array();
        foreach($configFolders as $folderToSearch=>$config){
            $folderToSearch = (substr($folderToSearch, -1) == DIRECTORY_SEPARATOR) ? substr($folderToSearch, 0, -1) : $folderToSearch;
            if($folderToSearch = $this->getValidPath($folderToSearch)){
                $includes = isset($config['include']) ?:array('*.ini','*.json','*.xml','*.yaml','*.yml','*.properties');
                foreach($includes as $globPattern){
                    $globPattern = $folderToSearch . DIRECTORY_SEPARATOR . $globPattern;
                    $folderPaths = array_merge($folderPaths, glob($globPattern));
                }
            }
        }
        return $folderPaths;
    }

    /**
     * @param array $configFilePaths
     * @return array
     */
    protected function overrideWithLocalConfigs(array $configFilePaths){
        if(count($this->confiConfigSettings->getEnvironmentSpecificConfigSuffixes())){
            $localConfigs = array();
            foreach($configFilePaths as $key => $configFilePath){
                $filePathArray = explode('.',$configFilePath);
                $filePathSegments = count($filePathArray);
                if($filePathSegments > 1 && in_array($filePathArray[$filePathSegments - 2],$this->confiConfigSettings->getEnvironmentSpecificConfigSuffixes())){
                    unset($configFilePaths[$key]);
                    continue;
                }
                foreach($this->confiConfigSettings->getEnvironmentSpecificConfigSuffixes() as $localCheck){
                    $localFilePathArray = $filePathArray;
                    $extension = array_pop($localFilePathArray);
                    $localFilePathArray[] = $localCheck;
                    $localFilePathArray[] = $extension;
                    if(file_exists($localFilePath = implode('.',$localFilePathArray))){
                        $localConfigs[] = $localFilePath;
                    }

                }
            }
            return $localConfigs;
        }
        return array();
    }

    /**
     * @return array
     */
    protected function getConfigPaths(){
        $validPaths = array();
        $configPaths = $this->confiConfigSettings->getConfigPaths();
        foreach($configPaths as $path){
            if($validPath = $this->getValidPath($path)){
                $validPaths[] = $validPath;
            }
        }
        return $validPaths;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getValidPath($path){
        if(file_exists($path)){
            return $path;
        }
//        if(file_exists($relativePath = getcwd().DIRECTORY_SEPARATOR.$path)){
//            return $relativePath;
//        }
    }
}
