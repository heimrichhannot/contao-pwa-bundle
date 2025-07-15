<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\PwaBundle\Command;


use Contao\CoreBundle\Framework\ContaoFramework;
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\PwaBundle\Notification\DefaultNotification;
use HeimrichHannot\PwaBundle\Sender\ConsoleLogger;
use HeimrichHannot\PwaBundle\Sender\PushNotificationSender;
use Symfony\Component\Console\Command\Command;
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

		foreach ($unsent as $notification)
		{
            $io->section("Sending notification ".$notification->title." (".$notification->id.")");
			$configuration = PwaConfigurationsModel::findByPk($notification->pid);
			if (!$configuration)
			{
				$io->error("No configuration found for id ".$notification->pid." for notification ".$notification->title." (".$notification->id.")");
				continue;
			}
			$pushNotification = new DefaultNotification($notification);

            try
            {
                $logger = new ConsoleLogger($output);
                if (!$this->dryRun) {
                    $result = $this->notificationSender->sendWithLog($pushNotification, $configuration, $logger);
                } else {
                    $result = true;

                    $logger->info("Simulated sending push notification to 0 subscribers. 0 subscribers skipped. 0 errors.", [
                        'verbosity' => OutputInterface::VERBOSITY_NORMAL,
                    ]);
                }

                if (false === $result) {
                    $io->error("Error sending notification ".$notification->title." (".$notification->id.")");
                    $io->newLine();
                    continue;
                }

            } catch (\ErrorException $e)
            {
                $io->error($e->getMessage());
                $io->newLine();
            }
            $io->newLine();
		}

		$io->success("Finished send command");
		return 0;
	}
}