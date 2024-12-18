<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule Call</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<style>
    /* input,
    select {
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #044F60;
    }

    .btn-primary {
        background-color: #044F60 !important;
        border-color: #044F60 !important;
    }

    .form-check-input:checked {
        background-color: #044F60;
        border-color: #044F60;
    } */

    .pad-bg {
        background: #f8f8f8;
        padding: 40px 20px;
        border-radius: 5px;
        margin-top: 50px;
        margin-bottom: 50px;
        border: 1px solid #e5e5e5;
    }

    h1 {
        padding-bottom: 30px;
        margin-top: 0;
        margin-bottom: 30px;
        border-bottom: 1px dashed #ccc;
    }
    .error{
        color: red;
    }
    .otp-box{
        display: none;
    }
    .hidden-box {
        display: none;
    }
</style>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-8 col-offset-2 col-sm-8 col-sm-offset-2">
                <!-- Default form -->
                <!-- <input type="hidden" id="steps" name="steps" value="1"> -->
                <form id="business_registration" method="POST" name="scheduleform" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <h2>Connect us for business registration</h2>
                    </div>
                    <div class="mb-3 hidden-box-2">
                        <?php 
                        $businessRegistration = [
                            '1'  => 'PAN Registration',
                            '2'  => 'TAN Registration',
                            '3'  => 'GST Registration',
                            '4'  => 'MSME Registration',
                            '5'  => 'SHOP ACT Registration',
                            '6'  => 'LLP Registration',
                            '7'  => 'PRIVATE LIMITED COMPANY Registration',
                            '8'  => 'PUBLIC LIMITED COMPANY Registration',
                            '9'  => 'SECTION 8 COMPANY Registration',
                            '10'  => 'TRADEMARK Registration',
                            '11'  => 'COPYRIGHT Registration',
                            '12'  => 'OPC Registration',
                            '13'  => 'ESI Registration',
                            '14'  => 'PF Registration',
                            '15'  => 'FIRM Registration',
                            '16'  => 'Start up Registration'
                        ];
                        
                        ?>
                        <select class="form-select" name="business" id ="business" aria-label="Default select example" required>
                            <option selected>Open this select menu</option>
                            <?php 
                            foreach ($businessRegistration as $key => $value) {
                            ?>
                            <option value="<?php echo $key;?>"><?php echo $value;?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document requirements for business registration</label>
                        <input type="file" class="form-control " id="document" name="document[]" multiple="multiple" required>

                    </div>
                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                  



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  
    


<script>
        $(document).ready(function (e) {
            
            $('#business_registration').on('submit', async (e) => {
                e.preventDefault(); // Prevent the default form submit
                let formElement = document.querySelector('#business_registration'); 
            
                const formData = new FormData(formElement);

                const formObject = Object.fromEntries(formData.entries());
                console.log("formObject",formObject);

                // Send the POST request
                const response = await fetch('http://127.0.0.1:8000/api/business-registration/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                    },
                    body: JSON.stringify(formObject),
                });

                // Parse the JSON response
                const data = await response.json();
                if(data && data.data > 0){}

            });
        });
    </script>

</body>
</html>