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
	 * HookListener constructor.
	 */
	public function __construct(ManifestLinkTag $manifestLinkTag, ThemeColorMetaTag $colorMetaTag)
	{
		$this->manifestLinkTag = $manifestLinkTag;
		$this->colorMetaTag = $colorMetaTag;
	}

	/**
	 * @param PageModel $page
	 * @param LayoutModel $layout
	 * @param PageRegular $pageRegular
	 */
	public function onGeneratePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
	{
		$rootPage = PageModel::findByPk($page->rootId);

		if ($rootPage->addPwa)
		{
			$this->manifestLinkTag->setContent('/manifest/' . $rootPage->alias . '_manifest.json');
			$this->colorMetaTag->setContent('#'.$rootPage->pwaThemeColor);

			$serviceWorkerPath = 'sw.js';

			$GLOBALS['TL_HEAD'][] = "<script>
									// Check that service workers are registered
									if ('serviceWorker' in navigator) {
									  // Use the window load event to keep the page load performant
									  window.addEventListener('load', () => {
										navigator.serviceWorker.register('sw.js', {scope: '/de/'});
									  });
									}
									</script>";
		}
	}
}