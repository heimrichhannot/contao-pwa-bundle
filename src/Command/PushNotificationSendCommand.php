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


use Contao\CoreBundle\Framework\ContaoFramework;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use HeimrichHannot\ContaoPwaBundle\Sender\PushNotificationSender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushNotificationSendCommand extends Command
{
    protected static $defaultName = 'huh:pwa:sendpush';
    protected static $defaultDescription = 'Send unsent push notifications.';

	protected                      $dryRun = false;
    private ContaoFramework        $contaoFramework;
    private PushNotificationSender $notificationSender;

    public function __construct(ContaoFramework $contaoFramework, PushNotificationSender $notificationSender)
    {
        parent::__construct();
        $this->contaoFramework = $contaoFramework;
        $this->notificationSender = $notificationSender;
    }


    protected function configure()
	{
		$this
			->setDescription(static::$defaultDescription)
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
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->contaoFramework->initialize();
		$io = new SymfonyStyle($input, $output);

		$io->title("Send unsend push notifications");

		if ($input->hasOption('dry-run') && $input->getOption('dry-run'))
		{
			$this->dryRun = true;
			$io->note("Dry run enabled, no data will be changed.");
			$io->newLine();
		}

        $unsent = PwaPushNotificationsModel::findUnsentPublishedNotifications();

		if (!$unsent || $unsent->count() == 0)
		{
			$io->text("Found no unsent messages.");
			$io->success("Finished");
			return 0;
		}

		$io->text("Found ".$unsent->count().' messages.');
		$io->newLine();

		$io->progressStart($unsent->count());

        $table = new Table($output);
        $table->setHeaders(["Notification", "Messages sent","Messages sent successfull","Errors"]);

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
					$result = $this->notificationSender->send($pushNotification, $configuration);
					$tableResult = ["notification" => $notification->title.' (ID: '.$notification->id.')'];
					$tableResult = array_merge($tableResult, $result);
					unset($tableResult['success']);
					if (!empty($tableResult['errors']))
                    {
                        $errors = count($tableResult['errors']);
                        $tableResult['errors'] = $errors;
                    }
					else {
					    $tableResult['errors'] = '-';
                    }
					$table->addRow($tableResult);
				} catch (\ErrorException $e)
				{
					$io->error($e->getMessage());
				}
			}
			$io->progressAdvance();
		}
		$io->progressFinish();

        $io->newLine();
		$io->section("Results:");

		$table->render();
        $io->newLine();


		$io->success("Finished send command");
		return 0;
	}
}