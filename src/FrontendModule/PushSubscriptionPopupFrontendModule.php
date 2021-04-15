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

class PushSubscriptionPopupFrontendModule extends Module
{
    const TYPE = 'huh_pwa_push_popup';

    const TOGGLE_EVENT = 'event';

    protected $strTemplate = 'mod_push_subscription_popup';

    protected function compile()
    {
        $container = System::getContainer();

        $buttonTemplate = $this->pwaSubscribeButtonTemplate ?: 'subscribe_button_default';

        $buttonBuffer = $container->get(TwigTemplateRenderer::class)->render($buttonTemplate);

        $modalTemplate = $this->pwaPopupTemplate ?: 'push_subscription_popup_default';

        $cssId = $this->cssID[0];
        if (empty($cssId)) {
            $cssId = 'mod_' . $this->type.'_'.$this->id;
        }

        $this->Template->popup = $container->get(TwigTemplateRenderer::class)->render($modalTemplate, [
            'button' => $buttonBuffer,
            'headline' => $this->Template->headline,
            'hl' => $this->Template->hl,
            'text' => $this->pwaText,
            'cssId' => $cssId,
        ]);

        $this->cssID[0] = $cssId.'_wrapper';
    }
}