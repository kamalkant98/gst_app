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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
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
        .hidden-box-1 {
            display: none;
        }
        .hidden-box-2 {
            display: none;
        }
        #multi-select{
            height: 26px;
            display: flex;
            flex-direction: column;
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            height: 25px !important;
        }
        .m-select{
            display: flex !important;
            flex-direction: column !important;
        }
        .payment-summary {
            display: none;
        }
        #remove-coupon{
            display: none;
        }
        #checkOutbtn{
            display: none;
        }

        .iti {
            width:100% !important ;
        }

        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 5px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>


</head>
<?php

    $query_types = [
        ['value'=>'1','label' => 'Income Tax Returns'],
        ['value'=>'2','label' => 'TDS Returns'],
        ['value'=>'3','label' => 'GST Returns'],
        ['value'=>'4','label' => 'Business Registration And Licenses'],
        ['value'=>'5','label' => 'NRI Taxation'],
        ['value'=>'6','label' => 'Consultancy Services'],
        ['value'=>'7','label' => 'Other Query']
    ];

    $plan_types = [
        ['value'=>'1','label' => '10 minutes plan'],
        ['value'=>'2','label' => '20 minutes plan'],
        ['value'=>'3','label' => '30 minutes plan'],

    ];
    $language = [
        ['value'=>'Hindi','label' => 'Hindi'],
        ['value'=>'English','label' => 'English'],

    ];
?>

