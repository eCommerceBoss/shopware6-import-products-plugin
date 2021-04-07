<?php declare(strict_types=1);

namespace Sas\SyncerModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1617784465CategoryExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1617784465;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `category_extension` (
            `id`                BINARY(16)      NOT NULL,
            `category_id`   BINARY(16)    NULL,
            `extern_id`   INT(11)    NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        )
            ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

                $connection->executeUpdate($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
