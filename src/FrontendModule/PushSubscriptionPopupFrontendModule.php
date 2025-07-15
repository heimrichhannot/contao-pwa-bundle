<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\FrontendModule;


use Contao\Controller;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRenderer;

class PushSubscriptionPopupFrontendModule extends Module
{
    const TYPE = 'huh_pwa_push_popup';

    const TOGGLE_EVENT = 'event';
    const TOGGLE_CUSTOM = 'custom';

    protected $strTemplate = 'mod_push_subscription_popup';

    protected function compile()
    {
        $container = System::getContainer();
        $templateData = [
            'headline' => $this->Template->headline,
            'hl' => $this->Template->hl,
            'text' => $this->pwaText,
            'openOnInit' => (static::TOGGLE_EVENT === $this->pwaPopupToggle),
            'singleSRC' => $this->singleSRC,
        ];

        $buttonTemplate = $this->pwaSubscribeButtonTemplate ?: 'subscribe_button_default';
        $templateData['button'] = $container->get(TwigTemplateRenderer::class)->render($buttonTemplate);

        $cssId = $this->cssID[0];
        if (empty($cssId)) {
            $cssId = 'mod_' . $this->type.'_'.$this->id;
        }
        $templateData['cssId'] = $cssId;

        // Add an image
        if ($this->addImage && $this->singleSRC != '')
        {
            $filesModel = FilesModel::findByUuid($this->singleSRC);
            $template = new FrontendTemplate('image');
            Controller::addImageToTemplate($template, [
                'singleSRC' => $filesModel->path,
                'size' => $this->imgSize,
            ], null, null, $filesModel);
            $image = $template->getData();
            $image['buffer'] = $template->parse();
            $image['size'] = StringUtil::deserialize($this->imgSize);
            $templateData['image'] = $image;
        }

        $modalTemplate = $this->pwaPopupTemplate ?: 'push_subscription_popup_default';

        $this->Template->popup = $container->get(TwigTemplateRenderer::class)->render($modalTemplate, $templateData);

        $this->cssID[0] = $cssId.'_wrapper';
    }
}