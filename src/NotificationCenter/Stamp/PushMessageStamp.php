<?php

/**
 * Heimrich & Hannot PWA Bundle.
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\NotificationCenter\Stamp;

use Terminal42\NotificationCenterBundle\Parcel\Stamp\StampInterface;

final class PushMessageStamp implements StampInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public readonly int $pwaConfigurationId,
        public readonly array $payload,
    ) {
    }

    public function toArray(): array
    {
        return [
            'pwaConfigurationId' => $this->pwaConfigurationId,
            'payload' => $this->payload,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self((int) $data['pwaConfigurationId'], (array) $data['payload']);
    }
}
