<?php declare(strict_types=1);

namespace Sas\SyncerModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1617833888PropertyGroupOptionExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1617833888;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `property_group_option_extension` (
            `id`                BINARY(16)      NOT NULL,
            `property_group_option_id`   BINARY(16)    NULL,
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
