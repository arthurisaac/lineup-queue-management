<?php

namespace App\Console\Commands;

use App\Events\TimeTicketEvent;
use Illuminate\Console\Command;

class Passage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:passage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        event(new TimeTicketEvent());
    }
}
