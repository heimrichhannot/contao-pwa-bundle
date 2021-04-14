<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Generator;


use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;

class PushSubscribeButtonGenerator
{
    protected TwigTemplateLocator $twigTemplateLocator;

    /**
     * PushSubscribeButtonGenerator constructor.
     */
    public function __construct(TwigTemplateLocator $twigTemplateLocator)
    {
        $this->twigTemplateLocator = $twigTemplateLocator;
    }

    public function generate(string $templateName = 'subscribe_button_default'): string
    {
        $this->twigTemplateLocator->

        $templatePath = $this->templateUtil->getTemplate($template);

        try
        {
            $this->Template->button = $this->twig->render($templatePath);
        } catch (\Twig_Error $e){
            $this->Template->button = '<button class="huhPwaWebSubscription" disabled="disabled"><span class="label">Push Notifications</span></button>';
        }
    }
}