<?php declare(strict_types=1);

namespace Sas\SyncerModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1617734931AddExternID extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1617734931;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE product
            ADD COLUMN extern_id INT NOT NULL;
            SQL;

        $connection->executeUpdate($query);
        $query = <<<SQL
            ALTER TABLE category
            ADD COLUMN extern_id INT NOT NULL;
            SQL;

        $connection->executeUpdate($query);
        $query = <<<SQL
            ALTER TABLE product_property
            ADD COLUMN extern_id INT NOT NULL;
            SQL;

        $connection->executeUpdate($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
