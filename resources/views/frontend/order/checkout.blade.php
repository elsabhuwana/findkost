@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Checkout</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>Rp{{ number_format($grossAmount, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">Checkout via WhatsApp</button>
    </form>
</div>
@endsection
