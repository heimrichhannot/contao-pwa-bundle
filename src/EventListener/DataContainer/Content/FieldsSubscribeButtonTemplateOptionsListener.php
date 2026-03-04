<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer\Content;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Twig\Finder\FinderFactory;

readonly class FieldsSubscribeButtonTemplateOptionsListener
{
    public function __construct(
        private FinderFactory $templateLocator,
    ) {}

    #[AsCallback('tl_content', 'fields.pwaSubscribeButtonTemplate.options')]
    public function onPwaSubscribeButtonTemplateOptionsCallback(): array
    {
        return $this->templateLocator->create()
            ->identifier('pwa/subscribe_button')
            ->extension('html.twig')
            ->withVariants()
            ->asTemplateOptions();
    }
}