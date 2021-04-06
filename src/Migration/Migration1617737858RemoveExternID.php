<?php declare(strict_types=1);

namespace Sas\SyncerModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1617737858RemoveExternID extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1617737858;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE product
            DROP COLUMN extern_id;
            SQL;

        $connection->executeUpdate($query);
        $query = <<<SQL
            ALTER TABLE category
            DROP COLUMN extern_id;
            SQL;

        $connection->executeUpdate($query);
        $query = <<<SQL
            ALTER TABLE product_property
            DROP COLUMN extern_id;
            SQL;

        $connection->executeUpdate($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
