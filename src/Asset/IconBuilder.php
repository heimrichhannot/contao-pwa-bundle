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
     * @param string $targetDir  The path where the icons should be stored
     * @param bool   $isRelative Set true if path is relative to public dir
     *
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

    /**
     * @throws \Throwable
     */
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

    /**
     * @throws \Throwable
     */
    public function buildPathForFirstSize(): string
    {
        $images = $this->doBuild();

        return '/'.$images[0]->getUrl($this->publicDir);
    }

    /**
     * @return ImageInterface[]
     * @throws \Throwable
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

        $fs = new Filesystem();
        $resizer = new Resizer($this->tmpDir);
        $image = new Image($this->file->getAbsolutePath(), $this->imagine, $fs);
        $fileExtension = strtolower(pathinfo($this->file->path, PATHINFO_EXTENSION));

        if ($this->emptyTargetDir) {
            return $this->buildWithEmptyTargetDir($image, $fileExtension, $resizer, $fs);
        }
        return $this->buildImages($this->targetDir, $image, $fileExtension, $resizer);
    }

    /**
     * @return ImageInterface[]
     */
    private function buildImages(
        string $targetDir,
        ImageInterface $image,
        string $fileExtension,
        Resizer $resizer,
    ): array {
        $options = (new ResizeOptions())->setTargetPath($targetDir);
        $fs = new Filesystem();
        $result = [];
        foreach ($this->sizes as $size) {
            $config = (new ResizeConfiguration())
                ->setWidth($size[0])
                ->setHeight($size[1])
                ->setMode(ResizeConfiguration::MODE_CROP);

            $fileName = $this->file->hash.'_'.$size[0].'x'.$size[1].'.'.$fileExtension;
            $filePath = Path::join($targetDir, $fileName);

            if (file_exists($filePath)) {
                $result[] = new Image($filePath, $this->imagine, $fs);
                continue;
            }

            $options->setTargetPath($filePath);

            $result[] = $resizer->resize($image, $config, $options);
        }

        return $result;
    }

    private function createTemporaryDirectoryPath(string $type): string
    {
        do {
            $path = Path::join(
                dirname($this->targetDir),
                sprintf('.%s.%s.%s', basename($this->targetDir), $type, bin2hex(random_bytes(8))),
            );
        } while (file_exists($path));

        return $path;
    }

    private function removeQuietly(Filesystem $fs, string $path): void
    {
        try {
            if ($fs->exists($path)) {
                $fs->remove($path);
            }
        } catch (\Throwable) {
        }
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

    private function buildWithEmptyTargetDir(Image $image, string $fileExtension, Resizer $resizer, Filesystem $fs): array
    {
        $stagingDir = $this->createTemporaryDirectoryPath(
            'staging'
        );
        $backupDir = $this->createTemporaryDirectoryPath(
            'backup'
        );
        $targetMovedToBackup = false;
        $stagingPromoted = false;

        try {
            $images = $this->buildImages(
                $stagingDir, $image, $fileExtension, $resizer
            );

            if ($fs->exists($this->targetDir)) {
                $fs->rename(
                    $this->targetDir, $backupDir
                );
                $targetMovedToBackup = true;
            }

            $fs->rename(
                $stagingDir, $this->targetDir
            );
            $stagingPromoted = true;

            $result = [];
            foreach ($images as $generatedImage) {
                $result[] = new Image(
                    Path::join(
                        $this->targetDir, basename(
                        $generatedImage->getPath()
                    )
                    ),
                    $this->imagine,
                    $fs,
                );
            }

            if ($targetMovedToBackup) {
                $this->removeQuietly(
                    $fs, $backupDir
                );
            }

            return $result;
        } catch (\Throwable $exception) {
            $this->removeQuietly(
                $fs, $stagingDir
            );

            if ($targetMovedToBackup && $fs->exists($backupDir)) {
                try {
                    $fs->rename(
                        $backupDir, $this->targetDir, true
                    );
                } catch (\Throwable $rollbackException) {
                    throw new \RuntimeException(
                        sprintf(
                            'Failed to restore the previous icon directory from "%s" after rebuilding failed.', $backupDir
                        ), 0, $rollbackException
                    );
                }
            } elseif ($stagingPromoted) {
                $this->removeQuietly($fs, $this->targetDir);
            }

            throw $exception;
        }
    }
}
