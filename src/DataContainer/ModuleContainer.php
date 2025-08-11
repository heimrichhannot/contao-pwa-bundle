<?php
/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Contao\DataContainer;
use Contao\ModuleModel;
use HeimrichHannot\PwaBundle\Contao\FrontendModule\PushSubscriptionPopupModule;

class ModuleContainer
{
    public const TABLE = 'tl_module';

    public function __construct(
        private readonly FinderFactory $templateLocator
    ) {}

    #[AsCallback(self::TABLE, 'config.onload')]
    public function onLoadCallback(?DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id)
        {
            return;
        }
        $module = ModuleModel::findByPk($dc->id);
        if (!$module || PushSubscriptionPopupModule::TYPE !== $module->type)
        {
            return;
        }
        $GLOBALS['TL_DCA']['tl_module']['subpalettes']['addImage'] = 'singleSRC,imgSize';
    }

    #[AsCallback(self::TABLE, 'fields.pwaSubscribeButtonTemplate.options')]
    public function onPwaSubscribeButtonTemplateOptionsCallback(): array
    {
        return $this->templateLocator->create()
            ->identifier('pwa/subscribe_button')
            ->extension('html.twig')
            ->withVariants()
            ->asTemplateOptions();
    }

    #[AsCallback(self::TABLE, 'fields.pwaPopupTemplate.options')]
    public function onPwaPopupTemplateOptionsCallback(): array
    {
        return $this->templateLocator->create()
            ->identifier('pwa/push_subscription_popup')
            ->extension('html.twig')
            ->withVariants()
            ->asTemplateOptions();
    }
}