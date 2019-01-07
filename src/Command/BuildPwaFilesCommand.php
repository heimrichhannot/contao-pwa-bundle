<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Command;


use Contao\CoreBundle\Command\AbstractLockedCommand;
use HeimrichHannot\ContaoPwaBundle\Model\PageModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildPwaFilesCommand extends AbstractLockedCommand
{

    protected function configure()
    {
        $this->setName('huh:pwa:build')
            ->setDescription('Build or rebuild file for pwa.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, "Performs a run without actually send notifications and making changes to the database.")
        ;

    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('contao.framework')->initialize();
        $io = new SymfonyStyle($input, $output);

        $io->title("Build PWA files");

        $pages = PageModel::findAllWithActivePwaConfiguration();

        if (($pageNumber = $pages->count()) < 1)
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

            if (!$manifest = $this->getContainer()->get('huh.pwa.generator.manifest')->generatePageManifest($page))
            {
                $io->error("Error on generating manifest file for page. Continue with next page...");
                $hasErrors = true;
                continue;
            }
            $io->text("Generated manifest file");

            if (!$this->getContainer()->get('huh.pwa.generator.serviceworker')->generatePageServiceworker($page))
            {
                $io->error("Error on generating service worker file for page. Continue with next page...");
                $hasErrors = true;
                continue;
            }
            $io->text("Generated service worker file");
            if (!$this->getContainer()->get('huh.pwa.generator.configurationfile')->generateConfigurationFile($page))
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
            return 1;
        }

        $io->success("Successfully created PWA files!");
    }
}