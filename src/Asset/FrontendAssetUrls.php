<?php

namespace HeimrichHannot\PwaBundle\Asset;

class FrontendAssetUrls
{
    public const BASE_PATH = '/bundles/heimrichhannotpwa/frontend/';

    /**
     * @var array<string, string>
     */
    private array $hashes = [];

    public function getUrl(string $file): string
    {
        if (!isset($this->hashes[$file])) {
            $path = \dirname(__DIR__, 2).'/public/frontend/'.$file;
            $algorithm = \in_array('xxh128', hash_algos(), true) ? 'xxh128' : 'md5';
            $hash = is_file($path) ? hash_file($algorithm, $path) : false;
            $this->hashes[$file] = false === $hash ? '' : substr($hash, 0, 10);
        }

        $url = self::BASE_PATH.$file;

        return '' === $this->hashes[$file] ? $url : $url.'?v='.$this->hashes[$file];
    }

    /**
     * @return array<string, string>
     */
    public function getImportMap(): array
    {
        return [
            '@hundh/pwa/bundle' => $this->getUrl('huh-pwa.js'),
            '@hundh/pwa/serviceworker' => $this->getUrl('huh-pwa-serviceworker.js'),
            '@hundh/pwa/InstallPrompt' => $this->getUrl('InstallPrompt.js'),
            '@hundh/pwa/PushNotificationSubscription' => $this->getUrl('PushNotificationSubscription.js'),
            '@hundh/pwa/PushSubscriptionButtons' => $this->getUrl('PushSubscriptionButtons.js'),
        ];
    }
}
