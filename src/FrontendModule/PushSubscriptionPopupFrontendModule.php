<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\FrontendModule;


use Contao\Module;
use Contao\System;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRenderer;
use Symfony\Component\HttpFoundation\RequestStack;

class PushSubscriptionPopupFrontendModule extends Module
{
    const TYPE = 'huh_pwa_push_popup';

    protected $strTemplate = 'mod_push_subscription_popup';

    protected function compile()
    {
        $container = System::getContainer();
        $buttonTemplate = $this->pwaSubscribeButtonTemplate ?: 'subscribe_button_default';

        $this->Template->button = $container->get(TwigTemplateRenderer::class)->render($buttonTemplate);
    }
}