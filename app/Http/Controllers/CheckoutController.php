<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CheckoutController extends Controller
{

    public function index()
{
    // Pastikan pelanggan sudah login
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    // Ambil data keranjang belanja
    $cartItems = Cart::getContent();
    $grossAmount = Cart::getTotal();

    // Jika keranjang kosong, arahkan ke halaman keranjang
    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
    }

    // Return halaman checkout
    return view('frontend.order.checkout', compact('cartItems', 'grossAmount'));
}

public function processCheckout(Request $request)
{
    // Pastikan user sudah login
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login to continue.');
    }

    // Ambil data pelanggan yang sudah login
    $user = Auth::user();

    // Ambil detail pesanan dari keranjang
    $cartItems = Cart::getContent();
    $grossAmount = Cart::getTotal();

    // Jika keranjang kosong, arahkan kembali ke halaman keranjang
    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
    }

    // Nomor WhatsApp Admin (gunakan format internasional)
    $adminPhone = '6281231638570'; // Ganti dengan nomor WhatsApp admin

    // Buat pesan WhatsApp dengan memasukkan data pengguna yang sudah login
    $message = "Halo Admin, saya ingin memesan kamar kost dengan detail sebagai berikut: ";
    $message .= "\n\nPesanan: ";
    foreach ($cartItems as $item) {
        $message .= "- {$item->name} (x{$item->quantity}): Rp" . number_format($item->price, 0, ',', '.') . "\n";
    }
    $message .= "\nTotal Harga: Rp" . number_format($grossAmount, 0, ',', '.');

    // URL WhatsApp (gunakan format yang benar untuk mengarahkan ke chat admin)
    $whatsappUrl = "https://wa.me/{$adminPhone}?text=" . urlencode($message);

    // Redirect pelanggan ke WhatsApp dengan pesan yang sudah terisi otomatis
    return redirect()->away($whatsappUrl);
}

}