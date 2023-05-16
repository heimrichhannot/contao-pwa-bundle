<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DataContainer;


use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use HeimrichHannot\Blocks\Backend\Content;
use HeimrichHannot\ContaoPwaBundle\Controller\ContentElement\InstallPwaButtonElementController;
use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;

class ContentContainer
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
        }
    }

	public function getPwaSubscriptionButtonTemplate()
	{
		return $this->templateLocator->getTemplateGroup('subscribe_button_');
	}
}