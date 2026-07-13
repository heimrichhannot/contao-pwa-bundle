<?php

/**
 * Heimrich & Hannot PWA Bundle.
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\Notification;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\FilesModel;
use Contao\StringUtil;

class PushIconResolver
{
    public function __construct(
        private readonly ImageFactoryInterface $imageFactory,
        private readonly ContaoFramework $framework,
        private readonly string $projectDir,
    ) {
    }

    public function resolve(string $uuid, ?string $serializedSize): ?string
    {
        $this->framework->initialize();

        $file = $this->framework->getAdapter(FilesModel::class)->findByUuid($uuid);

        if (!$file || !is_file($this->projectDir.'/'.$file->path)) {
            return null;
        }

        /** @var array<int, int|string>|null $size */
        $size = $serializedSize ? StringUtil::deserialize($serializedSize) : null;
        $image = $this->imageFactory->create($this->projectDir.'/'.$file->path, $size);

        return $image->getUrl($this->projectDir);
    }
}
