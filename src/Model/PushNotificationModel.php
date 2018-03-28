<?php

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
     * @param $msg
     */
    public function sendAll($msg) : void
    {
        foreach ($this->subscriptionModel->getActive() as $row)
        {
            $this->sendNotification($row, $msg, false);
        }

        $this->flush();
    }

    /**
     * Send notification to all subscriptions paired with specific user.
     * @param $msg
     * @param $userId
     * @param bool $flush
     */
    public function sendByUserId(string $msg, int $userId, bool $flush = false) : void
    {
        foreach ($this->subscriptionModel->getActive()->where('user_id', $userId) as $row)
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
     * @param $msg
     * @param $typeId
     * @param bool $flush
     */
    public function sendByType(string $msg, int $typeId, bool $flush = false) : void
    {
        $subscriptions = $this->subscriptionModel->getTable()
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
     * @param $userId
     */
    public function saveSubscription(int $userId = null) : void
    {
        $json = file_get_contents("php://input");

        if (!$json)
        {
            return;
        }

        $data = json_decode($json, true);

        if (!$data || empty($data['endpoint']) || empty($data['publicKey']) || empty($data['authToken']))
        {
            return;
        }

        switch ($this->request->getMethod()) {
            case 'POST':
                $this->subscriptionModel->insert([
                    'user_id' => $userId,
                    'endpoint' => $data['endpoint'],
                    'key' => $data['publicKey'],
                    'token' => $data['authToken'],
                    'encoding' => $data['contentEncoding']
                ]);
                break;
            case 'PUT':
                $row = $this->subscriptionModel->getActive()->where('endpoint', $data['endpoint'])->fetch();

                if (!$row)
                {
                    $this->subscriptionModel->insert([
                        'user_id' => $userId,
                        'endpoint' => $data['endpoint'],
                        'key' => $data['publicKey'],
                        'token' => $data['authToken'],
                        'encoding' => $data['contentEncoding']
                    ]);
                    break;
                }

                $row->update([
                    'user_id' => $userId ?: $row->user_id ?: null,
                    'key' => $data['publicKey'],
                    'token' => $data['authToken'],
                    'encoding' => $data['contentEncoding']
                ]);
                break;
            case 'DELETE':
                $this->subscriptionModel->findBy('endpoint', $data['endpoint'])->update([
                    'active' => 0
                ]);
                return;
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
