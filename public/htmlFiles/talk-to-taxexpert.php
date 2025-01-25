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
        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
        <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>

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
        .other_query_message_box{
            display: none;
        }
        #terms-box{
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
        .strike {
            color: #999;
            text-decoration: line-through;
        }

        .filepond--credits {
            display: none !important;
        }
    </style>


</head>
<?php

    $query_types = [
        ['value'=>'','label' => 'Select Your Query'],
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
                    <input type="hidden" id ="form_type" name="form_type" value="talk_to_tax_expert">

                    <div class="mb-3 text-center">
                        <h2>Talk To Our Tax Expert</h2>
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
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="mobile" class="form-control" id="mobile" name="mobile" requiredInput placeholder="Mobile">
                    </div> -->

                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <div id="mobile-box">
                            <input type="mobile" class="form-control" id="mobile" name="mobile" requiredInput placeholder="Mobile">
                        </div>
                    </div>


                    <div class="mb-3 hidden-box-1">
                        <label for="otp" class="form-label">OTP</label>
                        <input type="text" class="form-control hide-input" id="otp" name="otp" maxlength="6" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be exactly 6 digits">
                        <!-- <input type="number" class="form-control hide" id="otp" name="otp" requiredInput pattern="\d{6}" placeholder="******" title="OTP must be 6 digits."> -->
                    </div>

                    <!-- <div class="mb-3 hidden-box-2 m-select-check">
                        <label for="multi-select" class="form-label">Select Your Query</label>
                        <select id="multi-select" class="form-control hide-input" name="queryType" requiredInput  multiple="multiple">
                            <?php foreach ($query_types as $type): ?>
                                <option value="<?= $type['value']; ?>"><?= $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> -->

                    <div class="mb-3 hidden-box-2 m-select-check">
                        <label for="queryType" class="form-label">Select Your Query</label>
                        <select id="queryType" class="form-control hide-input" name="queryType" requiredInput>
                            <?php foreach ($query_types as $type): ?>
                                <option value="<?= $type['value']; ?>"><?= $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 other_query_message_box hidden-box-2">
                        <label for="other_query_message" class="form-label">Description</label>
                        <textarea class="form-control hide-input" id="other_query_message" name="other_query_message" rows="4" placeholder="Enter your message here" requiredInput maxlength="500" title="Message should not exceed 500 characters.">
                        </textarea>
                    </div>

                    <div class="mb-3 hidden-box-2">
                    <label for="datepicker" class="form-label">Select Date & Time</label>
                    <input type="text" id="datepicker" name="datetime" class="form-control hide-input" requiredInput placeholder="Y-m-d H:i">
                    </div>

                    <div class="mb-3 hidden-box-2 ">
                        <label for="multi-select" class="form-label">Choose Plan</label>
                        <select id="plan" class="form-control hide-input" requiredInput name="plan">
                        <option  value="">select value</option>
                            <?php foreach ($plan_types as $type): ?>

                                <option value="<?= $type['value']; ?>"><?= $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 hidden-box-2">
                        <label for="multi-select" class="form-label">Choose Language</label>
                        <select id="language" class="form-control hide-input" requiredInput name="language">
                        <option value="">select value</option>
                            <?php foreach ($language as $type): ?>

                                <option value="<?= $type['value']; ?>"><?= $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 hidden-box-2">
                        <label for="document" class="form-label">Select Document</label>
                        <!-- <input type="file" class="form-control hide-input" id="document" name="document[]" multiple="multiple"  accept=".jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.pdf" title="select jpeg,jpg,png,doc,docx,xls,xlsx,pdf"> -->
                        <input type="file" class="form-control hide-input" id="document" name="document[]" multiple="multiple"  requiredInput >

                    </div>

                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Sign Up</button>
                    </div>

                    <div class="payment-summary" id ="payment-summary">

                    </div>
                    <div class="mt-4" id ="terms-box">
                        <input type="checkbox" name="terms" class='hide-input' id="terms" value='1'>
                        <a href="http://" target="_blank">T&C apply</a>
                    </div>

                </form>

                <form action="#" method="POST" name="payuForm">
                </form>
                <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg w-100 mt-4"  id ='checkOutbtn' onclick="proceedToCheckout()">Pay Now</button>
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

    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>

<script>


        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginImagePreview
        );

        let uploadedFiles = []; // Array to store uploaded file names

        // Initialize FilePond
        const pond = FilePond.create(document.querySelector('#document'), {
            allowMultiple: true,
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    const formData = new FormData();
                    formData.append('files[]', file);

                    fetch('http://127.0.0.1:8000/api/commonUploadFile', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                console.log("asdasd");

                                data.files.forEach(fileInfo => {
                                    uploadedFiles.push(fileInfo); // Store uploaded file name
                                });
                                // console.log('Uploaded Files:', uploadedFiles); // Log the uploaded files array
                                load(data.files.map(file => file.name)); // Pass file name to FilePond
                            } else {
                                error('Upload failed');
                            }
                        })
                        .catch(() => {
                            error('Upload error');
                        });
                }
            }
        });

        pond.on('removefile', (error, file) => {
            if (error) {
                console.error('Error removing file:', error);
                return;
            }

            // Find the file to be deleted by matching originalName and get the uploadedFile
            const fileToDelete = uploadedFiles.find(uploadedFile => uploadedFile.originalName === file.filename);

            if (fileToDelete) {
                // Call API to delete the file from the server using uploadedFile (not originalName)
                fetch('http://127.0.0.1:8000/api/deleteFile', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        uploadedFile: fileToDelete.uploadedFile, // Use the uploadedFile key to delete the file
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('File deleted from server:', fileToDelete.uploadedFile);
                    } else {
                        console.error('Error deleting file:', data.message);
                    }
                })
                .catch(error => {
                    console.error('API request error:', error);
                });

                // Remove the file from the uploadedFiles array after deletion
                uploadedFiles = uploadedFiles.filter(uploadedFile => uploadedFile.originalName !== file.filename);

                console.log('Updated Uploaded Files:', uploadedFiles); // Log the updated array after removal
            }
        });



        // async function getPublicHolidays(year) {
        //     const response = await fetch(`https://calendarific.com/api/v2/holidays?&api_key=yLvtyEf6SznkBEWoyLrZETzXH2V3l7cW&country=IN&year=2024`);
        //     const data = await response.json();
        //     let holidays = data?.response?.holidays;
        //     // console.log(holidays);

        //     return holidays.map(holiday => holiday.date.iso); // Return array of holiday dates (YYYY-MM-DD)
        // }


        async function fetchPublicHolidays(year) {
            try {

                    const API_KEY = 'AIzaSyD5_j5XtDqu_IBjkAOu8w_FraLVQcDCeKk';
                    const CALENDAR_ID = 'en.indian%23holiday@group.v.calendar.google.com'; // URL-encoded Calendar ID

                    // Set year dynamically
                    // const year = 2024; // Change the year as needed
                    const timeMin = `${year}-01-01T00:00:00Z`; // Start of the year
                    const timeMax = `${year}-12-31T23:59:59Z`; // End of the year

                    const URL = `https://www.googleapis.com/calendar/v3/calendars/${CALENDAR_ID}/events?key=${API_KEY}&timeMin=${timeMin}&timeMax=${timeMax}`;

                const response = await fetch(URL);

                // Check if the response is OK
                if (!response.ok) {
                throw new Error(`HTTP Error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log(`Public Holidays for ${year}:`, data);

                // Display the events in the console
                if (data.items && data.items.length > 0) {
                    let dates = [];

                    data.items.forEach((event) => {
                        if (/Public holiday/.test(event.description)) {
                            const start = event.start.date || event.start.dateTime;
                            dates.push(start);
                            console.log(`${start} - ${event.summary}`);
                        }

                    });
                    return dates
                    // console.log(dates,"datesdatesdates");

                } else {
                    console.log(`No public holidays found for ${year}.`);
                }
            } catch (error) {
                console.error('Error fetching public holidays:', error.message);
            }
        }

        // Disable weekends and public holidays
        async function initDatePicker() {
            const today = new Date();
            const nextMonth = new Date();
            nextMonth.setMonth(today.getMonth() + 1); // Get the date next month

            const publicHolidays = await fetchPublicHolidays(today.getFullYear());
            // console.log(publicHolidays,'publicHolidays');
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
        // fetchPublicHolidays();



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
            form_type ='talk_to_tax_expert'
            user_id = 0;

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
                        formObject.mobile = mobileData;
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

                            if(response.status == 200 && data && data.data > 0){
                                let checkIdinput =  document.querySelector('#id');
                                if(!checkIdinput){
                                    let hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'id';
                                    hiddenInput.id = 'id';
                                    hiddenInput.value = data.data;
                                    formElement.appendChild(hiddenInput);
                                }
                                user_id = data.data;
                                document.querySelector('.hidden-box-1').style.display = 'block';
                                document.querySelector('#otp').classList.remove('hide-input');
                                document.querySelector('#otp').classList.add('show-input');

                                steps.value = '2'
                                inputsww.textContent = 'Verify OTP'
                            }

                        } catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Retry';
                        }finally {
                            fetchButton.innerHTML = 'Verify OTP';
                            fetchButton.disabled = false;
                        }
                    //    document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);
                    }


                    let otp =  document.getElementById('otp');
                    let id =  document.getElementById('id');
                    if(isValid && steps.value == 2 && otp.value > 0 &&  id.value > 0){
                        // Send the POST request
                        fetchButton.disabled = true;
                        fetchButton.innerHTML = 'Loading <span class="loader"></span>';
                        try {
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
                            }else if (response.status == 200){
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
                                inputsww.textContent = 'Schedule Your Call'
                                isValid = false

                            }else{
                                alert("Something went wrong.")
                            }
                        } catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Retry';
                        }finally {
                            fetchButton.innerHTML = 'Schedule Your Call';
                            fetchButton.disabled = false;
                        }
                        // document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);

                    }

                    if(isValid && steps.value == 3){

                        const formElement = document.querySelector('#scheduleform');
                        const formData = new FormData(formElement);

                        uploadedFiles.forEach((file, index) => {
                           formData.append('uploadedFile[' + index + ']', file.uploadedFile);
                        });

                        // const formObject = Object.fromEntries(formData.entries());
                        // const fileInput = document.getElementById("document");
                        // const files = fileInput.files;

                        // for (let i = 0; i < files.length; i++) {
                        //     formData.append("files[]", files[i]); // Append each file to FormData
                        // }
                        // const selectedValues = $('#multi-select').val() || [];
                        // formObject.QueryType = selectedValues
                        // formData.append("QueryType", selectedValues);
                        // console.log(selectedValues,"====selectedValues",formObject);
                        fetchButton.disabled = true;
                        fetchButton.innerHTML = 'Loading <span class="loader"></span>';
                        try {
                            const response = await fetch('http://127.0.0.1:8000/api/calculatePlanForCall', {
                                method: 'POST',
                                headers: {
                                    // 'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                                },
                                body: formData,
                            });

                            // Parse the JSON response
                            const data = await response.json();

                            if(response.status == 200){
                            // Render the response for debugging
                                // document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);
                                let checkIdinput =  document.querySelector('#call_id');
                                if(!checkIdinput){
                                    let hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'call_id';
                                    hiddenInput.id = 'call_id';
                                    hiddenInput.value = data.call_id;
                                    formElement.appendChild(hiddenInput);
                                }

                                call_id =  data.call_id;

                                // console.log(data.call_id,"==",formData?.form_type,"==",formData?.id);


                                let html=`<div>
                                    <h4 class="text-left mb-4 mt-4">Payment Summary</h4>
                                    <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <!-- Subscription Items -->
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <!-- Item 1 -->
                                                <div id='cart-details'>
                                                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                                    <div>
                                                        <h6>Schedule Call</h6>
                                                        <p class="text-muted mb-1">Regarding ::${data?.regarding}</p>
                                                        ${data?.getPlan?.url && data?.getPlan?.url !='' ? `<a href="${data?.getPlan?.url}" target="_blank">Read more</a>` : ''}
                                                    </div>
                                                    <div class="fw-bold">₹${data?.getPlan?.value}</div>
                                                    </div>
                                                </div>

                                                <!-- Coupon Code -->
                                                <div class="mt-4 border-bottom pb-3">
                                                    <h6>Have a Coupon Code?</h6>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="coupon-code" name='coupon' placeholder="Enter coupon code" value='${data?.inputCoupon && data?.inputCoupon != 'undefined' ? data?.inputCoupon:''}'>
                                                        <button class="btn btn-primary" id="apply-coupon">Apply</button>
                                                        <button class="btn btn-danger" id="remove-coupon">Remove Coupon</button>
                                                    </div>
                                                        <p id="coupon-message" class="text-success mt-2 d-none">Coupon applied successfully!</p>
                                                </div>
                                            ${data?.coupon && data?.coupon?.id > 0 ? `
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-3">
                                                        <h6>Coupon Code :: <strong>${data.coupon.code}</strong></h6>
                                                        <span class="fw-bold">₹${data.lessAmount}</span>
                                                    </div>
                                                ` : `${data?.coupon != null ?`
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-3">
                                                        <h6>Coupon Code :: <strong style="color">${data.coupon}</strong></h6>
                                                    </div>`:''}
                                                `}

                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <h6>Sub Total:</h6>
                                                    <div>
                                                        ${data?.defaultOfferAmount && data?.defaultOfferAmount > 0 ? `<span class="strike">₹${data?.defaultOfferAmount}</span>` :''}
                                                        <span class="fw-bold">₹${data?.subtotal}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <h6>GST 18%:</h6>
                                                    <span class="fw-bold">₹${data?.gstCharge}</span>
                                                </div>

                                                <!-- Total -->
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <h6>Total:</h6>
                                                    <div>

                                                        <span class="fw-bold" style="font-size:20px;">₹${data?.amount}</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>



                                        <!-- Checkout Button -->

                                    </div>
                                    </div>
                                </div>`;


                                document.getElementById("checkOutbtn").style.display = 'block'
                                document.getElementById("payment-summary").innerHTML = html;
                                document.getElementById("payment-summary").style.display = 'block'
                                document.getElementById("terms-box").style.display = 'block'
                                document.querySelector('#terms').classList.remove('hide-input');
                            }else{
                                alert('Something went wrong.')
                            }
                        } catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Retry';
                        }finally {
                            fetchButton.innerHTML = 'Schedule Your Call';
                            fetchButton.disabled = false;
                        }
                        // if(formObject.coupon != null  && formObject.coupon != 'null')
                        // steps.value = '4'

                    }

                } catch (error) {
                    console.error('Error:', error);
                }
            });

            let checkOutbtn = document.getElementById('checkOutbtn');

            checkOutbtn.onclick = async function(){
                let checkIdinput =  document.querySelector('#call_id').value;
                let terms =  document.querySelector('#terms');
                let isValid = true;
                if (terms.checked) {
                    isValid = true;
                } else {
                    isValid = false;
                }

                if(call_id && form_type && user_id && isValid){
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
