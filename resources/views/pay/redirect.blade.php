<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayUMoney</title>
</head>
<body onload="submitPayuForm()">
    <h1>Redirecting to PayUMoney...</h1>
    <form id="payu-form" action="{{ $paymentUrl }}" method="POST" name="payuForm">
        <input type="hidden" name="key" value="{{ $data['key'] }}">
        <input type="hidden" name="txnid" value="{{ $data['txnid'] }}">
        <input type="hidden" name="amount" value="{{ $data['amount'] }}">
        <input type="hidden" name="productinfo" value="{{ $data['productinfo'] }}">
        <input type="hidden" name="firstname" value="{{ $data['firstname'] }}">
        <input type="hidden" name="email" value="{{ $data['email'] }}">
        <input type="hidden" name="phone" value="{{ $data['phone'] }}">
        <input type="hidden" name="surl" value="{{ $data['surl'] }}">
        <input type="hidden" name="furl" value="{{ $data['furl'] }}">
        <input type="hidden" name="hash" value="{{ $data['hash'] }}">
        <button type="submit">Click here if you are not redirected</button>
    </form>

    <script>
        function submitPayuForm(){
            document.getElementById('payu-form').submit();
        }
    </script>
</body>
</html>
