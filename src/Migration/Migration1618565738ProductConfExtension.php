<?php declare(strict_types=1);

namespace Sas\SyncerModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1618565738ProductConfExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1618565738;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `product_conf_extension` (
            `id`                BINARY(16)      NOT NULL,
            `product_id`   BINARY(16)    NULL,
            `configable`   TINYINT(1)    NULL,
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
