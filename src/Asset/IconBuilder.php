<?php

namespace HeimrichHannot\PwaBundle\Asset;

use Contao\FilesModel;
use Contao\Image\Image;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Contao\Image\Resizer;
use Imagine\Image\ImagineInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Path;

class IconBuilder
{
    public function __construct(
        private readonly ImagineInterface $imagine,
        private readonly ParameterBagInterface $parameterBag,
    ) {}

    public function buildForManifest(FilesModel $source, array $sizes, string $targetDir): array
    {
        $resizer = new Resizer(Path::join($this->parameterBag->get('kernel.project_dir'), '/var/pwa/manifest_icon/'));
        $image = new Image($source->path, $this->imagine);

        $options = (new ResizeOptions())->setTargetPath($targetDir);

        $result = [];

        foreach ($sizes as $size) {
            $config = (new ResizeConfiguration())
                ->setWidth($size[0])
                ->setHeight($size[1])
                ->setMode(ResizeConfiguration::MODE_BOX);

            $options->setTargetPath(Path::join($targetDir, $size[0] . 'x' . $size[1] . '.' . pathinfo($source->path, PATHINFO_EXTENSION)));

            $resizedImage = $resizer->resize($image, $config, $options);
            $format = strtolower(pathinfo($resizedImage->getPath(), PATHINFO_EXTENSION));

            $result[] = [
                'src' => $resizedImage->getPath(),
                'sizes' => "{$size[0]}x{$size[1]}",
                'type' => $this->getMimeFromFormat($format),
            ];
        }

        return $result;
    }

    private function getMimeFromFormat(string $format): string
    {
        static $mapping = [
            'jpg' => 'image/jpeg',
            'wbmp' => 'image/vnd.wap.wbmp',
            'svg' => 'image/svg+xml',
        ];

        return $mapping[$format] ?? 'image/'.$format;
    }
}