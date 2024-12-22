<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Registration</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    </style>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-lg-6 col-md-8 col-offset-2 col-sm-12 col-sm-offset-2">
                <!-- Default form -->
                <form id="business_registration" method="POST" name="scheduleform" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <h2>CONNECT US FOR BUSINESS REDISTRATION</h2>
                    </div>
                    <input type="hidden" id ="form_type" name="form_type" value="business_registration">
                    <input type="hidden" id ="user_id" name="user_id" value="<?= !empty($_GET['user_id'])?$_GET['user_id']:1; ?>">
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
                    <div class="mb-3 m-select-check">
                        <label for="multi-select" class="form-label  w-100">Choose Plan</label>
                        <select id="multi-select" class="form-control" name="plan" requiredInput  multiple="multiple">
                            <?php foreach ($businessRegistration as $key => $value): ?>
                                <option value="<?= $key; ?>"><?= $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document requirements for business registration</label>
                        <input type="file" class="form-control " id="document" name="document" requiredInput multiple="multiple">
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
            let call_id=0;
            form_type =''
            user_id = 0
            $('#business_registration').on('submit', async (e) => {
                e.preventDefault(); // Prevent the default form submit
                let formElement = document.querySelector('#business_registration'); 

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
                });
           
                const fileInput = document.getElementById("document");
                const files = fileInput.files;

                const formData = new FormData(formElement);
                for (let i = 0; i < files.length; i++) {
                    formData.append("files[]", files[i]); // Append each file to FormData
                }

                // Handle multi-select values
                const selectedValues = $('#multi-select').val() || [];
                formData.append("plan", selectedValues);
                if(isValid){

                    try {
                        // Send the POST request
                        const response = await fetch('http://127.0.0.1:8000/api/business-registration/store', {
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
                
                                                <h6>Plan List</h6>
                                                    <div class=" justify-content-between align-items-center border-top">
        
                                                    ${data?.getPlan.map(plan => `
                                                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                                        <div>
                                                            <h6>${plan.label}</h6>
                                                        </div>
                                                        <div class="fw-bold">₹${plan.value}</div>
                                                    </div>
                                                    `).join('')}
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


                    
                    } catch (error) {
                        // console.error('Error:', error);
                    }

                }
            });

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