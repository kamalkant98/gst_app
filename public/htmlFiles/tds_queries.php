<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TDS/TCS Queries</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
        <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
</head>
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
        .select2-container{width: 100% !important;}
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
        #terms-box{
            display: none;
        }
        .strike {
            color: #999;
            text-decoration: line-through;
        }
        .selectTime-group{
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .selectTime-group > .form-check {
            margin-right: 10px;
        }
        .hidden-box-3 {
            display: none;
        }
        .hidden-box-4 {
            display: none;
        }
        .filepond--credits {
            display: none !important;
        }
    </style>

    <?php
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
                <form id="tds_queries" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id ="form_type" name="form_type" value="tds_queries">
                    <input type="hidden" id ="user_id" name="user_id" value="<?= !empty($_GET['user_id'])?$_GET['user_id']:''; ?>">
                    <div class="mb-3 text-center">
                        <h2>CONNECT US FOR TDS/TCS QUERIES</h2>
                    </div>
                        <?php


                        ?>

                    <div class="mb-3 ">
                        <label for="type" class="form-label">Tan No.</label>
                        <input type="text" class="form-control " id="tan_number" name="tan_number" requiredInput placeholder="TAN No." show_name='Tan No.'>


                    </div>
                    <div class="mb-3 ">
                        <label for="type_of_return" class="form-label w-100">Type of return</label>
                        <select id="type_of_return" class="form-control" name="type_of_return" requiredInput show_name="Type of return">
                            <option  value="">select value</option>
                            <option  value="1">24Q</option>
                            <option  value="2">26Q</option>
                            <option  value="3">27Q</option>
                            <option  value="4">26QB</option>
                        </select>
                    </div>

                    <div class="mb-3 first_select hidden-box-2">
                        <label for="no_of_employees" class="form-label w-100">No of employees</label>
                        <select id="no_of_employees" class="form-control first_select hide-input selectEmp" name="no_of_employees" requiredInput show_name="No of employees">
                            <option  value="">select value</option>
                            <option  value="1">1 to 10</option>
                            <option  value="2">10 to 50</option>
                            <option  value="3">50 to 100</option>
                            <option  value="4">More than 100</option>
                        </select>
                    </div>

                    <div class="mb-3 first_select hidden-box-2">
                        <label for="tax_planning" class="form-label w-100">Computation/Tax Planning of Employees</label>
                        <select id="tax_planning" class="form-control first_select hide-input" name="tax_planning" requiredInput show_name="Tax planning">
                            <option  value="">select value</option>
                            <option  value="1">Yes</option>
                            <option  value="2">No</option>
                        </select>
                    </div>


                    <div class="mb-3 secend_select hidden-box-2">
                        <label for="no_of_entries" class="form-label w-100">No of entries</label>

                        <select id="no_of_entries" class="form-control secend_select hide-input selectEmp" name="no_of_entries" requiredInput show_name = "No of entries">
                            <option  value="">select value</option>
                            <option  value="1">Up to 100</option>
                            <option  value="2">100 to 250</option>
                            <option  value="3">250 to 500</option>
                            <option  value="4">More than 500</option>
                        </select>
                    </div>

                    <div class="mb-3 third_select hidden-box-2">
                        <label for="no_of_entries_27" class="form-label w-100">No of entries</label>
                        <select id="no_of_entries_27" class="form-control third_select hide-input selectEmp" name="no_of_entries_27" requiredInput show_name="No of entries">
                            <option  value="">select value</option>
                            <option  value="1">Up to 50</option>
                            <option  value="2">50 to 100</option>
                            <option  value="3">100 to 200</option>
                            <option  value="4">More than 200</option>
                        </select>
                    </div>

                    <div class="form-section hidden-box-3" id ="selectTime-box">
                        <label class="form-label" >Select a Time:</label>
                        <div class="selectTime-group">
                            <div class="form-check">
                                <input class="form-check-input hide-input" requiredInput type="radio" name="selectTime" id="flexRadioDefault1"  value="1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                Connect Now
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input hide-input" requiredInput type="radio" name="selectTime" id="flexRadioDefault2" value="2">
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Choose Date & Time
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3 hidden-box-4">
                        <label for="datepicker" class="form-label">Select Date & Time</label>
                        <input type="text" id="datepicker" name="datetime" class="form-control hide-input" requiredInput placeholder="Y-m-d H:i" show_name="DateTime">
                    </div>

                    <div class="mb-3 hidden-box-3">
                        <label for="multi-select" class="form-label">Choose Language</label>
                        <select id="language" class="form-control hide-input" requiredInput name="language" show_name="language">
                        <option value="">select value</option>
                            <?php foreach ($language as $type): ?>

                                <option value="<?= $type['value']; ?>"><?= $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 ">
                        <label for="document" class="form-label">Select Document</label>
                        <!-- <input type="file" class="form-control hide-input" id="document" name="document[]" multiple="multiple"  accept=".jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.pdf" title="select jpeg,jpg,png,doc,docx,xls,xlsx,pdf"> -->
                        <input type="file" class="form-control" id="document" name="document[]" multiple="multiple"  requiredInput >

                    </div>

                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit</button>
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
                // console.log(`Public Holidays for ${year}:`, data);

                // Display the events in the console
                if (data.items && data.items.length > 0) {
                    let dates = [];

                    data.items.forEach((event) => {
                        if (/Public holiday/.test(event.description)) {
                            const start = event.start.date || event.start.dateTime;
                            dates.push(start);
                            // console.log(`${start} - ${event.summary}`);
                        }

                    });
                    return dates


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

            // const publicHolidays = await getPublicHolidays(today.getFullYear());
            const publicHolidays = await fetchPublicHolidays(today.getFullYear());
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
        initDatePicker();


        $(document).ready(function (e) {

            function isValidTAN(tan) {
                // Regular expression to validate TAN number format (4 letters, 5 digits, 1 letter)
                const tanRegex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
                return tanRegex.test(tan);
            }

            $('#type_of_return').on('change', function (event) {
                $('#no_of_employees').val('').trigger('change');
                $('#no_of_entries').val('').trigger('change');
                $('#no_of_entries_27').val('').trigger('change');
                // triggerChange('1')
                var selectedValue = $(this).val();
                if(selectedValue == 1){
                    $('.first_select').show().removeClass('hide-input');
                    $('.secend_select').hide().addClass('hide-input').val('');
                    $('.third_select').hide().addClass('hide-input').val('');
                }else if(selectedValue == 2){
                    $('.first_select').hide().addClass('hide-input').val('');
                    $('.secend_select').show().removeClass('hide-input');
                    $('.third_select').hide().addClass('hide-input').val('');

                }else if(selectedValue == 3){
                    $('.first_select').hide().addClass('hide-input').val('');
                    $('.secend_select').hide().addClass('hide-input').val('');
                    $('.third_select').show().removeClass('hide-input');

                }else{
                    $('.first_select').hide().addClass('hide-input').val('');
                    $('.secend_select').hide().addClass('hide-input').val('');
                    $('.third_select').hide().addClass('hide-input').val('');
                }
            });




            $('.selectEmp').on('change', function (event) {
                var selectedValue = $(this).val();

                const elements = document.querySelectorAll('.selectTimeClass');
                elements.forEach(element => {
                    element.remove();
                });
                let language = document.getElementById('language');
                let datepicker = document.getElementById('datepicker');

                datepicker.classList.remove('show-input');
                datepicker.classList.add('hide-input');
                // const radioButtons = document.querySelectorAll('input[name="selectTime"]');

                // // Iterate through each radio button and uncheck them
                // radioButtons.forEach(radio => {
                //     radio.checked = false;
                // });
                $('.hidden-box-4').hide();
                if(selectedValue == 4){
                    $('.hidden-box-3').show();
                    let inputs = document.querySelectorAll('input[name="selectTime"]');
                    inputs.forEach(input => {
                        input.classList.add('show-input');
                        input.classList.remove('hide-input');
                        input.checked = false;
                    })

                    language.classList.add('show-input');
                    language.classList.remove('hide-input');

                }else{
                    $('.hidden-box-3').hide();
                    let inputs = document.querySelectorAll('input[name="selectTime"]');
                    inputs.forEach(input => {
                        input.classList.remove('show-input');
                        input.classList.add('hide-input');
                        input.checked = false;
                    })
                    let language = document.getElementById('language');
                    language.classList.remove('show-input');
                    language.classList.add('hide-input');

                }
            })

            // $('#no_of_entries').on('change', function (event) {
            //     var selectedValue = $(this).val();
            //     const elements = document.querySelectorAll('.selectTimeClass');
            //     elements.forEach(element => {
            //         element.remove();
            //     });

            //     if(selectedValue == 4){
            //         $('.hidden-box-3').show();
            //         let inputs = document.querySelectorAll('input[name="selectTime"]');
            //         inputs.forEach(input => {
            //             input.value = ''
            //             input.classList.add('show-input');
            //             input.classList.remove('hide-input');
            //         })
            //         let language = document.getElementById('language');
            //         language.classList.add('show-input');
            //         language.classList.remove('hide-input');

            //     }else{
            //         $('.hidden-box-3').hide();
            //         let inputs = document.querySelectorAll('input[name="selectTime"]');
            //         inputs.forEach(input => {
            //             input.value = ''
            //             input.classList.remove('show-input');
            //             input.classList.add('hide-input');
            //         })
            //         let language = document.getElementById('language');
            //         language.classList.remove('show-input');
            //         language.classList.add('hide-input');
            //     }

            // })

            // $('#no_of_entries_27').on('change', function (event) {
            //     var selectedValue = $(this).val();
            //     const elements = document.querySelectorAll('.selectTimeClass');
            //     elements.forEach(element => {
            //         element.remove();
            //     });

            //     if(selectedValue == 4){
            //         $('.hidden-box-3').show();
            //         let inputs = document.querySelectorAll('input[name="selectTime"]');
            //         inputs.forEach(input => {
            //             input.value = ''
            //             input.classList.add('show-input');
            //             input.classList.remove('hide-input');
            //         })
            //         let language = document.getElementById('language');
            //         language.classList.add('show-input');
            //         language.classList.remove('hide-input');

            //     }else{
            //         $('.hidden-box-3').hide();
            //         let inputs = document.querySelectorAll('input[name="selectTime"]');
            //         inputs.forEach(input => {
            //             input.value = ''
            //             input.classList.remove('show-input');
            //             input.classList.add('hide-input');
            //         })
            //         let language = document.getElementById('language');
            //         language.classList.remove('show-input');
            //         language.classList.add('hide-input');
            //     }

            // })


            let chooseOption = document.querySelector('input[name="selectTime"][value="2"]');
            let dateTimePicker = document.querySelector('#datepicker');
            document.querySelectorAll('input[name="selectTime"]').forEach((radio) => {
                radio.addEventListener("change", () => {
                    if (chooseOption.checked) {
                        let allbox  = document.querySelectorAll('.hidden-box-4');
                        allbox.forEach(box => {

                                box.style.display = 'block';
                        })
                        dateTimePicker.classList.remove('hide-input');
                        dateTimePicker.classList.add('show-input');
                        // dateTimePicker.classList.remove("hidden");
                    } else {
                        let allbox  = document.querySelectorAll('.hidden-box-4');
                        allbox.forEach(box => {
                            box.style.display = 'none';
                        })
                        dateTimePicker.classList.add('hide-input');
                        dateTimePicker.classList.remove('show-input');

                    }
                });
            });



            let call_id=0;
            form_type =''
            user_id = 0
            const fetchButton = document.getElementById('submit_button');
            $('#tds_queries').on('submit', async (e) => {

                try {

                    e.preventDefault(); // Prevent the default form submit
                    let formElement = document.querySelector('#tds_queries');
                    const formData = new FormData(formElement);
                    const formObject = Object.fromEntries(formData.entries());
                    let chooseOptions = document.querySelector('input[name="selectTime"][value="2"]');
                    console.log("chooseOption",formObject);

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
                        if (input.value.trim() === '' && !input.classList.contains('hide-input')  && input.name != 'selectTime') {
                            // console.log(input.show_name);
                            let showName = input.getAttribute('show_name');
                            showName = showName && showName!= null ? showName : input.name;
                            console.log(showName);

                            let errorElement = document.createElement('span');
                            errorElement.className = 'error'; // Add error class for styling
                            errorElement.textContent = `${showName.charAt(0).toUpperCase() + showName.slice(1)} is required.`;

                            input.after(errorElement);
                            input.classList.add('is-invalid');
                            isValid = false;

                        }

                        if (input.name == 'tan_number' && input.value != '') {
                            let checkTAN = isValidTAN(input.value);
                            if (checkTAN == false) {
                                let errorElement = document.createElement('span');
                                errorElement.className = 'error'; // Add error class for styling
                                errorElement.textContent = `Please enter a valid TAN number.`;
                                input.after(errorElement);
                                input.classList.add('is-invalid');
                                isValid = false;
                            }
                        }

                    });

                    const selectedOption = document.querySelector('input[name="selectTime"]:checked');
                    const selectedOptionw = document.querySelector('input[name="selectTime"]');
                    if (!selectedOption && !selectedOptionw.classList.contains('hide-input')) {
                            let mobilebox = document.getElementById('selectTime-box');
                            let errorElement = document.createElement('span');
                            errorElement.className = 'error selectTimeClass'; // Add error class for styling
                            errorElement.textContent = `Please select Date time option.`;
                            mobilebox.after(errorElement);
                            // input.classList.add('is-invalid');
                            isValid = false;
                            console.log("false option ");

                    }



                if (isValid) {
                    // Send the POST request
                    fetchButton.disabled = true;
                    fetchButton.innerHTML = 'Loading <span class="loader"></span>';

                    uploadedFiles.forEach((file, index) => {
                        formData.append('uploadedFile[' + index + ']', file.uploadedFile);
                    });

                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/tds-queries/store', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                            },
                            body: formData, // Pass the FormData object directly
                        });

                        // Parse the JSON response
                        const data = await response.json();

                            // Render the response for debugging
                    // document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);
                        if(response.status == 200){
                            if(data?.amount > 0 && !data?.redirect_url){

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
                                form_type = formData?.form_type;
                                user_id = formData?.id;

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
                                                        <h6>TDS/TCS QUERIES</h6>
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
                                                        <input type="text" class="form-control" id="coupon-code" name='coupon' placeholder="Enter coupon code" value='${data?.inputCoupon}'>
                                                        <button class="btn btn-primary" id="apply-coupon">Apply</button>
                                                        <button class="btn btn-danger" id="remove-coupon">Remove Coupon</button>
                                                    </div>
                                                        <p id="coupon-message" class="text-success mt-2 d-none">Coupon applied successfully!</p>
                                                </div>
                                            ${data?.coupon && data?.coupon?.id > 0 ? `
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <h6>Coupon Code :: <strong>${data.coupon.code}</strong></h6>
                                                        <span class="fw-bold">₹${data.lessAmount}</span>
                                                    </div>
                                                ` : `${data?.coupon != null ?`
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
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
                                document.getElementById("payment-summary").style.display = 'block';
                                document.getElementById("terms-box").style.display = 'block'
                                document.querySelector('#terms').classList.remove('hide-input');
                            }else if(data?.redirect_url !=''){
                                document.getElementById("tds_queries").reset();
                                window.location.href = `${data?.redirect_url}`;

                                // alert('Form create successfully');

                            }
                        }else{
                            alert('Something went wrong.')
                        }
                    }catch (error) {
                        console.error('Error fetching data:', error);
                        fetchButton.innerHTML = 'Retry';
                    } finally {
                        fetchButton.innerHTML = 'Submit';
                        fetchButton.disabled = false;
                    }
                }

                } catch (error) {
                    console.error('Error:', error);
                }

                let checkOutbtn = document.getElementById('checkOutbtn');

                checkOutbtn.onclick = async function(){
                    let checkIdinput =  document.querySelector('#call_id').value;
                    let form_type =  document.querySelector('#form_type').value;
                    let user_id =  document.querySelector('#user_id').value;
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

                        if(response.status == 200){
                            // Render the response for debugging
                            // document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);

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
        });

    </script>



<script>

    $(document).ready(() => {

// Initialize Select2
$('#multi-select').select2({
    placeholder: "Select options",
    closeOnSelect: false, // Prevent dropdown from closing on selection
    templateResult: formatOption, // Custom rendering for dropdown options
    templateSelection: formatSelection // Custom rendering for selected options
});

// Function to render checkboxes in options
function formatOption(option) {
    if (!option.id) {
    return option.text; // For the placeholder
    }
    const isChecked = option.selected ? "checked" : "";
    const $option = $(
    `<span>
        <input type="checkbox" class="option-checkbox" style="margin-right: 10px;" ${isChecked} />
        ${option.text}
    </span>`
    );
    return $option;
}

// Function to customize the selected option display
function formatSelection(option) {
    return option.text;
}

// Handle clicks on checkboxes or options
$(document).on('click', '.select2-results__option', function (e) {
    e.preventDefault(); // Prevent default Select2 behavior

    const $checkbox = $(this).find('input[type="checkbox"]');
    const isChecked = $checkbox.prop('checked');
    const value = $(this).data('select2Id');

    // Toggle checkbox state
    $checkbox.prop('checked', !isChecked);

    // Update the Select2 value
    const selectedValues = $('#multi-select').val() || [];
    if (!isChecked) {
        selectedValues.push(value);
    } else {
        const index = selectedValues.indexOf(value);
        if (index > -1) {
            selectedValues.splice(index, 1);
        }
    }
    $('#multi-select').val(selectedValues).trigger('change');
});

// Sync checkboxes with the default behavior
$('#multi-select').on('change', function () {
    $('.select2-results__option').each(function () {
    const $checkbox = $(this).find('input[type="checkbox"]');
    const value = $(this).data('data')?.id;
    if (value) {
        const isSelected = $('#multi-select').val().includes(value);
        $checkbox.prop('checked', isSelected);
    }
    });
});
});
</script>

</body>
</html>
