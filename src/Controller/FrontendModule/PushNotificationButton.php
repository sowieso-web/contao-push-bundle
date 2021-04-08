<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PushNotificationButton extends AbstractFrontendModuleController
{
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->bag = $bag;
    }

    public function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $publicKey = $this->bag->get('minishlink_web_push.auth')['VAPID']['publicKey'];

        // insert public key into DOM if the script has not been added before
        if (!\is_array($GLOBALS['TL_BODY']) || !\array_key_exists('contao_push', $GLOBALS['TL_BODY'])) {
            $GLOBALS['TL_BODY']['contao_push_key'] = sprintf("<script>const applicationServerKey = '%s';</script>", $publicKey);
        }

        $GLOBALS['TL_BODY']['contao_push'] = Template::generateScriptTag('/bundles/contaopush/main.min.js');

        return $template->getResponse();
    }
}
