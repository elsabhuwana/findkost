<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Payment Page</h1>
    <button id="pay-button">Pay Now</button>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $snap_token }}', {
                onSuccess: function (result) {
                    window.location.href = '/checkout/success';
                },
                onPending: function (result) {
                    window.location.href = '/checkout/pending';
                },
                onError: function (result) {
                    window.location.href = '/checkout/failed';
                }
            });
        }
    </script>
</body>
</html>
