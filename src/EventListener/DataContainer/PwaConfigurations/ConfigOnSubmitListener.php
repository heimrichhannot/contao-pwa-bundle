<?php

namespace HeimrichHannot\PwaBundle\EventListener\DataContainer\PwaConfigurations;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Message;
use HeimrichHannot\PwaBundle\Asset\AssetBuilder;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsCallback(table: 'tl_pwa_configurations', target: 'config.onsubmit')]
readonly class ConfigOnSubmitListener
{
    public function __construct(
        private RequestStack $requestStack,
        private AssetBuilder $assetBuilder,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || 'edit' !== $request->query->get('act')) {
            return;
        }

        $config = PwaConfigurationsModel::findByPk($dc->id);
        if (null === $config) {
            return;
        }

        try {
            $this->assetBuilder->buildForConfig($config);
        } catch (\Throwable $e) {
            Message::addError(sprintf(
                $GLOBALS['TL_LANG']['tl_pwa_configurations']['buildFilesError'],
                $e->getMessage(),
            ));

            return;
        }

        Message::addInfo($GLOBALS['TL_LANG']['tl_pwa_configurations']['buildFilesSuccess']);
    }
}
