<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer\Page;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Message;
use HeimrichHannot\PwaBundle\Asset\AssetBuilder;
use HeimrichHannot\PwaBundle\DataContainer\PageContainer;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;

#[AsCallback(table: 'tl_page', target: 'config.oncreate_version')]
readonly class ConfigOnCreateVersionListener
{
    public function __construct(
        private AssetBuilder $assetBuilder,
    ) {}

    public function __invoke(string $table, int $pid, int $version, array $row): void
    {
        if ($row['type'] !== 'root' || $row['addPwa'] !== PageContainer::ADD_PWA_YES) {
            return;
        }

        $config = PwaConfigurationsModel::findByPk($row['pwaConfiguration']);
        if (null === $config) {
            return;
        }

        try {
            $this->assetBuilder->buildForConfig($config);
        } catch (\RuntimeException $e) {
            Message::addError(sprintf(
                $GLOBALS['TL_LANG']['tl_pwa_configurations']['buildFilesError'],
                $e->getMessage(),
            ));
        }
    }
}