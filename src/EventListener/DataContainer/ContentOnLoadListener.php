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

        // Contao labels can be translated through Symfony Translator with domain `contao_<table>`.
        // Example: `tl_content.text.pwa_offline_pages_0` in domain `contao_tl_content`.
        $fields['text']['label'][0] = $this->translateLabel(
            'tl_content.text.pwa_offline_pages_0',
            $fields['text']['label'][0] ?? ''
        );
        $fields['text']['label'][1] = $this->translateLabel(
            'tl_content.text.pwa_offline_pages_1',
            $fields['text']['label'][1] ?? ''
        );
        $fields['text']['eval']['mandatory'] = false;
    }

    private function translateLabel(string $key, string $fallback): string
    {
        $translation = (string) $this->translator->trans($key, [], 'contao_tl_content');

        if ($translation === '' || $translation === $key) {
            return $fallback;
        }

        return $translation;
    }
}
