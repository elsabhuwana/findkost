@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>
    <p>Total Pembayaran: Rp{{ number_format(Cart::getTotal(), 0, ',', '.') }}</p>

    <!-- Tombol Checkout -->
    <button id="checkout-button" class="btn btn-primary">Proses Checkout</button>
</div>

<!-- Tambahkan JavaScript Midtrans -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
document.getElementById('checkout-button').addEventListener('click', function () {
    // Panggil API untuk mendapatkan Snap token
    fetch('/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({}),
    })
    .then(response => response.json())
    .then(data => {
        if (data.snapToken) {
            // Tampilkan modal pembayaran Snap
            window.snap.pay(data.snapToken, {
                onSuccess: function (result) {
                    alert('Pembayaran berhasil!');
                    window.location.href = '/checkout/success';
                },
                onPending: function (result) {
                    alert('Pembayaran sedang diproses.');
                    window.location.href = '/checkout/pending';
                },
                onError: function (result) {
                    alert('Pembayaran gagal!');
                    window.location.href = '/checkout/failed';
                },
            });
        } else {
            alert('Gagal mendapatkan Snap token');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran.');
    });
});
</script>
@endsection
