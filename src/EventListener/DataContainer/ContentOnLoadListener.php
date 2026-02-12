<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\PwaBundle\Controller\ContentElement\OfflinePagesElementController;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCallback('tl_content', 'config.onload')]
class ContentOnLoadListener
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public function __invoke(?DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        if (!$contentModel = ContentModel::findByPk($dc->id)) {
            return;
        }

        if ($contentModel->type !== OfflinePagesElementController::TYPE) {
            return;
        }

        $fields = &$GLOBALS['TL_DCA']['tl_content']['fields'];

        $fields['text']['label'][0] = $this->translator->trans('tl_content.text.pwa_offline_pages_0', [], 'contao_tl_content');
        $fields['text']['label'][1] = $this->translator->trans('tl_content.text.pwa_offline_pages_1', [], 'contao_tl_content');
        $fields['text']['eval']['mandatory'] = false;
    }
}
