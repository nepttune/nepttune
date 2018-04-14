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

/**
 * This file uses modified code snippets from Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types = 1);

namespace Nepttune\Presenter;

use Nette\Application\Helpers;
use Nette\Application\Request;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\ComponentReflection;

trait TLink
{
    /** @var bool  use absolute Urls or paths? */
    public $absoluteUrls = false;

    /** @var int */
    public $invalidLinkMode;

    /** @var \Nette\Application\IRouter */
    private $router;

    /** @var  \Nette\Application\IPresenterFactory */
    private $presenterFactory;

    /** @var Request|null */
    private $lastCreatedRequest;

    /** @var array */
    private $lastCreatedRequestFlag;

    /** @var \Nette\Http\Url */
    private $refUrlCache;

    public function injectLink(
        \Nette\Application\IRouter $router,
        \Nette\Application\IPresenterFactory $presenterFactory) : void
    {
        $this->router = $router;
        $this->presenterFactory = $presenterFactory;
    }

    public function getLastCreatedRequest()
    {
        return $this->lastCreatedRequest;
    }

    public function getLastCreatedRequestFlag($flag) : bool
    {
        return !empty($this->lastCreatedRequestFlag[$flag]);
    }

    public function link($destination, array $args = []) : string
    {
        try {
            $args = \func_num_args() < 3 && \is_array($args) ? $args : \array_slice(\func_get_args(), 1);
            return $this->createRequest($this, $destination, $args, 'link');

        } catch (InvalidLinkException $e) {
            return $this->handleInvalidLink($e);
        }
    }

    protected function createRequest($component, $destination, array $args, $mode)
    {
        // note: createRequest supposes that saveState(), run() & tryCall() behaviour is final

        $this->lastCreatedRequest = $this->lastCreatedRequestFlag = null;

        // PARSE DESTINATION
        // 1) fragment
        $a = strpos($destination, '#');
        if ($a === false) {
            $fragment = '';
        } else {
            $fragment = substr($destination, $a);
            $destination = substr($destination, 0, $a);
        }

        // 2) ?query syntax
        $a = strpos($destination, '?');
        if ($a !== false) {
            parse_str(substr($destination, $a + 1), $args);
            $destination = substr($destination, 0, $a);
        }

        // 3) URL scheme
        $a = strpos($destination, '//');
        if ($a === false) {
            $scheme = false;
        } else {
            $scheme = substr($destination, 0, $a);
            $destination = substr($destination, $a + 2);
        }

        // 4) signal or empty
        if (!$component instanceof self || substr($destination, -1) === '!') {
            list($cname, $signal) = Helpers::splitName(rtrim($destination, '!'));
            if ($cname !== '') {
                $component = $component->getComponent(strtr($cname, ':', '-'));
            }
            if ($signal === '') {
                throw new InvalidLinkException('Signal must be non-empty string.');
            }
            $destination = 'this';
        }

        if ($destination == null) {  // intentionally ==
            throw new InvalidLinkException('Destination must be non-empty string.');
        }

        // 5) presenter: action
        $current = false;
        list($presenter, $action) = Helpers::splitName($destination);
        if ($presenter === '') {
            $action = $destination === 'this' ? $this->action : $action;
            $presenter = $this->getName();
            $presenterClass = \get_class($this);

        } else {
            if ($presenter[0] === ':') { // absolute
                $presenter = substr($presenter, 1);
                if (!$presenter) {
                    throw new InvalidLinkException("Missing presenter name in '$destination'.");
                }
            } else { // relative
                list($module, , $sep) = Helpers::splitName($this->getName());
                $presenter = $module . $sep . $presenter;
            }
            if (!$this->presenterFactory) {
                throw new \Nette\InvalidStateException('Unable to create link to other presenter, service PresenterFactory has not been set.');
            }
            try {
                $presenterClass = $this->presenterFactory->getPresenterClass($presenter);
            } catch (\Nette\Application\InvalidPresenterException $e) {
                throw new InvalidLinkException($e->getMessage(), 0, $e);
            }
        }

        // PROCESS SIGNAL ARGUMENTS
        if (isset($signal)) { // $component must be IStatePersistent
            $reflection = new ComponentReflection(get_class($component));
            if ($signal === 'this') { // means "no signal"
                $signal = '';
                if (array_key_exists(0, $args)) {
                    throw new InvalidLinkException("Unable to pass parameters to 'this!' signal.");
                }

            } elseif (strpos($signal, self::NAME_SEPARATOR) === false) {
                // counterpart of signalReceived() & tryCall()
                $method = $component->formatSignalMethod($signal);
                if (!$reflection->hasCallableMethod($method)) {
                    throw new InvalidLinkException("Unknown signal '$signal', missing handler {$reflection->getName()}::$method()");
                }
                // convert indexed parameters to named
                self::argsToParams(\get_class($component), $method, $args, [], $missing);
            }

            // counterpart of IStatePersistent
            if ($args && array_intersect_key($args, $reflection->getPersistentParams())) {
                $component->saveState($args);
            }

            if ($args && $component !== $this) {
                $prefix = $component->getUniqueId() . self::NAME_SEPARATOR;
                foreach ($args as $key => $val) {
                    unset($args[$key]);
                    $args[$prefix . $key] = $val;
                }
            }
        }

        // PROCESS ARGUMENTS
        if (is_subclass_of($presenterClass, __CLASS__)) {
            if ($action === '') {
                $action = self::DEFAULT_ACTION;
            }

            $current = ($action === '*' || strcasecmp($action, $this->action) === 0) && $presenterClass === get_class($this);

            $reflection = new ComponentReflection($presenterClass);

            // counterpart of run() & tryCall()
            $method = $presenterClass::formatActionMethod($action);
            if (!$reflection->hasCallableMethod($method)) {
                $method = $presenterClass::formatRenderMethod($action);
                if (!$reflection->hasCallableMethod($method)) {
                    $method = null;
                }
            }

            // convert indexed parameters to named
            if ($method === null) {
                if (array_key_exists(0, $args)) {
                    throw new InvalidLinkException("Unable to pass parameters to action '$presenter:$action', missing corresponding method.");
                }
            } else {
                self::argsToParams($presenterClass, $method, $args, $destination === 'this' ? $this->params : [], $missing);
            }

            // counterpart of IStatePersistent
            if ($args && array_intersect_key($args, $reflection->getPersistentParams())) {
                $this->saveState($args, $reflection);
            }

            if ($mode === 'redirect') {
                $this->saveGlobalState();
            }

            $globalState = $this->getGlobalState($destination === 'this' ? null : $presenterClass);
            if ($current && $args) {
                $tmp = $globalState + $this->params;
                foreach ($args as $key => $val) {
                    if (http_build_query([$val]) !== (isset($tmp[$key]) ? http_build_query([$tmp[$key]]) : '')) {
                        $current = false;
                        break;
                    }
                }
            }
            $args += $globalState;
        }

        if ($mode !== 'test' && !empty($missing)) {
            foreach ($missing as $rp) {
                if (!array_key_exists($rp->getName(), $args)) {
                    throw new InvalidLinkException("Missing parameter \${$rp->getName()} required by {$rp->getDeclaringClass()->getName()}::{$rp->getDeclaringFunction()->getName()}()");
                }
            }
        }

        // ADD ACTION & SIGNAL & FLASH
        if ($action) {
            $args[self::ACTION_KEY] = $action;
        }
        if (!empty($signal)) {
            $args[self::SIGNAL_KEY] = $component->getParameterId($signal);
            $current = $current && $args[self::SIGNAL_KEY] === $this->getParameter(self::SIGNAL_KEY);
        }
        if (($mode === 'redirect' || $mode === 'forward') && $this->hasFlashSession()) {
            $args[self::FLASH_KEY] = $this->getFlashKey();
        }

        $this->lastCreatedRequest = new Request($presenter, Request::FORWARD, $args);
        $this->lastCreatedRequestFlag = ['current' => $current];

        return $mode === 'forward' || $mode === 'test'
            ? null
            : $this->requestToUrl($this->lastCreatedRequest, $mode === 'link' && $scheme === false && !$this->absoluteUrls) . $fragment;
    }

    protected function requestToUrl(Request $request, $relative = null)
    {
        if ($this->refUrlCache === null) {
            $this->refUrlCache = new \Nette\Http\Url($this->getHttpRequest()->getUrl());
            $this->refUrlCache->setPath($this->getHttpRequest()->getUrl()->getScriptPath());
        }
        if (!$this->router) {
            throw new \Nette\InvalidStateException('Unable to generate URL, service Router has not been set.');
        }

        $url = $this->router->constructUrl($request, $this->refUrlCache);
        if ($url === null) {
            $params = $request->getParameters();
            unset($params[self::ACTION_KEY]);
            $params = urldecode(http_build_query($params, '', ', '));
            throw new InvalidLinkException("No route for {$request->getPresenterName()}:{$request->getParameter('action')}($params)");
        }

        if ($relative === null ? !$this->absoluteUrls : $relative) {
            $hostUrl = $this->refUrlCache->getHostUrl() . '/';
            if (strncmp($url, $hostUrl, \strlen($hostUrl)) === 0) {
                $url = substr($url, \strlen($hostUrl) - 1);
            }
        }

        return $url;
    }

    protected function handleInvalidLink(InvalidLinkException $e)
    {
        if ($this->invalidLinkMode & ILinkPresenter::INVALID_LINK_EXCEPTION) {
            throw $e;
        } elseif ($this->invalidLinkMode & ILinkPresenter::INVALID_LINK_WARNING) {
            trigger_error('Invalid link: ' . $e->getMessage(), E_USER_WARNING);
        }
        return ($this->invalidLinkMode & ILinkPresenter::INVALID_LINK_TEXTUAL)
            ? '#error: ' . $e->getMessage()
            : '#';
    }

    public static function argsToParams($class, $method, &$args, array $supplemental = [], array &$missing = [])
    {
        $i = 0;
        $rm = new \ReflectionMethod($class, $method);
        foreach ($rm->getParameters() as $param) {
            list($type, $isClass) = ComponentReflection::getParameterType($param);
            $name = $param->getName();

            if (array_key_exists($i, $args)) {
                $args[$name] = $args[$i];
                unset($args[$i]);
                $i++;

            } elseif (array_key_exists($name, $args)) {
                // continue with process

            } elseif (array_key_exists($name, $supplemental)) {
                $args[$name] = $supplemental[$name];
            }

            if (!isset($args[$name])) {
                if (!$param->isDefaultValueAvailable() && !$param->allowsNull() && $type !== 'NULL' && $type !== 'array') {
                    $missing[] = $param;
                    unset($args[$name]);
                }
                continue;
            }

            if (!ComponentReflection::convertType($args[$name], $type, $isClass)) {
                throw new InvalidLinkException(sprintf(
                    'Argument $%s passed to %s() must be %s, %s given.',
                    $name,
                    $rm->getDeclaringClass()->getName() . '::' . $rm->getName(),
                    $type === 'NULL' ? 'scalar' : $type,
                    \is_object($args[$name]) ? \get_class($args[$name]) : \gettype($args[$name])
                ));
            }

            $def = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            if ($args[$name] === $def || ($def === null && $args[$name] === '')) {
                $args[$name] = null; // value transmit is unnecessary
            }
        }

        if (array_key_exists($i, $args)) {
            throw new InvalidLinkException("Passed more parameters than method $class::{$rm->getName()}() expects.");
        }
    }
}
