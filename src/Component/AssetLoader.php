<?php

namespace Nepttune\Component;

final class AssetLoader extends BaseComponent
{
    /** @var \Kdyby\Redis\RedisStorage */
    public $storage;

    public function __construct(\Kdyby\Redis\RedisStorage $redisStorage)
    {
        $this->storage = $redisStorage;
    }

    public function renderScript()
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__ . '/Script.latte');
        $this->template->render();
    }

    public function renderStyle()
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__ . '/Style.latte');
        $this->template->render();
    }

    public function renderStyleComponent()
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__ . '/Style_component.latte');
        $this->template->render();
    }

    public function getIntegrity(string $path) : string
    {
        $cache = new \Nette\Caching\Cache($this->storage);

        return $cache->call('Nepttune\Component\AssetLoader::generateChecksum', $path);
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', getcwd() . $path, true));
    }
}
