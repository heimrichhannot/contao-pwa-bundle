<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Contao\FrontendModule;

use Contao\FilesModel;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;

class PushSubscriptionPopupModule extends Module
{
    public const TYPE = 'huh_pwa_push_popup';
    public const TOGGLE_EVENT = 'event';
    public const TOGGLE_CUSTOM = 'custom';

    protected $strTemplate = 'mod_push_subscription_popup';

    protected function compile(): void
    {
        $container = System::getContainer();
        $twig = $container->get('twig');

        $templateData = [
            'headline' => $this->Template->headline,
            'hl' => $this->Template->hl,
            'text' => $this->pwaText,
            'openOnInit' => (static::TOGGLE_EVENT === $this->pwaPopupToggle),
            'singleSRC' => $this->singleSRC,
        ];

        $buttonTemplate = \sprintf(
            '@Contao/%s.html.twig',
            $this->pwaSubscribeButtonTemplate ?: 'content_element/pwa_subscribe_button'
        );
        $templateData['button'] = $twig?->render($buttonTemplate) ?: '';

        $cssId = $this->cssID[0];
        if (empty($cssId)) {
            $cssId = 'mod_' . $this->type.'_'.$this->id;
        }
        $templateData['cssId'] = $cssId;

        // Add an image
        if ($this->addImage && $this->singleSRC && ($filesModel = FilesModel::findByUuid($this->singleSRC)))
        {
            $this->Template->addImage = true;
            $this->Template->singleSRC = $this->singleSRC;
            $this->Template->image = $filesModel;
            $this->Template->imgSize = StringUtil::deserialize($this->imgSize);
        }

        $modalTemplate = \sprintf(
            '@Contao/%s.html.twig',
            $this->pwaPopupTemplate ?: 'pwa/push_subscription_popup'
        );

        $this->Template->popup = $twig?->render($modalTemplate, $templateData) ?: '';

        $this->cssID[0] = $cssId.'_wrapper';
    }
}