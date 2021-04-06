<?php declare(strict_types=1);
namespace Sas\SyncerModule;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class SasSyncerModule extends Plugin
{
    public function activate(ActivateContext $context): void
	{
		$context->setAutoMigrate(false); // disable auto migration execution

        $migrationCollection = $context->getMigrationCollection();

        // execute all UPDATE migrations until and including 2019-12-12T09:30:51+00:00
        $migrationCollection->migrateInPlace(1617734931);
	}

	public function uninstall(UninstallContext $context): void
	{
		$context->setAutoMigrate(false); // disable auto migration execution

        $migrationCollection = $context->getMigrationCollection();

        // execute all UPDATE migrations until and including 2019-12-12T09:30:51+00:00
        $migrationCollection->migrateInPlace(1617737858);
	}
}
