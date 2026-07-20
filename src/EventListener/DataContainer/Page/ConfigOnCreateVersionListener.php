<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer\Page;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Message;
use Contao\PageModel;
use HeimrichHannot\PwaBundle\Asset\AssetBuilder;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;

#[AsCallback(table: 'tl_page', target: 'config.oncreate_version')]
readonly class ConfigOnCreateVersionListener
{
    public function __construct(
        private AssetBuilder $assetBuilder,
    ) {
    }

    public function __invoke(string $table, int $pid, int $version, array $row): void
    {
        if ('root' !== $row['type'] || PageContainer::ADD_PWA_YES !== $row['addPwa']) {
            return;
        }

        $page = PageModel::findByPk($pid);
        if (null === $page) {
            return;
        }

        try {
            $this->assetBuilder->buildForPage($page);
        } catch (\RuntimeException $e) {
            Message::addError(sprintf(
                $GLOBALS['TL_LANG']['tl_pwa_configurations']['buildFilesError'],
                $e->getMessage(),
            ));
        }
    }
}
