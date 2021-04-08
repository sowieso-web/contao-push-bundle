<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Push;

use Contao\CoreBundle\Monolog\ContaoContext;
use Doctrine\ORM\EntityManagerInterface;
use Dreibein\ContaoPushBundle\Repository\PushRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;

class PushManager
{
    private WebPush $push;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private PushRepository $pushRepository;

    /**
     * PushManager constructor.
     *
     * @param WebPush                $push
     * @param EntityManagerInterface $em
     * @param PushRepository         $pushRepository
     * @param LoggerInterface        $logger
     */
    public function __construct(WebPush $push, EntityManagerInterface $em, PushRepository $pushRepository, LoggerInterface $logger)
    {
        $this->push = $push;
        $this->em = $em;
        $this->pushRepository = $pushRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $title
     * @param string $body
     * @param array  $data
     *
     * @throws \ErrorException|\JsonException
     */
    public function sendNotification(string $title, string $body, array $data = []): void
    {
        // find all subscriptions to send the notification
        $subscriptions = $this->pushRepository->findAll();

        $payload = \json_encode([
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ], \JSON_THROW_ON_ERROR);

        foreach ($subscriptions as $sub) {
            // Create the subscription data from the entity
            $subscription = Subscription::create($sub->toArray());

            // prepare the subscription for sending
            $this->push->sendNotification($subscription, $payload);
        }

        $this->flushNotifications();
    }

    /**
     * @throws \ErrorException
     */
    private function flushNotifications(): void
    {
        // Prepare the logging context for contao
        $context = [
            'contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL),
        ];

        // Loop over the generator to send the notifications to given uri
        foreach ($this->push->flush() as $report) {
            $endpoint = (string) $report->getRequest()->getUri();

            if ($report->isSuccess()) {
                $this->logger->info("Message sent successfully for subscription {$endpoint}.", $context);

                continue;
            }

            $this->logger->error("Message failed to sent for subscription {$endpoint}: {$report->getReason()}", $context);
        }
    }
}
