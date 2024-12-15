<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayUMoney Payment</title>
</head>
<body>
    <form action="{{$url}}" method="post">
        @foreach ($payuForm as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="submit" value="Pay Now">
    </form>
</body>
</html>
