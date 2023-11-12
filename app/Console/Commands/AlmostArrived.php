<?php

namespace App\Console\Commands;

use App\Events\TicketAlmostArrivedEvent;
use Illuminate\Console\Command;

class AlmostArrived extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:almost-arrived';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a ticket has 5 minutes left';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        event(new TicketAlmostArrivedEvent());
    }
}
