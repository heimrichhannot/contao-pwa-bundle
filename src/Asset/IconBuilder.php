<?php

namespace HeimrichHannot\PwaBundle\Asset;

use Contao\FilesModel;
use Contao\Image\Image;
use Contao\Image\ImageInterface;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Contao\Image\Resizer;
use Imagine\Image\ImagineInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class IconBuilder
{
    private array $sizes;
    private string $targetDir;
    private FilesModel $file;
    private bool $emptyTargetDir = false;

    public function __construct(
        private readonly ImagineInterface $imagine,
        private readonly string $tmpDir,
        private readonly string $publicDir,
    ) {
    }

    public function setSizes(array $sizes): self
    {
        $this->sizes = $sizes;
        return $this;
    }

    /**
     * @param string $targetDir The path where the icons should be stored
     * @param bool $isRelative Set true if path is relative to public dir
     * @return $this
     */
    public function setTargetDir(string $targetDir, bool $isRelative = false): self
    {
        if ($isRelative) {
            $targetDir = Path::join($this->publicDir, $targetDir);
        }

        $this->targetDir = $targetDir;
        return $this;
    }

    public function setFile(FilesModel $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function setEmptyTargetDirOnBuild(bool $emptyTargetDir = true): self
    {
        $this->emptyTargetDir = $emptyTargetDir;
        return $this;
    }

    public function buildForManifest(): array
    {
        $images = $this->doBuild();
        $result = [];

        foreach ($images as $image) {
            $result[] = [
                'src' => '/'.$image->getUrl($this->publicDir),
                'sizes' => "{$image->getDimensions()->getSize()->getWidth()}x{$image->getDimensions()->getSize()->getHeight()}",
                'type' => $this->getMimeFromFormat(strtolower(pathinfo($image->getPath(), PATHINFO_EXTENSION))),
            ];
        }

        return $result;
    }

    public function buildPathForFirstSize(): string
    {
        $images = $this->doBuild();
        return '/'.$images[0]->getUrl($this->publicDir);
    }

    /**
     * @return ImageInterface[]
     */
    private function doBuild(): array
    {
        if (!isset($this->sizes)) {
            throw new \LogicException('No icon sizes defined');
        }

        if (!isset($this->targetDir)) {
            throw new \LogicException('No target directory defined');
        }

        if (!isset($this->file)) {
            throw new \LogicException('No source file defined');
        }

        $resizer = new Resizer($this->tmpDir);
        $image = new Image($this->file->path, $this->imagine);

        $options = (new ResizeOptions())->setTargetPath($this->targetDir);
        $fileExtension = strtolower(pathinfo($this->file->path, PATHINFO_EXTENSION));

        $fs = new Filesystem();
        if ($this->emptyTargetDir) {
            $fs->remove($this->targetDir);
        }

        $result = [];
        foreach ($this->sizes as $size) {
            $config = (new ResizeConfiguration())
                ->setWidth($size[0])
                ->setHeight($size[1])
                ->setMode(ResizeConfiguration::MODE_CROP);


            $fileName = $this->file->hash. '_' . $size[0] . 'x' . $size[1] . '.' . $fileExtension;
            $filePath = Path::join($this->targetDir, $fileName);

            if (file_exists($filePath)) {
                $result[] = new Image($filePath, $this->imagine, $fs);
                continue;
            }

            $options->setTargetPath($filePath);

            $result[] = $resizer->resize($image, $config, $options);
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