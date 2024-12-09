<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment with Midtrans</title>
    <script 
        src="https://app.midtrans.com/snap/snap.js" 
        data-client-key="{{ env('SB-Mid-client-pt39kw16xR1lWMtl') }}">
    </script>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h1>Complete Your Payment</h1>
        <button id="pay-button" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Pay Now</button>
    </div>

    <script>
        const payButton = document.getElementById('pay-button');

        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    alert('Payment Successful!');
                    console.log(result);
                    // Redirect or process the result as needed
                    window.location.href = '/payment-success';
                },
                onPending: function(result) {
                    alert('Payment Pending. Please complete payment.');
                    console.log(result);
                    // Handle pending payment
                    window.location.href = '/payment-pending';
                },
                onError: function(result) {
                    alert('Payment Failed. Please try again.');
                    console.log(result);
                    // Handle payment failure
                    window.location.href = '/payment-failed';
                },
                onClose: function() {
                    alert('You closed the payment popup without finishing the payment.');
                }
            });
        });
    </script>
</body>
</html>
