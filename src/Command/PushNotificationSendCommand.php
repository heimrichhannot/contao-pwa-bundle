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
use HeimrichHannot\PwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\PwaBundle\Model\PwaPushNotificationsModel;
use HeimrichHannot\PwaBundle\Notification\DefaultNotification;
use HeimrichHannot\PwaBundle\Sender\ConsoleLogger;
use HeimrichHannot\PwaBundle\Sender\PushNotificationSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('huh:pwa:send-push', 'Send unsent push notifications.')]
class PushNotificationSendCommand extends Command
{
    public function __construct(
        private readonly ContaoFramework        $contaoFramework,
        private readonly PushNotificationSender $notificationSender,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            name: 'dry-run',
            description: 'Performs a run without actually send notifications and making changes to the database.',
            default: false
        );
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->contaoFramework->initialize();
        $io = new SymfonyStyle($input, $output);

        $io->title("Send unsend push notifications");

        if ($dryRun = (bool) $input->getOption('dry-run'))
        {
            $io->note("Dry run enabled, no data will be changed.");
            $io->newLine();
        }

        $unsent = PwaPushNotificationsModel::findUnsentPublishedNotifications();

        if (!$unsent || $unsent->count() < 1)
        {
            $io->text("Found no unsent messages.");
            $io->success("Finished");

            return Command::SUCCESS;
        }

        $io->text("Found " . $unsent->count() . ' messages.');
        $io->newLine();

        foreach ($unsent as $notification)
        {
            $io->section("Sending notification " . $notification->title . " (" . $notification->id . ")");

            if (!$configuration = PwaConfigurationsModel::findByPk($notification->pid))
            {
                $io->error(\sprintf(
                    'No configuration found for id %d for notification %s (%d)',
                    $notification->pid,
                    $notification->title,
                    $notification->id
                ));

                continue;
            }

            $pushNotification = new DefaultNotification($notification);

            try
            {
                $logger = new ConsoleLogger($output);

                if ($dryRun)
                {
                    $logger->info(
                        "Simulated sending push notification to 0 subscribers. 0 subscribers skipped. 0 errors.",
                        ['verbosity' => OutputInterface::VERBOSITY_NORMAL],
                    );
                }
                elseif (!$this->notificationSender->sendWithLog($pushNotification, $configuration, $logger))
                {
                    $io->error("Error sending notification " . $notification->title . " (" . $notification->id . ")");
                    $io->newLine();

                    continue;
                }
            }
            catch (\ErrorException $e)
            {
                $io->error($e->getMessage());
                $io->newLine();
            }

            $io->newLine();
        }

        $io->success("Finished send command");

        return Command::SUCCESS;
    }
}