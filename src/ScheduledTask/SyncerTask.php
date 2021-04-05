<?php declare(strict_types=1);

namespace Sas\SyncerModule\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class SyncerTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'shopware.syncer_task';
    }

    public static function getDefaultInterval(): int
    {
        return 10; // 5 minutes
    }
}