<?php

namespace HeimrichHannot\ContaoPwaBundle\Asset;

use HeimrichHannot\ContaoPwaBundle\HeimrichHannotContaoPwaBundle;
use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;

class EncoreExtension implements EncoreExtensionInterface
{
    public function getBundle(): string
    {
        return HeimrichHannotContaoPwaBundle::class;
    }

    public function getEntries(): array
    {
        return [
            EncoreEntry::create('contao-pwa-bundle', 'src/Resources/assets/js/contao-pwa-bundle.js')
                ->addJsEntryToRemoveFromGlobals('huh.pwa.bundle')
        ];
    }
}