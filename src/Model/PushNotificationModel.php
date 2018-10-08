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

final class PushNotificationModel
{
    /** @var  WebPush */
    private $webPush;

    /** @var  SubscriptionModel */
    private $subscriptionModel;

    /** @var  \Nette\Http\Request */
    private $request;

    public function __construct(array $parameters, SubscriptionModel $subscriptionModel, \Nette\Http\Request $request)
    {
        $auth = ['VAPID' => $parameters];

        $this->webPush = new WebPush($auth);
        $this->subscriptionModel = $subscriptionModel;
        $this->request = $request;
    }

    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Send notification to all active subscriptions.
     * @param string $msg
     */
    public function sendAll($msg) : void
    {
        foreach ($this->subscriptionModel->findActive() as $row)
        {
            $this->sendNotification($row, $msg, false);
        }

        $this->flush();
    }

    /**
     * Send notification to all subscriptions paired with specific user.
     * @param string $msg
     * @param int $userId
     * @param bool $flush
     */
    public function sendByUserId(string $msg, int $userId, bool $flush = false) : void
    {
        foreach ($this->subscriptionModel->findActive()->where('user_id', $userId) as $row)
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
     * @param string $msg
     * @param int $typeId
     * @param bool $flush
     */
    public function sendByType(string $msg, int $typeId, bool $flush = false) : void
    {
        $subscriptions = $this->subscriptionModel->findAll()
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

        $row = $this->subscriptionModel->findActive()->where('endpoint', $data['endpoint'])->fetch();

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

                $row = $this->subscriptionModel->insert([
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

    private function flush() : void
    {
        $this->webPush->flush();
    }
}
