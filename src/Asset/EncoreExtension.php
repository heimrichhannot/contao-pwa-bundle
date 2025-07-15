<?php

namespace HeimrichHannot\PwaBundle\Asset;

use HeimrichHannot\PwaBundle\HeimrichHannotPwaBundle;
use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;

class EncoreExtension implements EncoreExtensionInterface
{
    public function getBundle(): string
    {
        return HeimrichHannotPwaBundle::class;
    }

    public function getEntries(): array
    {
        return [
            EncoreEntry::create('contao-pwa-bundle', 'src/Resources/assets/js/pwa-bundle.js')
                ->addJsEntryToRemoveFromGlobals('huh.pwa.bundle')
        ];
    }
}