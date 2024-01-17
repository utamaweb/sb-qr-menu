<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseCashier extends Model
{
    use HasFactory;
    protected $fillable = [
        'open_time',
        'close_time',
        'user_id',
        'warehouse_id',
        'initial_balance',
        'total_cash',
        'total_non_cash',
        'total_money',
        'complete_product_sales',
    ];

    // Definisikan relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisikan relasi dengan Warehouse
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Definisikan relasi dengan Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Metode untuk menghitung total penjualan produk
    public function calculateTotalProductSales()
    {
        return $this->transactions->flatMap(function ($transaction) {
            return $transaction->transaction_details->mapWithKeys(function ($detail) {
                return [
                    $detail->product->id => $detail->qty,
                ];
            });
        })->toArray();
    }

    // Metode untuk mengambil total penjualan per produk
    public function getProductSales()
    {
        $productSales = [];

        // Loop melalui setiap transaksi terkait dengan penutupan kasir
        foreach ($this->transactions as $transaction) {
            // Loop melalui setiap detail transaksi
            foreach ($transaction->transaction_details as $detail) {
                $productId = $detail->product->id;

                // Menambahkan ke jumlah penjualan produk
                if (isset($productSales[$productId])) {
                    $productSales[$productId] += $detail->qty;
                } else {
                    $productSales[$productId] = $detail->qty;
                }
            }
        }

        return $productSales;
    }
}
