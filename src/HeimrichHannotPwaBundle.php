<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotPwaBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * {@inheritdoc}
     * @return class-string<ExtensionInterface>
     */
    public function getContainerExtensionClass(): string
    {
        return DependencyInjection\HeimrichHannotPwaExtension::class;
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return $this->extension ??= $this->createContainerExtension() ?: null;
    }
}