<body>
    <div class="container">



        <div class="row justify-content-md-center mt-5">
            <div class="col-12">
                <!-- Default form -->
                <form id="scheduleform" method="POST" name="scheduleform">
                    <input type="hidden" id="steps" name="steps" value="1">
                    <input type="hidden" id ="form_type" name="form_type" value="schedule_call">

                    <div class="mb-3 text-center">
                        <h2>Register</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control " id="name" name="name" requiredInput placeholder="Full Name">

                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email</label>
                        <input type="text" class="form-control " id="exampleInputEmail1" name="email" requiredInput placeholder="Email">
                    </div>
                    <!-- <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" requiredInput>
                    </div> -->
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <div id="mobile-box">
                            <input type="mobile" class="form-control" id="mobile" name="mobile" requiredInput placeholder="Mobile">
                        </div>
                    </div>

                    <div class="mb-3 ">
                        <select class="form-select formurl" id ="formurl" aria-label="Default select example">
                            <option value=''>Select Your Preferred Service</option>
                            <option value="http://localhost/work/laravel/gst-html/tds_queries.php">TDS/TCS Forms</option>
                            <option value="http://localhost/work/laravel/gst-html/itr_queries.php">Income Tax Returns</option>
                            <option value="http://localhost/work/laravel/gst-html/gst_queries.php">GST Returns</option>
                            <option value="http://localhost/work/laravel/gst-html/business_registration.php">Business Registration</option>
                        </select>
                    </div>


                    <div class="mb-3 hidden-box-1">
                        <label for="otp" class="form-label">OTP</label>
                        <input type="text" class="form-control hide-input" id="otp" name="otp" maxlength="6" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be exactly 6 digits">
                        <!-- <input type="number" class="form-control hide" id="otp" name="otp" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be 6 digits."> -->
                    </div>

                    
                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Sign Up</button>
                    </div>

                    <div class="payment-summary" id ="payment-summary">

                    </div>

                </form>

                <form action="#" method="POST" name="payuForm">
                </form>
                <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg w-100 mt-4"  id ='checkOutbtn' onclick="proceedToCheckout()">Proceed to Checkout</button>
                </div>
            </div>
        </div>



    </div>


    <div id="response"></div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/js/utils.js"></script>
    <script>


        // const phoneInput = document.querySelector("#mobile");
        // const iti = window.intlTelInput(phoneInput, {
        //     initialCountry: "auto",
        //     geoIpLookup: function(callback) {
        //         fetch('https://ipinfo.io/json?token=2d4335ab2b31e0') // Replace `your_token` with an actual token
        //             .then(response => response.json())
        //             .then(data => callback(data.country))
        //             .catch(() => callback("IN"));
        //     },
        //     utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
        // });

        


        function isValidEmail(email) {
            // Regular expression for validating email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function isValidIndianMobileNumber(mobileNumber) {
            // Regular expression for Indian mobile number validation
            const mobileRegex = /^[6-9]\d{9}$/;
            return mobileRegex.test(mobileNumber);
        }



        $(document).ready(() => {

            let validMobileNumber = true;
            let input = document.querySelectorAll("#mobile");
                let iti_el = $('.iti.iti--allow-dropdown.iti--separate-dial-code');
                let iti;
            $(function () {
               

                if (iti_el.length) {
                    iti.destroy();
                }

                for (var i = 0; i < input.length; i++) {
                    iti = intlTelInput(input[i], {
                        autoHideDialCode: false,
                        autoPlaceholder: "aggressive",
                        initialCountry: "auto",
                        separateDialCode: true,
                        preferredCountries: ['in'],
                        customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                            return '' + selectedCountryPlaceholder.replace(/[0-9]/g, 'X');
                        },
                        geoIpLookup: function (callback) {
                            $.get('https://ipinfo.io', function () {}, "jsonp").always(function (resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "";
                                callback(countryCode);
                            });
                        },
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/js/utils.js" // Load utility script
                    });

                    // Focus/Click/Country Change Event
                    $('#mobile').on("focus click countrychange", function (e, countryData) {
                        var pl = $(this).attr('placeholder') + '';
                        var res = pl.replace(/X/g, '9');
                    });

                    // Validate Mobile Number on Focus Out
                    $('#mobile').on("focusout", function (e) {
                        const currentInput = e.target; // Get the current input element
                        const inputNumber = currentInput.value; // Get the input value
                        
                        // Check if the number is valid
                        console.log(iti.isValidNumber());
                        
                        if (iti.isValidNumber()) {
                            const intlNumber = iti.getNumber(); // Get full international number
                            console.log("Valid Number:", intlNumber);
                            validMobileNumber = true;
                            
                        } else {
                            validMobileNumber = false;
                            
                        }
                    });
                }


            })



            $('#mobile').on('change', async (e) => {
                let steps =  document.getElementById('steps');
                let otp =  document.getElementById('otp');
                steps.value = 1
                document.querySelector('.hidden-box-1').style.display = 'none';
                let inputs = document.querySelectorAll('.show-input');
                inputs.forEach(input => {
                    input.value = ''
                    input.classList.add('hide-input');
                    input.classList.remove('show-input');
                })
            });

            let call_id=0;
            form_type =''
            user_id = 0
            const fetchButton = document.getElementById('submit_button');

            $('#scheduleform').on('submit', async (e) => {
                e.preventDefault(); // Prevent default form submission

                try {

                    
                    
                    let steps =  document.getElementById('steps');
                    const inputsww = document.getElementById('submit_button');
                    console.log("steps",steps.value);

                    const errorElements = document.querySelectorAll('.error');
                    // Loop through and remove each element
                    errorElements.forEach(element => {
                    element.remove();
                    });

                    const inputs = document.querySelectorAll('[requiredInput]');
                    let isValid = true;

                    // Loop through each input and validate
                    inputs.forEach(input => {
                        input.classList.remove('is-invalid');
                        if (input.value.trim() === '' && !input.classList.contains('hide-input')) {
                            let errorElement = document.createElement('span');
                            errorElement.className = 'error'; // Add error class for styling
                            errorElement.textContent = `${input.name.charAt(0).toUpperCase() + input.name.slice(1)} is required.`;

                           
                            if(input.name == 'mobile'){
                                let mobilebox = document.getElementById('mobile-box');
                                mobilebox.after(errorElement);
                            }else{
                                input.after(errorElement);
                            }
                            input.classList.add('is-invalid');
                            isValid = false;

                        }

                        if(input.name == 'email' && input.value != '' ){
                            let checkEmail = isValidEmail(input.value)
                                if(isValidEmail(input.value) == false ){

                                    let errorElement = document.createElement('span');
                                    errorElement.className = 'error'; // Add error class for styling
                                    errorElement.textContent = `Please enter a valid email address.`;
                                    input.after(errorElement);
                                    input.classList.add('is-invalid');
                                    isValid = false;
                                }
                        }

                        if(input.name == 'mobile' && input.value != '' ){
                            // let checkMobile = isValidIndianMobileNumber(input.value)
                                // if(isValidIndianMobileNumber(input.value) == false ){
                                if(validMobileNumber == false ){
                                    
                                    let mobilebox = document.getElementById('mobile-box');
                                    let errorElement = document.createElement('span');
                                    errorElement.className = 'error'; // Add error class for styling
                                    errorElement.textContent = `Please enter a valid mobile Number.`;
                                    mobilebox.after(errorElement);
                                    input.classList.add('is-invalid');
                                    isValid = false;
                                }
                        }
                    });




                    if (isValid && steps.value == 1) {
                        const formElement = document.querySelector('#scheduleform');
                        const formData = new FormData(formElement);

                        const formObject = Object.fromEntries(formData.entries());
                        const mobileData = iti.getNumber();
                        // console.log(countryData);
                        formObject.mobile = mobileData
                        fetchButton.disabled = true;
                        fetchButton.innerHTML = 'Loading <span class="loader"></span>';
                        
                        try {
                        
                            // Send the POST request
                            const response = await fetch('http://127.0.0.1:8000/api/generate_otp', {
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
                            if(response.status == 200 &&  data && data.data > 0){
                                let checkIdinput =  document.querySelector('#id');
                                if(!checkIdinput){
                                    let hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'id';
                                    hiddenInput.id = 'id';
                                    hiddenInput.value = data.data;
                                    formElement.appendChild(hiddenInput);
                                }

                                document.querySelector('.hidden-box-1').style.display = 'block';
                                document.querySelector('#otp').classList.remove('hide-input');
                                document.querySelector('#otp').classList.add('show-input');

                                steps.value = '2'
                                inputsww.textContent = 'Verify OTP'
                            }else{
                                alert("Something went wrong.");
                            }

                        } catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Sign up';
                        } finally {
                            fetchButton.innerHTML = 'Verify OTP';
                            fetchButton.disabled = false;
                        }
                    }


                    let otp =  document.getElementById('otp');
                    let id =  document.getElementById('id');
                    if(isValid && steps.value == 2 && otp.value > 0 &&  id.value > 0){

                        fetchButton.disabled = true;
                        fetchButton.innerHTML = 'Loading <span class="loader"></span>';

                        try {
                            // Send the POST request
                            const response = await fetch('http://127.0.0.1:8000/api/verifyOtp', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                                },
                                body: JSON.stringify({otp:otp.value,id:id.value}),
                            });

                            const data = await response.json();
                            if(data.error){
                                let errorElement = document.createElement('span');
                                errorElement.className = 'error'; // Add error class for styling
                                errorElement.textContent = `${data.error}`;

                                // Insert the error message after the input field
                                otp.after(errorElement);
                                otp.classList.add('is-invalid');
                                isValid = false;

                                alert("Something went wrong.")
                            }else if(response.status == 200){
                                otp.classList.add('is-valid');
                                // document.getElementById('mobile').classList.add('is-valid')
                                // document.querySelector('.hidden-box-1').style.display = 'none';

                                let allbox  = document.querySelectorAll('.hidden-box-2');
                                allbox.forEach(box => {
                                    if(box.classList.contains('m-select-check')){

                                        box.classList.add('m-select');
                                    }else{
                                        box.style.display = 'block';
                                    }

                                })

                                let inputs  = document.querySelectorAll('.hide-input');
                                inputs.forEach(input => {
                                    input.classList.remove('hide-input');
                                    input.classList.add('show-input');

                                })

                                steps.value = '3'
                                // inputsww.textContent = 'Schedule Your Call'
                                isValid = false

                                let geturl = document.getElementById('formurl').value;
                                window.location.href = geturl+'?user_id='+data?.data.id;

                            }else{
                                alert("Something went wrong.")
                            }
                        } catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Verify OTP';
                        } finally {
                            fetchButton.innerHTML = 'Verify OTP';
                            fetchButton.disabled = false;
                        }
                    }

                } catch (error) {
                    alert('Error:'+error)
                }
            });

            let checkOutbtn = document.getElementById('checkOutbtn');

            checkOutbtn.onclick = async function(){
                let checkIdinput =  document.querySelector('#call_id').value;

                if(call_id && form_type && user_id){
                    console.log(checkIdinput,"checkIdinput",call_id);

                    let formObject = {id :call_id,form_type:form_type,user_id:user_id}
                    const response = await fetch('http://127.0.0.1:8000/api/payu-payment', {
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
                    console.log(response.status,'response.status');
                    if(response.status == 200){
                        // Render the response for debugging
                        document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);

                        // Select the form and set the action
                        const payuForm = document.forms['payuForm'];
                        payuForm.action = data?.url || ''; // Ensure the URL is set

                        // Add hidden inputs dynamically
                        if (data.data) {
                            for (const [key, value] of Object.entries(data.data)) {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = key;
                                hiddenInput.value = value;
                                payuForm.appendChild(hiddenInput);
                            }
                        }

                        // Submit the form
                        payuForm.submit();
                    }else{
                        alert(data)
                    }

                }else{
                    alert("Please fill the from.")
                }

            }
        });

    </script>

</body>

</html>