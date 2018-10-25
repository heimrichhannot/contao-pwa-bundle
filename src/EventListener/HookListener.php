<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\EventListener;


use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ManifestLinkTag;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ThemeColorMetaTag;

class HookListener
{
	/**
	 * @var ManifestLinkTag
	 */
	private $manifestLinkTag;
	/**
	 * @var ThemeColorMetaTag
	 */
	private $colorMetaTag;
	/**
	 * @var \Twig_Environment
	 */
	private $twig;


	/**
	 * HookListener constructor.
	 */
	public function __construct(ManifestLinkTag $manifestLinkTag, ThemeColorMetaTag $colorMetaTag, \Twig_Environment $twig)
	{
		$this->manifestLinkTag = $manifestLinkTag;
		$this->colorMetaTag = $colorMetaTag;
		$this->twig = $twig;
	}

	/**
	 * @param PageModel $page
	 * @param LayoutModel $layout
	 * @param PageRegular $pageRegular
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function onGeneratePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
	{
		$rootPage = PageModel::findByPk($page->rootId);

		if ($rootPage->addPwa)
		{
			$this->manifestLinkTag->setContent('/manifest/' . $rootPage->alias . '_manifest.json');
			$this->colorMetaTag->setContent('#'.$rootPage->pwaThemeColor);

			$serviceWorker = 'sw_'.$rootPage->alias.'.js';

//			$GLOBALS['TL_HEAD'][] =
//				"<script>"
//				.$this->twig->render('@HeimrichHannotContaoPwa/registration/push.js.twig', [
//					'scriptName' => $serviceWorker,
//				])
//				."</script>";
		}
	}
}