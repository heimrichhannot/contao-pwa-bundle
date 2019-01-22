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
use HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer;
use HeimrichHannot\ContaoPwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ManifestLinkTag;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\PwaHeadScriptTags;
use HeimrichHannot\ContaoPwaBundle\HeaderTag\ThemeColorMetaTag;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use Symfony\Component\Routing\RouterInterface;

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
	 * @var RouterInterface
	 */
	private $router;
    /**
     * @var ConfigurationFileGenerator
     */
    private $configurationGenerator;
    /**
     * @var ContainerUtil
     */
    private $containerUtil;
    /**
     * @var PwaHeadScriptTags
     */
    private $pwaHeadScriptTags;


    /**
	 * HookListener constructor.
	 */
	public function __construct(
	    ManifestLinkTag $manifestLinkTag,
        ThemeColorMetaTag $colorMetaTag,
        PwaHeadScriptTags $pwaHeadScriptTags,
        \Twig_Environment $twig, RouterInterface $router, ConfigurationFileGenerator $configurationGenerator, ContainerUtil $containerUtil)
	{
		$this->manifestLinkTag = $manifestLinkTag;
		$this->colorMetaTag = $colorMetaTag;
		$this->twig = $twig;
		$this->router = $router;
        $this->configurationGenerator = $configurationGenerator;
        $this->containerUtil = $containerUtil;
        $this->pwaHeadScriptTags = $pwaHeadScriptTags;
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
	    if ($this->containerUtil->isBackend())
        {
            return;
        }
		$rootPage = PageModel::findByPk($page->rootId);

		if ($rootPage->addPwa === PageContainer::ADD_PWA_YES && $rootPage->pwaConfiguration)
		{
			$config = PwaConfigurationsModel::findByPk($rootPage->pwaConfiguration);
			if (!$config)
			{
				return;
			}

			$this->manifestLinkTag->setContent('/pwa/' . $rootPage->alias . '_manifest.json');
			$this->colorMetaTag->setContent('#'.$config->pwaThemeColor);

			$this->pwaHeadScriptTags->addScript($this->twig->render('@HeimrichHannotContaoPwa/translation/translation.js.twig'));
			$this->pwaHeadScriptTags->addScript("HuhContaoPwaBundle=".json_encode(
			    $this->configurationGenerator->generateConfiguration($rootPage, $config),
                JSON_UNESCAPED_UNICODE
            ));
		}
	}
}