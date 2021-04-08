<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
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
    private EntityManagerInterface $em;
    private PushRepository $pushRepository;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, PushRepository $pushRepository, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->pushRepository = $pushRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/_push/subscription", name="push_subscription", defaults={"_scope"="frontend", "_token_check"=false})
     *
     * @param Request $request
     *
     * @throws \ErrorException
     *
     * @return Response
     */
    public function handleSubscription(Request $request): Response
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $payload = null;
        }

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

    /**
     * @param Subscription $subscription
     * @param Push|null    $push
     */
    private function updatePush(Subscription $subscription, ?Push $push): void
    {
        if (null === $push) {
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
