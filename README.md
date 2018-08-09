# ConfiConfig
Compile several config files into a single config file and cache it.
Can combine files of type 'ini', 'json', 'php', 'xml', and 'properties'
Allows for environment specific configs to override global configs.

Installation
------------

You can install this package through Composer:

```json
{
    "require": {
        "yafa11/ConfiConfig": "~1.0"
    }
}
```

The packages adheres to the [SemVer](http://semver.org/) specification, and there will be full backward compatibility
between minor versions.


Usage
-----
### Create confiConfig settings file
Create 'confiConfig.settings.php' in your project by making a copy of ConfiConfig/src/confiConfig.settings.php.sample
and then configure for your environment. The sample config file provides explanations of configuration setting.

### Initialize in your project
Add the following lines to your project bootstrap. Replace <path to settings> with the path to the confiConfig.settings.php
file you created in the previous step:
```
    $configService = new ConfiConfig('<path to settings>');
    $config = $configService->getConfig();
```
$config will be populated with a multidimensional array version of the compiled configs.

### Caching
If you would like, you can reduce disk reads by storing the compiled config in cache. ConfiConfig uses 
[PSR-6](http://www.php-fig.org/psr/psr-6/) interfaces for cache services. Simply instantiate your cache item pool and
pass it in as the second parameter when instantiating the ConfiConfig service
```
    $configService = new ConfiConfig('../config.settings.php', $cacheItemPool);
```
When defined the compiled config will be stored in the provided cache item pool. The TTL for the file generation is
used for the cache key. ConfiConfig will always check cache before falling back to checking for the file.  


    