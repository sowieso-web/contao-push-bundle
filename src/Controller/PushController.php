<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Repository\PushRepository;
use Minishlink\WebPush\Subscription;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PushController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PushRepository
     */
    private $pushRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, PushRepository $pushRepository, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->pushRepository = $pushRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/_push/subscription", name="push_subscription", defaults={"_scope"="frontend", "_token_check"=false})
     */
    public function handleSubscription(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        if (!$payload) {
            return new Response('Please provide a valid JSON payload', Response::HTTP_BAD_REQUEST);
        }

        /** @var Push $push */
        $push = $this->pushRepository->findOneBy(['authToken' => $payload['authToken']]);
        $subscription = Subscription::create($payload);

        switch ($request->getMethod()) {
            case 'POST':
                $this->updatePush($subscription, $push);

                return new Response('1', Response::HTTP_CREATED);
            case 'PUT':
                $this->updatePush($subscription, $push);

                return new Response('1', Response::HTTP_OK);
            case 'DELETE':
                if ($push) {
                    $this->em->remove($push);

                    return new Response('', Response::HTTP_NO_CONTENT);
                }
        }

        return new Response('', Response::HTTP_METHOD_NOT_ALLOWED);
    }

    private function updatePush(Subscription $subscription, ?Push $push): void
    {
        if ($push === null) {
            $push = new Push();
        }

        $push->setAuthToken($subscription->getAuthToken());
        $push->setContentEncoding($subscription->getContentEncoding());
        $push->setEndpoint($subscription->getEndpoint());
        $push->setPublicKey($subscription->getPublicKey());

        $this->em->persist($push);
        $this->em->flush();
    }
}
