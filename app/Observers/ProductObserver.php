<?php

namespace App\Observers;

use App\Mail\BackInStock;
use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Support\Facades\Mail;

class ProductObserver
{
    public function saving(Product $product): void
    {
        if ($product->isDirty('stock_quantity')) {
            $qty = (int) $product->stock_quantity;
            $product->stock_status = $qty > 0 ? 'instock' : 'outofstock';
        }
    }

    public function updated(Product $product): void
    {
        if (
            $product->wasChanged('stock_status')
            && $product->stock_status === 'instock'
        ) {
            $this->sendBackInStockNotifications($product);
        }
    }

    private function sendBackInStockNotifications(Product $product): void
    {
        $notifications = StockNotification::where('product_id', $product->id)
            ->whereNull('notified_at')
            ->get();

        if ($notifications->isEmpty()) {
            return;
        }

        $product->loadMissing('featuredImage');

        foreach ($notifications as $notification) {
            Mail::to($notification->email)->send(new BackInStock($product));
            $notification->update(['notified_at' => now()]);
        }
    }
}
