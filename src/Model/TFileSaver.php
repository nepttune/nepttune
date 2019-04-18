<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\Model;

trait TFileSaver
{
    protected static $filePath = '/../data/';

    public function saveFile(\Nette\Http\FileUpload $file, string $subPath = '') : string
    {
        if (!$file->isOk())
        {
            throw new \Nette\Application\BadRequestException('Bad request', 400);
        }

        $name = $file->getSanitizedName();
        $extension = \substr($name, \strrpos($name, '.') + 1);

        do
        {
            $fileName = ($subPath ? $subPath . '/' : '') . \Nette\Utils\Random::generate(10) . '.' . $extension;
            $filePath = static::getFilePath($fileName);
        }
        while(\file_exists($filePath));

        $file->move($filePath);

        return $fileName;
    }

    public function saveImage(\Nette\Http\FileUpload $image, string $subPath = '') : string
    {
        if (!$image->isImage())
        {
            throw new \Nette\Application\BadRequestException('Bad request', 400);
        }

        return $this->saveFile($image, $subPath);
    }

    public static function getFilePath(string $file) : string
    {
        if (\mb_substr($file, 0, 6) === 'static') {
            return \getcwd() . '/' . $file;
        }

        return \getcwd() . self::$filePath . $file;
    }
}
