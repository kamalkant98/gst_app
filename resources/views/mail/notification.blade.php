<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .email-header {
            display: flex;
            background-color: #000000;
            color: white;
            text-align: center;
            padding: 20px;
            align-items: center;
            justify-content: space-between;
        }
        .email-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 20px;
            line-height: 1.6;
        }
        .email-footer {
            background-color: #000000;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .email-footer a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 10px;
        }
        .social-icons {
            margin: 10px 0;
        }
        .social-icons a {
            display: inline-block;
            margin: 0 5px;
        }
        .social-icons img {
            width: 24px;
            height: 24px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <a href=""><img src="https://zuggadi.com/taxdunia/wp-content/uploads/2024/12/Primary-Logo-Copy.png" alt="Company Logo"></a>
            <!-- <h3>TaxDunia</h3> -->
        </div>
        <div class="email-body">

            {!!$data['message']!!}
            <p>Best regards,

                <br> <strong><a href="" style="text-decoration:none; color: #4CAF50;">The TaxDunia Team</a></strong> <br>
               Mail: <a href="mailto:info@taxdunia.com" style="text-decoration:none; color:#2653a3;">info@taxdunia.com</a><br>
               Phone: <a href="tel:+917300076643" style="text-decoration:none; color:#2653a3;">+917300076643</a>
            </p>

        </div>
        <div class="email-footer">
            <p>Follow us on:</p>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
                </a>
                <a href="https://twitter.com" target="_blank">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter">
                </a>
                <a href="https://linkedin.com" target="_blank">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733561.png" alt="LinkedIn">
                </a>
                <a href="https://instagram.com" target="_blank">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Instagram">
                </a>
            </div>
            <p>123 Business Street, Suite 100, City, State, 12345</p>
            <p>&copy; 2025 [Company Name]. All rights reserved.</p>
            <a href="#">Unsubscribe</a> | <a href="#">Privacy Policy</a>
        </div>
    </div>
</body>
</html>

