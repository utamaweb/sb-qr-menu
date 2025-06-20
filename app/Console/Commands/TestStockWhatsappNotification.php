<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestStockWhatsappNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test-whatsapp-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the stock WhatsApp notification manually';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing stock WhatsApp notification...');

        // Call the actual notification command
        $this->call('stock:whatsapp-notification');

        $this->info('Test completed.');
        return 0;
    }
}
