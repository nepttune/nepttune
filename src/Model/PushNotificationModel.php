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

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

final class PushNotificationModel extends BaseModel
{
    const TABLE_NAME = 'subscription';

    /** @var  WebPush */
    private $webPush;

    /** @var  \Nette\Http\Request */
    private $request;

    public function __construct(array $parameters, \Nette\Http\Request $request)
    {
        parent::__construct();

        $this->webPush = new WebPush(['VAPID' => $parameters]);
        $this->request = $request;
    }

    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Output msg queue
     */
    public function flush() : void
    {
        $this->webPush->flush();
    }

    /**
     * Send notification to all active subscriptions.
     * @param string $msg
     */
    public function sendAll($msg) : void
    {
        foreach ($this->findActive() as $row)
        {
            $this->sendNotification($row, $msg, false);
        }

        if ($flush)
        {
            $this->flush();
        }
    }

    /**
     * Send notification to all subscriptions paired with specific user.
     * @param int $userId
     * @param string $msg
     * @param string $dest
     * @param bool $flush
     */
    public function sendByUserId(int $userId, string $msg, ?string $dest = null, bool $flush = false) : void
    {
        $msg = $this->composeMsg($msg, $dest);

        foreach ($this->findActive()->where('user_id', $userId) as $row)
        {
            $this->sendNotification($row, $msg, false);
        }

        if ($flush)
        {
            $this->flush();
        }
    }

    /**
     * Send notification to all users subscribed to specific type.
     * @param int $typeId
     * @param string $msg
     * @param string $dest
     * @param bool $flush
     */
    public function sendByType(int $typeId, string $msg, ?string $dest = null, bool $flush = false) : void
    {
        $msg = $this->composeMsg($msg, $dest);

        $subscriptions = $this->findAll()
            ->where('subscription.active', 1)
            ->where('user.active', 1)
            ->where('user:user_subscription_type.subscription_type_id', $typeId);

        foreach ($subscriptions as $row)
        {
            $this->sendNotification($row, $msg, false);
        }

        if ($flush)
        {
            $this->flush();
        }
    }

    /**
     * Save or update subscriber information.
     * @throws \Nette\Application\BadRequestException
     * @param int $userId
     */
    public function saveSubscription(int $userId = null) : void
    {
        $json = file_get_contents('php://input');

        if (!$json)
        {
            return;
        }

        $data = json_decode($json, true);

        if (!$data || empty($data['endpoint']) || empty($data['publicKey']) || empty($data['authToken']))
        {
            return;
        }

        $row = $this->findActive()->where('endpoint', $data['endpoint'])->fetch();

        switch ($this->request->getMethod()) {
            case 'POST':
            case 'PUT':
            {
                if ($row)
                {
                    $row->update([
                        'user_id' => $userId ?: $row->user_id ?: null,
                        'key' => $data['publicKey'],
                        'token' => $data['authToken'],
                        'encoding' => $data['contentEncoding']
                    ]);
                    
                    return;
                }

                $row = $this->insert([
                    'user_id' => $userId,
                    'endpoint' => $data['endpoint'],
                    'key' => $data['publicKey'],
                    'token' => $data['authToken'],
                    'encoding' => $data['contentEncoding']
                ]);

                $this->sendNotification($row, 'Notifications enabled!', true);

                return;
            }
            case 'DELETE':
            {
                if ($row)
                {
                    $row->update([
                        'active' => 0
                    ]);
                }

                return;
            }
            default:
                throw new \Nette\Application\BadRequestException();
        }
    }

    private function sendNotification($row, string $msg, bool $flush = false) : void
    {
        $subscription = Subscription::create([
            'endpoint' => $row->endpoint,
            'publicKey' => $row->key,
            'authToken' => $row->token,
            'contentEncoding' => $row->encoding
        ]);

        $this->webPush->sendNotification($subscription, $msg, $flush);
    }

    private function composeMsg(string $msg, ?string $dest) : string
    {
        return $dest ? \json_encode(['text' => $msg, 'destination' => $dest]) : $msg;
    }
}
