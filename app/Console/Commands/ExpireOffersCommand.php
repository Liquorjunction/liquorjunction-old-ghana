<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireOffersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate offers that have passed their expiry date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $expiredOffers = Offers::where('status', 1)
            ->whereDate('expiry_date', '<', $today)
            ->update(['status' => 0]);

        $message = "Expired offers check completed: {$expiredOffers} offer(s) deactivated for date {$today}.";
        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }
}
