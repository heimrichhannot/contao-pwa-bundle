<?php

namespace HeimrichHannot\PwaBundle\Asset;

use Contao\FilesModel;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Imagine\Image\ImagineInterface;
use Symfony\Component\Filesystem\Path;

readonly class IconBuilderFactory
{
    public function __construct(
        private ImagineInterface $imagine,
        private string           $projectDir,
        private string           $webDir,
    ) {
    }

    public function createIconBuilder(): IconBuilder
    {
        return new IconBuilder(
            $this->imagine,
            Path::join($this->projectDir, 'var/pwa/manifest_icon'),
            $this->webDir,
        );
    }

    public function createBuilderForManifestFromConfig(PwaConfigurationsModel $config): ?IconBuilder
    {
        $iconModel = FilesModel::findByUuid($config->pwaIcons);
        if (null === $iconModel) {
            return null;
        }

        return $this->createIconBuilder()
            ->setFile($iconModel)
            ->setSizes([[180, 180], [192, 192], [512, 512]])
            ->setTargetDir(Path::join($this->webDir, 'pwa', $config->id))
            ->setEmptyTargetDirOnBuild(true);
    }
}