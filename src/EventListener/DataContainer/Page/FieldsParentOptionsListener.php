<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer\Page;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\PageModel;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

#[AsCallback('tl_page', 'fields.pwaParent.options')]
class FieldsParentOptionsListener
{
    public function __invoke(): array
    {
        $pages = PageModel::findBy('addPwa', PageContainer::ADD_PWA_YES);
        if (null === $pages) {
            return [];
        }

        $options = [];

        foreach ($pages as $page) {
            if (!$pwaConfig = PwaConfigurationsModel::findByPk($page->pwaConfiguration)) {
                continue;
            }

            $options[$page->id] = \sprintf('%s (%s)', $page->title, $pwaConfig->title);
        }

        return $options;
    }
}