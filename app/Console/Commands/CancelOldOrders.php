<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelOldOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel unprocessed orders older than 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cancelling old unprocessed orders...');

        $cutoffTime = Carbon::now()->subHour();

        $ordersToCancel = Order::where('status', 'pending') // Assuming 'pending' is an unprocessed status
                               ->where('created_at', '<', $cutoffTime)
                               ->get();

        if ($ordersToCancel->isEmpty()) {
            $this->info('No old orders to cancel.');
            return 0;
        }

        foreach ($ordersToCancel as $order) {
            $order->status = 'cancelled'; // Assuming 'cancelled' is a valid status
            $order->save();
            Log::info('Order cancelled: ' . $order->id);
            $this->info('Cancelled order: ' . $order->id);
            // Optionally, dispatch an event or send a notification for cancelled order
        }

        $this->info('Finished cancelling old orders. Total cancelled: ' . $ordersToCancel->count());
        return 0;
    }
}
