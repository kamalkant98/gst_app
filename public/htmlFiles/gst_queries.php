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

    </style>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-8 col-offset-2 col-sm-8 col-sm-offset-2">
                <!-- Default form -->
                <input type="hidden" id="steps" name="steps" value="1">
                <form id="gst_queries" method="POST" name="scheduleform" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <h2>Connect us for gst queries</h2>
                    </div>
                    <input type="hidden" name='user_inquiry_id' value='1'>
                        <?php 
                        $type = [
                            '1' =>  'Income tax returns',
                            '2' => 'TDS returns',
                            '3'  => 'GST returns',
                            '4' => 'Business registrations and
                            licenses',
                            '5'  => 'NRI taxation',
                            '6'  => 'Consultancy services',
                            '7'  =>  'Other Query',
                            
                            ];
                        $planList = [
                            '1'  => '10 min.',
                            '2'  => '20 min.',
                            '3'  => '30 min.',
                        ];
                        
                        ?>

                    <div class="mb-3 ">
                        <label for="type" class="form-label">GST No.</label>
                        <input type="text" class="form-control">
                        
                    </div>

                    <div class="mb-3 ">
                        <label for="type" class="form-label">Type of Taxpayer</label>
                        <select class="form-control hide-input" name='type_of_taxpayer' id = "type_of_taxpayer">
                            <option  value="">select value</option>
                            <option  value="1">regular</option>
                            <option  value="2">composition</option>   
                        </select>
                    </div>

                  

                    <div class="mb-3 hidden-box-2 regular">
                        <label for="type" class="form-label">Return filling frequency</label>
                        <select class="form-control hide-input" name="return_filling_frequency" >
                            <option  value="">select value</option>
                            <option  value="1">Button of Quarterly</option>
                            <option  value="2">Button of Monthly</option>   
                        </select>
                    </div>
                    <div class="mb-3 hidden-box-2 regular m-select-check">
                        <label for="multi-select" class="form-label w-100">Type of return</label>
                        <select id="multi-select" class="form-control hide-input" name="plan" requiredInput  multiple="multiple">
                        <option  value="1">GSTR 1</option>
                            <option  value="2">GSTR 3B</option> 
                            <option  value="3">GSTR 9/9C</option>   
                            <option  value="4">GSTR 8</option>   
                            <option  value="5">TCS Return</option>
                        </select>
                    </div>
                    <div class="mb-3 hidden-box-2 regular">
                        <label for="type" class="form-label">Service type</label>
                        <select class="form-control hide-input" id="service-type" name="service_type">
                            <option  value="">select value</option>
                            <option  value="1">Prepare only</option>
                            <option  value="2">Both Prepare and file</option>   
                        </select>
                    </div>
                    

                   
                    <div>
                        <button type="submit" id="submit_button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                  



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
        $(document).ready(function (e) {

            $('#type_of_taxpayer').on('change', function (event) {

                var selectedValue = $(this).val();
                if(selectedValue == 1){
                    $('.regular').show();

                }else{
                    $('.regular').hide();


                }
                
                
              
            });

            $('#gst_queries').on('submit', async (e) => {
            e.preventDefault(); // Prevent the default form submit
            let formElement = document.querySelector('#gst_queries'); 
            const formData = new FormData(formElement);
           

            // Handle multi-select values
            const selectedValues = $('#multi-select').val() || [];
            formData.append("plan", selectedValues);

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
                if (data && data.data > 0) {
                    // Handle the success case
                    console.log("Success:", data);
                }
            } catch (error) {
                console.error('Error:', error);
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