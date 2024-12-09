<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Midtrans\Transaction;

class OrderController extends Controller
{
    public function process()
    {
        return view('frontend.order.checkout');
    }

    public function checkout(Request $request)
{
    $params = $request->except('_token');
    $items = Cart::getContent();
    
    $totalWeight = 0;
    foreach ($items as $item) {
        $totalWeight += ($item->quantity * $item->associatedModel->weight);
    }

    $selectedShipping = $this->getSelectedShipping($params['city'], $totalWeight, $params['shippingService']);
    
    $baseTotalPrice = Cart::getSubTotal();
    $shippingCost = $selectedShipping['cost'];
    $grandTotal = $baseTotalPrice + $shippingCost;

    $orderCode = Order::generateCode();  // Generate order code
    $userProfile = [
        'username' => $params['fullName'],
        'address' => $params['address'],
        'address2' => $params['address2'],
        'province_id' => $params['province'],
        'city_id' => $params['city'],
        'postcode' => $params['postcode'],
        'phone' => $params['phone'],
        'email' => $params['email'],
    ];

    // Create payment gateway transaction details
    $transactionDetails = [
        'order_id' => $orderCode,
        'gross_amount' => $grandTotal,
        'customer_details' => [
            'first_name' => $params['fullName'],
            'email' => $params['email'],
            'phone' => $params['phone'],
        ]
    ];

    try {
        // Proceed with payment gateway (Midtrans in this case)
        $snap = Snap::createTransaction($transactionDetails);
        
        // Process payment token and redirect user to payment page
        $paymentToken = $snap->token;
        $paymentUrl = $snap->redirect_url;
        
        // Log transaction token and ID for debugging purposes
        Log::info('Snap Token: ' . $paymentToken);
        
        // Redirect to payment page
        return redirect($paymentUrl);  // Redirect to payment page
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
    }
}

    public function paymentCallback(Request $request)
    {
        $notif = new Notification();

        // Get order ID from Midtrans notification
        $orderCode = $notif->order_id;
        Log::info('Order ID dari Midtrans:', ['order_id' => $orderCode]);

        // Check if the order exists in database
        $order = Order::where('code', $orderCode)->first();

        if (!$order) {
            Log::error('Order tidak ditemukan di database:', ['order_code' => $orderCode]);
            return response('Order not found', 404);  // Mengembalikan response error jika order tidak ditemukan
        }

        // Get transaction status from Midtrans
        $status = $notif->transaction_status;
        Log::info('Status transaksi dari Midtrans:', ['status' => $status]);

        // Update order status based on the transaction status
        switch ($status) {
            case 'capture':
                $order->status = 'paid';
                break;
            case 'pending':
                $order->status = 'pending';
                break;
            case 'deny':
                $order->status = 'failed';
                break;
            case 'cancel':
                $order->status = 'canceled';
                break;
            default:
                $order->status = 'unknown';
        }

        $order->save();

        // Redirect user based on payment status
        if ($order->status == 'paid') {
            return redirect()->route('order.success', ['order_id' => $order->id]);
        }

        return redirect()->route('order.failed');
    }
}
