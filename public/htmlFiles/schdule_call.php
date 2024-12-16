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
                <form id="scheduleform" method="POST" name="scheduleform">
                    <input type="hidden" id="steps" name="steps" value="1">
                    <div class="mb-3 text-center">
                        <h2>Schedule Call</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control " id="name" name="name" requiredInput>

                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                        <input type="text" class="form-control " id="exampleInputEmail1" name="email" requiredInput>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" requiredInput>
                    </div> -->
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="mobile" class="form-control" id="mobile" name="mobile" requiredInput>
                    </div>

                    <div class="mb-3 otp-box hidden-box">
                        <label for="otp" class="form-label">Otp</label>
                        <input type="text" class="form-control hide-input" id="otp" name="otp" maxlength="6" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be exactly 6 digits">
                        <!-- <input type="number" class="form-control hide" id="otp" name="otp" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be 6 digits."> -->
                    </div>

                    <div class="mb-3 hidden-box-2">
                        <select class="form-select" name="typeselect" aria-label="Default select example">
                            <option selected>Open this select menu</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>

                    <div class="mb-3 hidden-box-2">
                    <input type="text" id="datepicker" name="datetime" class="form-control" placeholder="Y-m-d H:i"> 
                    </div>
                    <!-- <div class="mb-3 otp-box hidden-box" >
                        <label for="otp" class="form-label">Otp</label>
                        <input type="text" class="form-control hidden-input" id="otp" name="otp" maxlength="6" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be exactly 6 digits">
                         <input type="number" class="form-control hide" id="otp" name="otp" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be 6 digits.">
                    </div> -->

                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit</button>
                    </div>


                </form>

                <form action="#" method="POST" name="payuForm">
                </form>
            </div>
        </div>



    </div>


    <div id="response"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>


        // async function getPublicHolidays(year) {
        //     const response = await fetch(`https://holidayapi.com/v1/holidays?pretty&key=add32bae-dd85-48bf-b707-9bb08892b631&country=IN&year=2023`);
        //     const data = await response.json();
        //     let jk = data.holidays
        //     return jk.map(holiday => holiday.date); // Return array of holiday dates (YYYY-MM-DD)
        // }

        // // Disable weekends and public holidays
        // async function initDatePicker() {
        //     const today = new Date();
        //     const nextMonth = new Date();
        //     nextMonth.setMonth(today.getMonth() + 1); // Get the date next month

        //     const publicHolidays = await getPublicHolidays(today.getFullYear());

        //     flatpickr("#datepicker", {
        //         minDate: today, // Allow dates starting from today
        //         maxDate: nextMonth, // Allow dates until the end of next month
        //         disable: [
        //             // Disable weekends (Saturday and Sunday)
        //             function(date) {
        //                 return date.getDay() === 0 || date.getDay() === 6; // 0 is Sunday, 6 is Saturday
        //             },
        //             // Disable public holidays
        //             function(date) {
        //                 const dateStr = date.toISOString().split('T')[0]; // Convert date to YYYY-MM-DD format
        //                 return publicHolidays.includes(dateStr); // If the date is a holiday, disable it
        //             }
        //         ],
        //         dateFormat: "Y-m-d", // Format the date as YYYY-MM-DD
        //     });
        // }

        // // Initialize the date picker
        // initDatePicker();






        async function getPublicHolidays(year) {
            const response = await fetch(`https://calendarific.com/api/v2/holidays?&api_key=yLvtyEf6SznkBEWoyLrZETzXH2V3l7cW&country=IN&year=2024`);
            const data = await response.json();
            let holidays = data?.response?.holidays;
            // console.log(holidays);
            
            return holidays.map(holiday => holiday.date.iso); // Return array of holiday dates (YYYY-MM-DD)
        }

        // Disable weekends and public holidays
        async function initDatePicker() {
            const today = new Date();
            const nextMonth = new Date();
            nextMonth.setMonth(today.getMonth() + 1); // Get the date next month

            const publicHolidays = await getPublicHolidays(today.getFullYear());

            // flatpickr("#datepicker", {
            //     minDate: today, // Allow dates starting from today
            //     maxDate: nextMonth, // Allow dates until the end of next month
            //     enableTime: true, // Enable time selection
            //     noCalendar: false, // Show calendar for date selection
            //     dateFormat: "Y-m-d H:i", // Format the date and time (YYYY-MM-DD HH:mm)
            //     time_24hr: true, // 24-hour time format
            //     disable: [
            //         // Disable weekends (Saturday and Sunday)
            //         function(date) {
            //             return date.getDay() === 0 //|| date.getDay() === 6; // 0 is Sunday, 6 is Saturday
            //         },
            //         // Disable public holidays
            //         function(date) {
            //             const dateStr = date.toISOString().split('T')[0]; // Convert date to YYYY-MM-DD format
            //             return publicHolidays.includes(dateStr); // If the date is a holiday, disable it
            //         }
            //     ],  
            // });


            flatpickr("#datepicker", {
                minDate: today, // Allow dates starting from today
                maxDate: nextMonth, // Allow dates until the end of next month
                enableTime: true, // Enable time selection
                noCalendar: false, // Show calendar for date selection
                dateFormat: "Y-m-d H:i", // Format the date and time (YYYY-MM-DD HH:mm)
                time_24hr: true, // 24-hour time format
                disable: [
                    // Disable weekends (Saturday and Sunday)
                    function (date) {
                        return date.getDay() === 0 || date.getDay() === 6; // 0 is Sunday, 6 is Saturday
                    },
                    // Disable public holidays
                    function (date) {
                        const dateStr = date.toISOString().split('T')[0]; // Convert date to YYYY-MM-DD format
                        return publicHolidays.includes(dateStr); // If the date is a holiday, disable it
                    }
                ],
                minTime: "09:00", // Minimum time selectable
                maxTime: "19:00", // Maximum time selectable
                onReady: function (selectedDates, dateStr, instance) {
                    instance.config.minTime = "09:00"; // Set the minimum time
                    instance.config.maxTime = "19:00"; // Set the maximum time
                },
                onValueUpdate: function (selectedDates, dateStr, instance) {
                    const time = instance.input.value.split(" ")[1]; // Get the selected time (HH:mm)
                    const [hours, minutes] = time.split(":").map(Number); // Convert to numbers
                    if (hours < 9 || (hours === 19 && minutes > 0) || hours > 19) {
                        alert("Please select a time between 9:00 AM and 7:00 PM.");
                        instance.clear(); // Clear the invalid selection
                    }
                }
            });
        }

        // Initialize the date picker
        initDatePicker();













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
            
            $('#mobile').on('change', async (e) => {
                let steps =  document.getElementById('steps');
                let otp =  document.getElementById('otp');
                steps.value = 1
                document.querySelector('.hidden-box').style.display = 'none';
                let inputs = document.querySelectorAll('.show-input');
                inputs.forEach(input => {
                    input.value = ''
                    input.classList.add('hide-input');
                    input.classList.remove('show-input');
                })
            });


            $('#scheduleform').on('submit', async (e) => {
                e.preventDefault(); // Prevent default form submission

                try {
                    let steps =  document.getElementById('steps');
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

                            // Insert the error message after the input field
                            input.after(errorElement);
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
                                }else{
                                    // input.classList.add('is-valid');
                                }
                        }
                        
                        if(input.name == 'mobile' && input.value != '' ){
                            // let checkMobile = isValidIndianMobileNumber(input.value)
                                if(isValidIndianMobileNumber(input.value) == false ){
                                    
                                    let errorElement = document.createElement('span');
                                    errorElement.className = 'error'; // Add error class for styling
                                    errorElement.textContent = `Please enter a valid mobile Number.`;
                                    input.after(errorElement);
                                    input.classList.add('is-invalid');
                                    isValid = false;
                                }else{
                                    // input.classList.add('is-valid');
                                }
                        }else{
                            // if( !input.classList.contains('hide')){
                            //     input.classList.add('is-valid');
                            // }
                        }
                    });




                    if (isValid && steps.value == 1) {
                        const formElement = document.querySelector('#scheduleform');
                        const formData = new FormData(formElement);

                        const formObject = Object.fromEntries(formData.entries());
                        console.log("formObject",formObject);
                        
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
                        
                        if(data && data.data > 0){
                            let checkIdinput =  document.querySelector('#id');
                            if(!checkIdinput){
                                let hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'id';
                                hiddenInput.id = 'id';
                                hiddenInput.value = data.data;
                                formElement.appendChild(hiddenInput);
                            }

                            document.querySelector('.otp-box').style.display = 'block';
                            document.querySelector('#otp').classList.remove('hide-input');
                            document.querySelector('#otp').classList.add('show-input');
                            const inputsww = document.getElementById('submit_button');
                            steps.value = '2'
                            inputsww.textContent = 'Veryfy OTP'
                        }
                        // Render the response for debugging
                        document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);
                    }


                    //verify Otp
                    let otp =  document.getElementById('otp');
                    let id =  document.getElementById('id');
                    if(isValid && steps.value == 2 && otp.value > 0 &&  id.value > 0){
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
                        }else{
                            otp.classList.add('is-valid');
                            steps.value = '3'
                        }
                        document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);

                    }



                    let jk = 1;
                    if (isValid && steps.value == 3) {
                        const formElement = document.querySelector('#scheduleform');
                        const formData = new FormData(formElement);


                        // Convert formData to a plain object
                        const formObject = Object.fromEntries(formData.entries());

                        // Send the POST request
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
                    }

                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

    </script>

</body>

</html>