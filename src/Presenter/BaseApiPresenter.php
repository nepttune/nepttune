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

namespace Nepttune\Presenter;

abstract class BaseApiPresenter implements \Nette\Application\IPresenter
{
    use \Nette\SmartObject;

    const ACTION_KEY = 'action';
    const DEFAULT_ACTION = 'default';

    /** @var \Nette\DI\Container */
    protected $context;

    /** @var \Nette\Http\IRequest */
    private $httpRequest;

    /** @var  \Nette\Http\IResponse */
    private $httpResponse;

    /** @var  \Nette\Application\Request */
    private $request;

    /** @var  \Nette\Application\IResponse */
    private $response;

    /** @var  string */
    protected $name;

    /** @var  string */
    protected $action;

    /** @var  \stdClass */
    public $payload;

    public function injectPrimary(
        \Nette\DI\Container $context,
        \Nette\Http\IRequest $httpRequest,
        \Nette\Http\IResponse $httpResponse)
    {
        $this->context = $context;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
    }

    public function run(\Nette\Application\Request $request) : \Nette\Application\IResponse
    {
        $this->request = $request;
        $this->payload = new \stdClass();
        $this->name = $request->getPresenterName();
        $this->action = $request->getParameters()[static::ACTION_KEY] ?? static::DEFAULT_ACTION;
        unset($request->parameters[static::ACTION_KEY]);

        try
        {
            $this->startup();
            $this->tryCall(static::ACTION_KEY . ucfirst($this->action), $request->getParameters());
        }
        catch (\Nette\Application\AbortException $e)
        {}

        if (!$this->response)
        {
            $this->error('Page not found. No response given.');
        }

        return $this->response;
    }

    public function startup()
    {
    }

    public function sendPayload()
    {
        $this->sendJson($this->payload);
    }

    public function sendJson($data)
    {
        $this->sendResponse(new \Nette\Application\Responses\JsonResponse($data));
    }

    public function sendResponse(\Nette\Application\IResponse $response) : void
    {
        $this->response = $response;
        throw new \Nette\Application\AbortException();
    }

    public function getHttpRequest() : \Nette\Http\IRequest
    {
        return $this->httpRequest;
    }

    public function getHttpResponse() : \Nette\Http\IResponse
    {
        return $this->httpResponse;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getAction() : string
    {
        return $this->action;
    }

    public function error($message = null, $httpCode = \Nette\Http\IResponse::S404_NOT_FOUND)
    {
        throw new \Nette\Application\BadRequestException($message, $httpCode);
    }
    
    protected function tryCall($method, array $params) : bool
    {
        $rc = new \Nette\Application\UI\ComponentReflection($this);
        if ($rc->hasMethod($method)) {
            $rm = $rc->getMethod($method);
            if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
                $rm->invokeArgs($this, $rc::combineArgs($rm, $params));
                return true;
            }
        }
        return false;
    }
}
