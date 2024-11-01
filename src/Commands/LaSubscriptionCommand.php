<?php

namespace Aldeebhasan\LaSubscription\Commands;

use Illuminate\Console\Command;

class LaSubscriptionCommand extends Command
{
    public $signature = 'la-subscription';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
