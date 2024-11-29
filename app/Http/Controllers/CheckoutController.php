<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{

    public function index(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = 'SB-Mid-server-NksDF_vAmJ4lvv87FiaG_e_M';
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Ambil data dari request
        $orderId = uniqid();
        $grossAmount = $request->input('total');
        $customerName = $request->input('customer_name');
        $customerEmail = $request->input('customer_email');
        $customerPhone = $request->input('customer_phone');

        // Data transaksi untuk Midtrans
        $transaction = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
        ];

        try {
            // Buat Snap Token
            $snapToken = Snap::getSnapToken($transaction);

            Log::info($snapToken);

            // Redirect ke halaman pembayaran Midtrans
            return view('frontend.order.payment', compact('snapToken'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->withError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function paymentSuccess()
    {
        return view('checkout.success'); // Buat halaman success di resources/views/checkout/success.blade.php
    }

    public function paymentPending()
    {
        return view('checkout.pending'); // Buat halaman pending di resources/views/checkout/pending.blade.php
    }

    public function paymentFailed()
    {
        return view('checkout.failed'); // Buat halaman failed di resources/views/checkout/failed.blade.php
    }
}
