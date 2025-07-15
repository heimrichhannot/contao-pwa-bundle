<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\DataContainer;


use Contao\DataContainer;
use Contao\DC_Table;
use Contao\ModuleModel;
use HeimrichHannot\PwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule;
use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;

class ModuleContainer
{
    /**
     * @var TwigTemplateLocator
     */
    protected $templateLocator;

    /**
     * ContentContainer constructor.
     */
    public function __construct(TwigTemplateLocator $templateLocator)
    {
        $this->templateLocator = $templateLocator;
    }

    /**
     * @param DataContainer|DC_Table|null $dc
     */
    public function onLoadCallback(?DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }
        $module = ModuleModel::findByPk($dc->id);
        if (!$module || PushSubscriptionPopupFrontendModule::TYPE !== $module->type) {
            return;
        }
        $GLOBALS['TL_DCA']['tl_module']['subpalettes']['addImage'] = 'singleSRC,imgSize';
    }

    public function onPwaSubscribeButtonTemplateOptionsCallback(): array
    {
        return $this->templateLocator->getTemplateGroup('subscribe_button_');
    }

    public function onPwaPopupTemplateOptionsCallback(): array
    {
        return $this->templateLocator->getTemplateGroup('push_subscription_popup_');
    }
}