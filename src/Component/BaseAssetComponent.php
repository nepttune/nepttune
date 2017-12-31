<?php

namespace Peldax\NetteInit\Component;

abstract class BaseAssetComponent extends BaseComponent
{
    /**
     * @var \Kdyby\Redis\RedisStorage
     * @inject
     */
    public $storage;

    public function injectStorage(\Kdyby\Redis\RedisStorage $redisStorage)
    {
        $this->storage = $redisStorage;
    }

    public function getIntegrity(string $path) : string
    {
        $cache = new \Nette\Caching\Cache($this->storage);

        return $cache->call('Peldax\NetteInit\Component\BaseAssetComponent::generateChecksum', $path);
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', getcwd() . $path, true));
    }
}
