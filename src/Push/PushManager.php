<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Push;

use Contao\CoreBundle\Monolog\ContaoContext;
use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Repository\PushRepository;

class PushManager
{
    /**
     * @var WebPush
     */
    private $push;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PushRepository
     */
    private $pushRepository;

    public function __construct(WebPush $push, EntityManagerInterface $em, PushRepository $pushRepository, LoggerInterface $logger)
    {
        $this->push = $push;
        $this->em = $em;
        $this->pushRepository = $pushRepository;
        $this->logger = $logger;
    }

    public function sendNotification(string $title, string $body, $data = []): void
    {
        $subscriptions = $this->pushRepository->findAll();

        $payload = \json_encode([
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        /** @var array<Push> $sub */
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create($sub->toArray());
            $this->push->sendNotification($subscription, $payload);
        }

        $this->flushNotifications();
    }

    private function flushNotifications(): void
    {
        $context = [
            'contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL),
        ];

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
