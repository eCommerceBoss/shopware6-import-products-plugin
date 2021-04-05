<?php declare(strict_types=1);

namespace Sas\SyncerModule\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class SyncerTaskHandler extends ScheduledTaskHandler
{
    public static function getHandledMessages(): iterable
    {
        return [ SyncerTask::class ];
    }

    public function run(): void
    {
    	error_log( '['.date("F j, Y, g:i a e O").']'.": Cron is working. \n", 3, "log.txt" );
        echo 'Do stuff!';
    }
}