<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\DataContainer;


use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Message;
use HeimrichHannot\PwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentContainer
{
    /**
     * @var TwigTemplateLocator
     */
    protected $templateLocator;
    private Utils $utils;
    private TranslatorInterface $translator;

    /**
	 * ContentContainer constructor.
	 */
	public function __construct(Utils $utils, TranslatorInterface $translator)
	{
        $this->templateLocator = null;
        $this->utils = $utils;
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_content", target="config.onload")
     */
    public function onLoadCallback(DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $contentModel = ContentModel::findByPk($dc->id);
        if (!$contentModel) {
            return;
        }

        if ($contentModel->type === InstallPwaButtonElementController::TYPE) {
            $GLOBALS['TL_DCA']['tl_content']['fields']['linkTitle']['label'][1] = $GLOBALS['TL_LANG']['tl_content']['linkTitle']['pwa_install_button'];
            $GLOBALS['TL_DCA']['tl_content']['fields']['text']['eval']['mandatory'] = false;
            $GLOBALS['TL_DCA']['tl_content']['fields']['text']['label'][0] = $GLOBALS['TL_LANG']['tl_content']['text']['pwa_0'];
            $GLOBALS['TL_DCA']['tl_content']['fields']['text']['label'][1] = $GLOBALS['TL_LANG']['tl_content']['text']['pwa_1'];

            if ((ArticleModel::getTable() !== $contentModel->ptable) || !($articleModel = ArticleModel::findByPk($contentModel->pid))) {
                return;
            }
            if (!($pageModel = $articleModel->getRelated('pid'))) {
                return;
            }
            if (!($rootPageModel = $this->utils->request()->getCurrentRootPageModel($pageModel))) {
                return;
            }

            if (PageContainer::ADD_PWA_YES !== $rootPageModel->addPwa) {
                Message::addInfo($this->translator->trans('huh.pwa.backend.message.disabled'));
            }
            $configuationModel = PwaConfigurationsModel::findByPk($rootPageModel->pwaConfiguration);
            if ($configuationModel && !$configuationModel->hideInstallPrompt) {
                Message::addInfo($this->translator->trans('huh.pwa.backend.message.hideInstallNotEnabled'));
            }
        }
    }

	public function getPwaSubscriptionButtonTemplate(): array
    {
		return $this->templateLocator->getTemplateGroup('subscribe_button_');
	}
}