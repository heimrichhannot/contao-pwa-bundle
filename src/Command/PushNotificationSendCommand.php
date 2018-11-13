<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Command;


use Contao\CoreBundle\Command\AbstractLockedCommand;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushNotificationSendCommand extends AbstractLockedCommand
{
	protected $dryRun = false;

	protected function configure()
	{
		$this->setName('huh:pwa:sendpush')
			->setDescription('Send unsent push notifications.')
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

		$io->title("Send unsend push notifications");

		if ($input->hasOption('dry-run') && $input->getOption('dry-run'))
		{
			$this->dryRun = true;
			$io->note("Dry run enabled, no data will be changed.");
			$io->newLine();
		}

		$sender = $this->getContainer()->get('huh.pwa.sender.pushnotification');

		$unsent = $sender->findUnsendNotifications();
		if (!$unsent || $unsent->count() == 0)
		{
			$io->text("Found no unsent messages.");
			$io->success("Finished");
			return 0;
		}

		$io->text("Found ".$unsent->count().' messages.');
		$io->newLine();

		$io->progressStart($unsent->count());

		foreach ($unsent as $notification)
		{
			$configuration = PwaConfigurationsModel::findByPk($notification->pid);
			if (!$configuration)
			{
				$io->error("No configuration found for id ".$notification->pid." for notification ".$notification->title." (".$notification->id.")");
				$io->progressAdvance();
				continue;
			}
			$pushNotification = new DefaultNotification($notification);
			if (!$this->dryRun)
			{
				try
				{
					$result = $sender->send($pushNotification, $configuration);
				} catch (\ErrorException $e)
				{
					$io->error($e->getMessage());
				}
			}
			$io->progressAdvance();
		}
		$io->progressFinish();
		$io->success("Finished send command");
		return 0;
	}
}