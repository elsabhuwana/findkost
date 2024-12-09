<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = false; // Ubah ke true jika sudah di produksi
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Ambil data dari cart
        $cartItems = Cart::getContent();
        $grossAmount = Cart::getTotal();

        // Cek apakah keranjang kosong
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Siapkan parameter untuk transaksi
        $params = array (
            'transaction_details' => array (
                'order_id' => 'order_' . time(), // Unique order ID
                'gross_amount' => $grossAmount, // Total dari keranjang
            ),
            'customer_details' => array (
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email, // Gunakan email pengguna
                'phone' => Auth::user()->phone, // Pastikan Anda memiliki field telepon
            ),
        );

        // Ambil Snap token
        try {
            $snapToken = Snap::getSnapToken($params); // Retrieve Snap token
            return response()->json(['snapToken' => $snapToken]); // Kembalikan Snap token
        } catch (\Exception $e) {
            Log::error('Gagal mendapatkan Snap token: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mendapatkan Snap token'], 500);
        }
    }

    public function checkTransactionStatus($orderId)
    {
        try {
            // Ambil status transaksi dari Midtrans
            $status = Transaction::status($orderId);

            // Log status transaksi untuk debugging
            Log::info('Status Transaksi untuk Order ID ' . $orderId . ': ' . json_encode($status));

            // Tampilkan status transaksi
            return response()->json($status);
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa status transaksi: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memeriksa status transaksi'], 500);
        }
    }

    public function paymentSuccess()
    {
        return view('checkout.success'); // Halaman sukses
    }

    public function paymentPending()
    {
        return view('checkout.pending'); // Halaman pending
    }

    public function paymentFailed()
    {
        return view('checkout.failed'); // Halaman gagal
    }
}