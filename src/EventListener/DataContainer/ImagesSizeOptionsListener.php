<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer;

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Image\ImageSizes;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsCallback(table: 'tl_pwa_pushnotifications', target: 'fields.iconSize.options')]
readonly class ImagesSizeOptionsListener
{
    public function __construct(
        private ImageSizes            $imageSizes,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function __invoke(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if (!($user instanceof BackendUser)) {
            return [];
        }
        return $this->imageSizes->getOptionsForUser($user);
    }
}