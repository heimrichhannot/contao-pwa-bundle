<?php

namespace HeimrichHannot\PwaBundle\Migration;

use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class ContentElementMigration implements MigrationInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function getName(): string
    {
        return 'PWA Content Element Migration';
    }

    public function shouldRun(): bool
    {
        return $this->connection
            ->executeQuery('SELECT id FROM tl_content WHERE type = "pwa_installButton"')
            ->rowCount() > 0;
    }

    public function run(): MigrationResult
    {
        $result = $this->connection->update(
            'tl_content',
            ['type' => 'pwa_install_button'],
            ['type' => 'pwa_installButton']
        );

        return new MigrationResult(
            true,
            sprintf('Updated %d content elements.', $result)
        );
    }
}