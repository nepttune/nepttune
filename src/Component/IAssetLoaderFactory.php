<?php

namespace Nepttune\Component;

interface IAssetLoaderFactory
{
    /** @return AssetLoader */
    public function create();
}
