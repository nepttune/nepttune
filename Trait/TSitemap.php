<?php

namespace Nepttune\Trait;

trait TSitemap
{
    public function getSitemap() : array
    {
        $pages = [];

        /** @var \Nette\Application\UI\ComponentReflection $reflection */
        $reflection = $this->getReflection();

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if ($method->class !== $reflection->getName() || substr($method->name, 0, 6) !== 'action')
            {
                continue;
            }

            if ($method->hasAnnotation('sitemap'))
            {
                $regex = '/App\\\\([A-Z][a-z]*)Module\\\\Presenter\\\\([A-Z][a-z]*)Presenter/';
                $matches = [];

                preg_match($regex, $reflection->name, $matches);

                if (\count($matches) < 3)
                {
                    continue;
                }

                $pages[] = ":{$matches[1]}:{$matches[2]}:" . lcfirst(substr($method->name, 6));
            }
        }

        return $pages;
    }
}
