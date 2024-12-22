<!DOCTYPE html>
<html>
<head>
    <title>Email Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333333;
            font-size: 10px !important;
        }
        p {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
        }
        ul {
            padding-left: 20px;
            color: #555555;
        }
        ul li {
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #888888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        {!!$data['message']!!}

        <div class="footer">
            <p>&copy; 2024 TaxDunia. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
