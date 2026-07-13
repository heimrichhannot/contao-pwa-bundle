<?php

/**
 * Heimrich & Hannot PWA Bundle.
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @author    Eric Gesemann <e.gesemann@heimrich-hannot.de>
 * @license   LGPL-3.0-or-later
 */

namespace HeimrichHannot\PwaBundle\NotificationCenter;

use Contao\CoreBundle\Framework\ContaoFramework;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\PwaBundle\Notification\PushIconResolver;
use HeimrichHannot\PwaBundle\NotificationCenter\Stamp\PushMessageStamp;
use HeimrichHannot\PwaBundle\Sender\PushNotificationSender;
use Psr\Log\LoggerInterface;
use Terminal42\NotificationCenterBundle\Exception\Parcel\CouldNotDeliverParcelException;
use Terminal42\NotificationCenterBundle\Gateway\AbstractGateway;
use Terminal42\NotificationCenterBundle\Parcel\Parcel;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\GatewayConfigStamp;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\LanguageConfigStamp;
use Terminal42\NotificationCenterBundle\Receipt\Receipt;

class PushGateway extends AbstractGateway
{
    public const NAME = 'huh_pwa_push';

    public function __construct(
        private readonly PushNotificationSender $sender,
        private readonly PushIconResolver $iconResolver,
        private readonly LoggerInterface $logger,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getRequiredStampsForSealing(): array
    {
        return [
            GatewayConfigStamp::class,
            LanguageConfigStamp::class,
        ];
    }

    protected function getRequiredStampsForSending(): array
    {
        return [
            PushMessageStamp::class,
        ];
    }

    protected function doSealParcel(Parcel $parcel): Parcel
    {
        $gatewayConfig = $parcel->getStamp(GatewayConfigStamp::class)->gatewayConfig;
        $languageConfig = $parcel->getStamp(LanguageConfigStamp::class)->languageConfig;

        $payload = [];

        if ('' !== ($title = $this->replaceTokensAndInsertTags($parcel, $languageConfig->getString('push_title')))) {
            $payload['title'] = $title;
        }

        if ('' !== ($body = $this->replaceTokensAndInsertTags($parcel, $languageConfig->getString('push_body')))) {
            $payload['body'] = $body;
        }

        if ('' !== ($icon = $languageConfig->getString('push_icon')) && null !== ($icon = $this->iconResolver->resolve($icon, null))) {
            $payload['icon'] = $icon;
        }

        if ('' !== ($url = $this->replaceTokensAndInsertTags($parcel, $languageConfig->getString('push_url')))) {
            $payload['data'] = [
                'clickJumpTo' => $url,
            ];
        }

        return $parcel
            ->seal()
            ->withStamp(new PushMessageStamp($gatewayConfig->getInt('pwaConfiguration'), $payload))
        ;
    }

    protected function doSendParcel(Parcel $parcel): Receipt
    {
        try {
            $stamp = $parcel->getStamp(PushMessageStamp::class);
            $this->framework->initialize();

            if (!$config = PwaConfigurationsModel::findByPk($stamp->pwaConfigurationId)) {
                throw new \RuntimeException(sprintf('PWA configuration ID %d does not exist.', $stamp->pwaConfigurationId));
            }

            $subscribers = PwaPushSubscriberModel::findByPid($config->id);
            $result = $this->sender->deliverPayload($stamp->payload, $subscribers ?? [], $this->logger);

            if (!$result['ok']) {
                throw new \RuntimeException('The web push payload could not be delivered.');
            }

            return Receipt::createForSuccessfulDelivery($parcel);
        } catch (\Throwable $exception) {
            return Receipt::createForUnsuccessfulDelivery(
                $parcel,
                CouldNotDeliverParcelException::becauseOfGatewayException(self::NAME, 0, $exception),
            );
        }
    }
}
