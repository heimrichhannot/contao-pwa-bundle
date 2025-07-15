<?php

/**
 * Heimrich & Hannot PWA Bundle
 *
 * @copyright 2025 Heimrich & Hannot GmbH
 * @author    Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license   http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\PwaBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use HeimrichHannot\PwaBundle\Generator\ConfigurationFileGenerator;
use HeimrichHannot\PwaBundle\Generator\ManifestGenerator;
use HeimrichHannot\PwaBundle\Generator\ServiceWorkerGenerator;
use HeimrichHannot\PwaBundle\Model\PageModel;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('huh:pwa:build', 'Build or rebuild file for pwa.')]
class BuildPwaFilesCommand extends Command
{
    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly ManifestGenerator $manifestGenerator,
        private readonly ServiceWorkerGenerator $serviceWorkerGenerator,
        private readonly ConfigurationFileGenerator $configurationFileGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            name: 'dry-run',
            description: 'Performs a run without actually send notifications and making changes to the database.'
        );
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->contaoFramework->initialize();
        $io = new SymfonyStyle($input, $output);

        $io->title("Build PWA files");

        $pages = PageModel::findAllWithActivePwaConfiguration();

        if (null === $pages || ($pageNumber = $pages->count()) < 1)
        {
            $io->text("Found no pages with active PWA Configuration. ");
            return 0;
        }

        $io->text("Found $pageNumber pages with active PWA Configuration.");

        $hasErrors = false;

        foreach ($pages as $page)
        {
            $io->section("Creating files for page \"".$page->title."\" (ID: ".$page->id.")");

            if (!$config = PwaConfigurationsModel::findByPk($page->pwaConfiguration))
            {
                $io->error("No valid configuration found. Skipping...");
                $hasErrors = true;
                continue;
            }
            $io->text("Use Configuration \"".$config->title."\" (ID: ".$config->id.")");

            if (!$manifest = $this->manifestGenerator->generatePageManifest($page))
            {
                $io->error("Error on generating manifest file for page. Continue with next page...");
                $hasErrors = true;
                continue;
            }
            $io->text("Generated manifest file");

            if (!$this->serviceWorkerGenerator->generatePageServiceworker($page))
            {
                $io->error("Error on generating service worker file for page. Continue with next page...");
                $hasErrors = true;
                continue;
            }
            $io->text("Generated service worker file");

            if (!$this->configurationFileGenerator->generateConfigurationFile($page))
            {
                $io->error("Error on generating configuration file for page. Continue with next page...");
                $hasErrors = true;
                continue;
            }
            $io->text("Generated configuration file");
        }

        if ($hasErrors)
        {
            $io->warning("Finished with error!");
            return Command::FAILURE;
        }

        $io->success("Successfully created PWA files!");

        return Command::SUCCESS;
    }
}