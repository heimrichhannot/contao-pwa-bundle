<?php

/**
 * PwaConfigurationContainer
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\DataContainer;

use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Contao\StringUtil;
use Symfony\Component\Routing\RouterInterface;

readonly class PwaConfigurationContainer
{
    public function __construct(
        private RouterInterface $router,
        private FinderFactory   $finderFactory
    ) {}

    public function onControlActionButtonCallback($href, $label, $title, $class, $attributes, $table, $root): string
    {
        return \sprintf(
            '<a href="%s" title="%s" class="%s" %s>%s</a>',
            $this->router->generate($href),
            StringUtil::specialchars($title),
            $class,
            $attributes,
            $label
        );
    }

    public function getServiceWorkerTemplates(): array
    {
        $finder = $this->finderFactory->create()
            ->identifier('pwa/serviceworker')
            ->extension('js.twig')
            ->withVariants()
        ;

        return $finder->asTemplateOptions();
    }
}