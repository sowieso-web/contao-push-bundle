<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\NewsModel;
use Dreibein\ContaoPushBundle\Push\PushManager;
use Symfony\Component\HttpFoundation\RequestStack;

class News
{
    /**
     * @var PushManager
     */
    private $manager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * News constructor.
     *
     * @param PushManager $manager
     */
    public function __construct(PushManager $manager, RequestStack $requestStack, ContaoFramework $framework)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->framework = $framework;
    }

    public function onLoad($dc): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request->query->get('sendPush')) {
            return;
        }

        $this->framework->initialize();
        $controller = $this->framework->getAdapter(Controller::class);
        $adapter = $this->framework->getAdapter(NewsModel::class);

        $model = $adapter->findByPk($dc->id);

        $title = $model->headline;
        $body = $model->subheadline;
        $url = sprintf(
            '%s/%s',
            $request->getSchemeAndHttpHost(),
            $controller->replaceInsertTags(sprintf('{{news_url::%s}}', $dc->id))
        );

        $this->manager->sendNotification($title, $body, ['url' => $url]);
    }
}
