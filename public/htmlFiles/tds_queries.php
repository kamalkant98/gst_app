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

    </style>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-lg-6 col-md-8 col-offset-2 col-sm-12 col-sm-offset-2">
                <!-- Default form -->
                <form id="tds_queries" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id ="form_type" name="form_type" value="tds_queries">
                    <input type="hidden" id ="user_id" name="user_id" value="<?= !empty($_GET['user_id']?$_GET['user_id']:1); ?>">
                    <div class="mb-3 text-center">
                        <h2>CONNECT US FOR TDS/TCS QUERIES</h2>
                    </div>
                        <?php 
                       
                        
                        ?>

                    <div class="mb-3 ">
                        <label for="type" class="form-label">Tan No.</label>
                        <input type="text" class="form-control " id="tan_number" name="tan_number" requiredInput placeholder="TAN No.">

                        
                    </div>
                    <div class="mb-3 ">
                        <label for="type_of_return" class="form-label w-100">Type of return</label>
                        <select id="type_of_return" class="form-control" name="type_of_return" requiredInput>
                            <option  value="">select value</option>
                            <option  value="1">24Q</option>
                            <option  value="2">26Q</option> 
                            <option  value="3">27Q</option>   
                            <option  value="4">24QB</option>   
                        </select>
                    </div>

                    <div class="mb-3 first_select hidden-box-2">
                        <label for="no_of_employees" class="form-label w-100">No of employees</label>
                        <select id="no_of_employees" class="form-control first_select hide-input" name="no_of_employees" requiredInput>
                            <option  value="">select value</option>
                            <option  value="1">1 to 10</option>
                            <option  value="2">10 to 50</option> 
                            <option  value="3">50 to 100</option>   
                            <option  value="4">More than 100</option>   
                        </select>
                    </div>

                    <div class="mb-3 first_select hidden-box-2">
                        <label for="tax_planning" class="form-label w-100">Computation/Tax Planning of Employees</label>
                        <select id="tax_planning" class="form-control first_select hide-input" name="tax_planning" requiredInput>
                            <option  value="">select value</option>
                            <option  value="1">Yes</option>
                            <option  value="2">No</option>   
                        </select>
                    </div>


                    <div class="mb-3 secend_select hidden-box-2">
                        <label for="no_of_entries" class="form-label w-100">No of entries</label>
                        <select id="no_of_entries" class="form-control secend_select hide-input" name="no_of_entries" requiredInput>
                            <option  value="">select value</option>
                            <option  value="1">Up to 100</option>
                            <option  value="2">100 to 250</option> 
                            <option  value="3">250 to 500</option>   
                            <option  value="4">More than 500</option>   
                        </select>
                    </div>

                    <div class="mb-3 third_select hidden-box-2">
                        <label for="no_of_entries_27" class="form-label w-100">No of entries</label>
                        <select id="no_of_entries_27" class="form-control third_select hide-input" name="no_of_entries_27" requiredInput>
                            <option  value="">select value</option>
                            <option  value="1">Up to 50</option>
                            <option  value="2">50 to 100</option> 
                            <option  value="3">100 to 200</option>   
                            <option  value="4">More than 200</option>   
                        </select>
                    </div>
                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit</button>
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
<script>
        $(document).ready(function (e) {

            function isValidTAN(tan) {
                // Regular expression to validate TAN number format (4 letters, 5 digits, 1 letter)
                const tanRegex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
                return tanRegex.test(tan);
            }

            $('#type_of_return').on('change', function (event) {

                var selectedValue = $(this).val();
                if(selectedValue == 1){
                    $('.first_select').show().removeClass('hide-input');
                    $('.secend_select').hide().addClass('hide-input');
                    $('.third_select').hide().addClass('hide-input');
                }else if(selectedValue == 2){
                    $('.first_select').hide();
                    $('.secend_select').show().removeClass('hide-input');
                    $('.third_select').hide();

                }else if(selectedValue == 3){
                    $('.first_select').hide().addClass('hide-input');
                    $('.secend_select').hide().addClass('hide-input');
                    $('.third_select').show().removeClass('hide-input');

                }else{
                    $('.first_select').hide().addClass('hide-input');
                    $('.secend_select').hide().addClass('hide-input');
                    $('.third_select').hide().addClass('hide-input');
                }
            });

            let call_id=0;
            form_type =''
            user_id = 0

            $('#tds_queries').on('submit', async (e) => {

                try {

                    e.preventDefault(); // Prevent the default form submit
                    let formElement = document.querySelector('#tds_queries'); 

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
                    
                    const formData = new FormData(formElement);

                    if (isValid) {
                    // Send the POST request
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
                                                <h6>Schedule Call</h6>
                                                <p class="text-muted mb-1">Regarding ::${data?.regarding}</p>

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


                                        <!-- Total -->
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <h6>Total:</h6>
                                            <span class="fw-bold">₹${data?.amount}</span>
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

                        }
                    
                } catch (error) {
                    console.error('Error:', error);
                }

                let checkOutbtn = document.getElementById('checkOutbtn');

                checkOutbtn.onclick = async function(){
                    let checkIdinput =  document.querySelector('#call_id').value;
                    let form_type =  document.querySelector('#form_type').value;
                    let user_id =  document.querySelector('#user_id').value;

                    
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