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

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Contao\DataContainer;
use Contao\Message;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentContainer
{
    public const TABLE = 'tl_content';

    public function __construct(
        private readonly FinderFactory       $templateLocator,
        private readonly TranslatorInterface $translator,
        private readonly Utils               $utils,
    ) {}

    #[AsCallback(self::TABLE, 'config.onload')]
    public function onLoadCallback(DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        if (!$contentModel = ContentModel::findByPk($dc->id)) {
            return;
        }

        if ($contentModel->type !== InstallPwaButtonElementController::TYPE) {
            return;
        }

        $fields = &$GLOBALS['TL_DCA']['tl_content']['fields'];
        $lang = $GLOBALS['TL_LANG']['tl_content'];

        $fields['linkTitle']['label'][1] = $lang['linkTitle']['pwa_install_button'];
        $fields['text']['label'][0] = $lang['text']['pwa_0'];
        $fields['text']['label'][1] = $lang['text']['pwa_1'];
        $fields['text']['eval']['mandatory'] = false;

        if ($contentModel->ptable !== ArticleModel::getTable()
            || !($article = ArticleModel::findByPk($contentModel->pid)))
        {
            return;
        }

        if (!$page = $article->getRelated('pid')) {
            return;
        }

        if (!$rootPage = $this->utils->request()->getCurrentRootPageModel($page)) {
            return;
        }

        if ($rootPage->addPwa !== PageContainer::ADD_PWA_YES)
        {
            Message::addInfo($this->translator->trans('huh.pwa.backend.message.disabled'));
        }

        if (!$config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration)) {
            return;
        }

        if (!$config->hideInstallPrompt)
        {
            Message::addInfo($this->translator->trans('huh.pwa.backend.message.hideInstallNotEnabled'));
        }
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
}