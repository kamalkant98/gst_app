<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gst Queries</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
        #terms-box{
            display: none;
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

    </style>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-12">
                <!-- Default form -->
                <form id="gst_queries" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id ="form_type" name="form_type" value="gst_queries">
                    <input type="hidden" id ="user_id" name="user_id"  value="<?php echo isset($_GET['user_id']) && $_GET['user_id'] != '' ? $_GET['user_id'] : ''; ?>">
                    <div class="mb-3 text-center">
                        <h2>CONNECT US FOR GST QUERIES</h2>
                    </div>
                    <div class="mb-3 ">
                        <label for="type" class="form-label">GST No.</label>
                        <input type="text" class="form-control " id="gst_number" name="gst_number" requiredInput placeholder="GST No.">
                    </div>
                    <div class="mb-3">
                        <label for="multi-select" class="form-label">Type of Taxpayer</label>
                        <select id="type_of_taxpayer" class="form-control" requiredInput name="type_of_taxpayer">
                            <option value="">select taxpayer</option>
                            <option value="1">Regular</option>
                            <option value="2">Composition</option>
                        </select>
                    </div>

                    <div class="mb-3 hidden-box-2 composition">
                        <label for="type" class="form-label">Plan</label>
                        <select class="form-control composition" id="plan_name" name="plan_name" requiredInput>
                            <option  value="">select paln</option>
                            <option  value="1">Quarterly</option>
                            <option  value="2">Monthly</option>   
                        </select>
                    </div>

                    <div class="mb-3 hidden-box-2 regular">
                        <label for="type" class="form-label">Return filling frequency</label>
                        <select class="form-control regular" id="return_filling_frequency" name="return_filling_frequency" requiredInput>
                            <option  value="">select frequency</option>
                            <option  value="3">Annually</option>
                            <option  value="2">Monthly</option>   
                            <option  value="1">Quarterly</option>
                              
                        </select>
                    </div>
                    <!-- <div class="mb-3 hidden-box-2 regular m-select-check">
                        <label for="multi-select" class="form-label w-100">Type of return</label>
                        <select id="multi-select" class="form-control regular" name="plan" requiredInput  multiple="multiple">
                            <option  value="1">GSTR 1</option>
                            <option  value="2">GSTR 3B</option> 
                            <option  value="3">GSTR 9/9C</option>   
                            <option  value="4">GSTR 8</option>   
                            <option  value="5">TCS Return</option>
                        </select>
                    </div> -->
                    <div class="mb-3 hidden-box-2 regular">
                        <label for="type" class="form-label">Service type</label>
                        <select class="form-control regular" id="service_type" name="service_type" requiredInput>
                            <option  value="">select service type</option>
                            <option  value="1">Prepare only</option>
                            <option  value="2">File only</option>
                            <option  value="3">Both Prepare and file</option>   
                        </select>
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
<script>
        $(document).ready(function (e) {

            function isValidGST(gst) {
                // Regular expression to validate GST number format (15 characters: alphanumeric)
                const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/;
                return gstRegex.test(gst);
            }

            $('#type_of_taxpayer').on('change', function (event) {

                var selectedValue = $(this).val();
                if(selectedValue == 1){
                    $('.regular').show().removeClass('hide-input');
                    $('.composition').hide().addClass('hide-input');

                }else{
                    $('.regular').hide().addClass('hide-input');
                    $('.composition').show().removeClass('hide-input');


                }
            });

            let call_id=0;
            form_type =''
            user_id = 0
            const fetchButton = document.getElementById('submit_button');

            $('#gst_queries').on('submit', async (e) => {

                try {

                    e.preventDefault(); // Prevent the default form submit
                    let formElement = document.querySelector('#gst_queries'); 

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

                            input.after(errorElement);
                            input.classList.add('is-invalid');
                            isValid = false;

                        }

                        if (input.name == 'gst_number' && input.value != '') {
                            let checkGST = isValidGST(input.value);
                           
                            if (checkGST == false) {
                                let errorElement = document.createElement('span');
                                errorElement.className = 'error'; // Add error class for styling
                                errorElement.textContent = `Please enter a valid GST number.`;
                                input.after(errorElement);
                                input.classList.add('is-invalid');
                                isValid = false;
                            }
                        }

                    });
                    console.log(isValid,"======");
                    
                    if(isValid){
                    
                        const formData = new FormData(formElement);
                        // Handle multi-select values
                        const selectedValues = $('#multi-select').val() || [];
                        formData.append("plan", selectedValues);
                        fetchButton.disabled = true;
                        fetchButton.innerHTML = 'Loading <span class="loader"></span>';
                        try {
                            // Send the POST request
                            const response = await fetch('http://127.0.0.1:8000/api/get-queries/store', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                                },
                                body: formData, // Pass the FormData object directly
                            });

                            // Parse the JSON response
                            const data = await response.json();

                            if(response.status == 200){

                            
                                // Render the response for debugging
                                //  document.getElementById('response').innerHTML = JSON.stringify(data, null, 2);
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
                                                        <h6>GST QUERIES</h6>
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
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-3">
                                                        <h6>Coupon Code :: <strong>${data.coupon.code}</strong></h6>
                                                        <span class="fw-bold">₹${data.lessAmount}</span>
                                                    </div>
                                                ` : `${data?.coupon != null ?`
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-3">
                                                        <h6>Coupon Code :: <strong style="color">${data.coupon}</strong></h6>
                                                    </div>`:''}
                                                `}

                                                <div class="d-flex justify-content-between align-items-center mt-3 ">
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
                        }catch (error) {
                            console.error('Error fetching data:', error);
                            fetchButton.innerHTML = 'Retry';
                        }finally {
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

                    console.log(call_id,form_type,user_id);
                    
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