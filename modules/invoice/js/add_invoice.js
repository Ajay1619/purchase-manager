const addItem = document.getElementById('add-item');
const form = document.getElementById('invoice-form');
const cartBody = document.getElementById('cart-body');
const gstEnableCheckbox = document.getElementById('gst-enable');
const gstDetails = document.getElementById('gst-details');
// Event listener to handle GST checkbox change
gstEnableCheckbox.addEventListener('change', function () {
    if (gstEnableCheckbox.checked) {
        const gstInputFields = `
                <div class="form-group">
                    <label for="sgst">S GST </label>
                    <input type="number" id="sgst" name="sgst" placeholder="S GST Amount" oninput="calculateInvoice()" readonly>
                </div>
                <div class="form-group">
                    <label for="cgst">C GST </label>
                    <input type="number" id="cgst" name="cgst" placeholder="C GST Amount" oninput="calculateInvoice()" readonly>
                </div>
                <div class="form-group">
                    <label for="igst">I GST </label>
                    <input type="number" id="igst" name="igst" placeholder="I GST Amount" oninput="calculateInvoice()" readonly>
                </div>
        `;
        $('#gst-details').html(gstInputFields);
    } else {

        $('#gst-details').html("");
    }
});

function checkGstEnable(value){
    if (value!=0) {
        const gstInputFields = `
        <div class="form-group">
            <label for="sgst">S GST </label>
            <input type="number" id="sgst" name="sgst" placeholder="S GST Amount" oninput="calculateInvoice()" readonly>
        </div>
        <div class="form-group">
            <label for="cgst">C GST </label>
            <input type="number" id="cgst" name="cgst" placeholder="C GST Amount" oninput="calculateInvoice()" readonly>
        </div>
        <div class="form-group">
            <label for="igst">I GST </label>
            <input type="number" id="igst" name="igst" placeholder="I GST Amount" oninput="calculateInvoice()" readonly>
        </div>
        `;
        $('#gst-details').html(gstInputFields);
    } else {

        $('#gst-details').html("");
    }
}

// Event listener to add a new row
addItem.addEventListener('click', function (event) {
    const result=checkCustomer();
    if(result){
        addRow();
    }
});

// Event listener to handle row removal
form.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-row')) {
        event.preventDefault();
        event.target.closest('tr').remove();
        
        document.getElementById("total-item-count").value--;
        updateSerialNumbers(); // Update serial numbers after removal
        calculateInvoice(); // Recalculate invoice after removal
    }
});

// Function to add a new row to the table
function addRow() {
    const rowCount = cartBody.querySelectorAll('tr').length;
    const newRow = `
    <tr>
        <td>${rowCount + 1}</td>
        <td>
                <div class="autocomplete">
                    <input type="text" id="item-name-${rowCount + 1}" class="autocomplete-input" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,${rowCount + 1})"  name="item-name[]">
                    <ul class="autocomplete-results" id="results-${rowCount + 1}"></ul>
                    <input type="hidden" name="item-id[]" id="item-id-${rowCount + 1}">
                    <input type="hidden" name="item-gst-amount[]" id="item-gst-amount-${rowCount + 1}">
                </div>

            </td>
            <td>
                <input type="hidden" id="product_unit_of_measure${rowCount + 1}" name="product_unit_of_measure[]">
                <select id="invoice_unit_of_measure-${rowCount + 1}" name="invoice_unit_of_measure[]" required onchange="calculateInvoice()">
                    <option value="" disabled selected>Select unit of measure</option>
                    <option value="piece">Piece</option>
                    <option value="tonne">Tonne</option>
                    <option value="packets">Packets</option>
                    <option value="kg">Kilogram</option>
                    <option value="g">Gram</option>
                    <option value="lb">Pound</option>
                    <option value="oz">Ounce</option>
                    <option value="l">Liter</option>
                    <option value="ml">Milliliter</option>
                    <option value="m">Meter</option>
                    <option value="cm">Centimeter</option>
                    <option value="mm">Millimeter</option>
                    <option value="ft">Foot</option>
                    <option value="in">Inch</option>
                </select>
            </td>
            <td>
            <p class="quanity-in-stock" id="quantity-in-stock-${rowCount + 1}" style="display:none;"></p>
            <input type="number" id="product-quantity-${rowCount + 1}" name="invoice_quantity[]" value="0" placeholder="Enter Quantity" oninput="calculateInvoice()">
            </td>
            <td>
                <div class="form-group discount-checkboxs" id="discount-checkboxs-${rowCount + 1}" >
                    <label for="discount-enable-${rowCount + 1}">
                        <input type="checkbox" id="discount-enable-${rowCount + 1}" name="discount_enable[]" onclick="toggleDiscountFields(${rowCount + 1})" onchange="calculateInvoice()" value="1">
                        <span class="checkboxes"></span> Discounts
                        <input type="hidden" id="discount_enable_${rowCount + 1}" name="discount_enable[]" value="0">
                    </label>
                </div>
                <div id="discount-fields-${rowCount + 1}" class="discount-fields" style="display: none;">
                    <input type="number" id="discount-rate-${rowCount + 1}" name="discount_rate[]" placeholder="Discount Rate" oninput="calculateInvoice()">
                    <input type="number" id="discount-amount-${rowCount + 1}" name="discount_amount[]" placeholder="Discount Amount" oninput="calculateInvoice()">
                </div>
                <input type="number" id="product-rate-${rowCount + 1}" name="invoice_rate[]" value="0.00" onchange="calculateInvoice()" oninput="changeUnitPrice(this.value,${rowCount + 1})">
                <input type="hidden" name="unit-price[]" id="unit-price-${rowCount + 1}">
            </td>
            <td>
                <div class="form-group gst-checkboxs" id="gst-checkboxs-${rowCount + 1}">
                    <label for="tax-inclusive-enable">
                        <input type="checkbox" id="tax-inclusive-enable-${rowCount + 1}" onchange="taxEnable(${rowCount + 1})" name="tax-inclusive-enable[]">
                        <span class="checkboxes"></span> Tax inclusive
                        <input type="hidden" id="tax_inclusive_enable_${rowCount + 1}" name="tax_inclusive_enable[]" value="0.00">
                        <input type="hidden" name="tax_percentage[]" id="tax-percentage-${rowCount + 1}">
                        
                        
                    </label>
                </div>
                <input type="number" id="product-amount-${rowCount + 1}" name="invoice_amount[]" value="0.00" oninput="calculateInvoice()" readonly>

            </td>
            <td>
                <div class="form-row">
                    <div id="high-${rowCount + 1}" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#4CAF50">
                            <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z" />
                        </svg>
                    </div>
                    <div id="low-${rowCount + 1}" style="display: none;">

                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F44336">
                            <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z" />
                        </svg>
                    </div>
                    <div class="remove-item" >
                        <button type="button" class="circular-button remove-row">x</button>
                    </div>

                </div>
            </td>
                                  
    </tr>
`;
    cartBody.insertAdjacentHTML('beforeend', newRow);
    document.getElementById("total-item-count").value++;
}

// Function to update serial numbers in the first column of the table
function updateSerialNumbers() {
    const rows = document.querySelectorAll('#cart-body tr');
    rows.forEach((row, index) => {
        const currentIndex = index + 1; // To match 1-based indexing
        // Update the serial number in the first column
        row.querySelector('td:first-child').textContent = currentIndex;

        // Update input fields and their IDs, attributes, and event handlers
        const updateFieldIdAndEvent = (fieldSelector, idPrefix, eventName, eventArg) => {
            const field = row.querySelector(`[id^="${idPrefix}"]`);
            if (field) {
                field.id = `${idPrefix}${currentIndex}`;
                if (eventName && eventArg) {
                    field.setAttribute(eventName, `${eventArg}(${currentIndex})`);
                }
            }
        };

        updateFieldIdAndEvent(`[id^="item-name-"]`, "item-name-", "oninput", "searchProductNameOnInput");
        updateFieldIdAndEvent(`[id^="results-"]`, "results-", null, null);
        updateFieldIdAndEvent(`[id^="invoice-item-id-"]`, "invoice-item-id-", null, null);
        updateFieldIdAndEvent(`[id^="item-id-"]`, "item-id-", null, null);
        updateFieldIdAndEvent(`[id^="product_unit_of_measure"]`, "product_unit_of_measure", null, null);
        updateFieldIdAndEvent(`[id^="invoice_unit_of_measure-"]`, "invoice_unit_of_measure-", "onchange", "calculateInvoice");
        updateFieldIdAndEvent(`[id^="product-quantity-"]`, "product-quantity-", "oninput", "calculateInvoice");
        updateFieldIdAndEvent(`[id^="discount-enable-"]`, "discount-enable-", "onclick", "toggleDiscountFields");
        updateFieldIdAndEvent(`[id^="discount-rate-"]`, "discount-rate-", "oninput", "calculateInvoice");
        updateFieldIdAndEvent(`[id^="discount-amount-"]`, "discount-amount-", "oninput", "calculateInvoice");
        updateFieldIdAndEvent(`[id^="product-rate-"]`, "product-rate-", "oninput", "changeUnitPrice");
        updateFieldIdAndEvent(`[id^="unit-price-"]`, "unit-price-", null, null);
        updateFieldIdAndEvent(`[id^="tax-inclusive-enable-"]`, "tax-inclusive-enable-", "onchange", "taxEnable");
        updateFieldIdAndEvent(`[id^="product-amount-"]`, "product-amount-", "oninput", "calculateInvoice");

        // Update the buttons with dynamic handlers
        const removeButton = row.querySelector('.remove-item button');
        if (removeButton) {
            removeButton.setAttribute('onclick', `removeRow(${currentIndex})`);
        }

        // Update any additional buttons or elements if necessary
    });

    calculateInvoice(); // Recalculate after updating serial numbers
}


function searchProductName(products, count) {
    const searchInput = document.getElementById("item-name-" + count);
    const itemId = document.getElementById("item-id-" + count);
    const resultsList = document.getElementById("results-" + count);
    const productUnitOfMeasure = document.getElementById("product_unit_of_measure" + count);
    const searches = products;
    resultsList.innerHTML = '';

    searches.forEach(result => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="list-name">${result.product_name}</span> <span class="list-code">${result.product_code}</span>`;
        resultsList.appendChild(li);

        li.addEventListener('click', function () {
            searchInput.value = result.product_name;
            itemId.value = result.product_id;
            productUnitOfMeasure.value = result.unit_of_measure;
            fetch_product(result.product_id, count);
            resultsList.style.display = 'none';


        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}

function searchCustomerName(customers) {
    const searchInput = document.getElementById("customer-name");
    const customerID = document.getElementById("customer-id");
    const customerState = document.getElementById("customer-state");
    const resultsList = document.getElementById("results");
    const searches = customers;
    resultsList.innerHTML = '';

    searches.forEach(result => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="list-name">${result.customer_name}</span> <span class="list-code">${result.customer_code}</span>`;
        resultsList.appendChild(li);

        li.addEventListener('click', function () {
            searchInput.value = result.customer_name;
            customerID.value = result.customer_id;
            customerState.value = result.address_state;
            resultsList.style.display = 'none';

            //remove class in searchInput
            searchInput.classList.remove('warning-message');
            searchInput.classList.add('success-message');
            //remove readonly with a querySelector which has many element with same class name
            document.querySelector('.in-item-name').removeAttribute('readonly');
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}

function taxEnable(count){
    $('#gst_enable_'+count).val(1);
    calculateInvoice();
}

function checkCustomer() {
    if ($('#customer-name').val() == '') {
        //add class warning-message
        showToast('warning', 'Please Select The Customer!')
        $('#customer-name').addClass('warning-message');
        return false;
    } else {
        $('#customer-name').removeClass('warning-message');
        $('#customer-name').addClass('success-message');
        document.querySelectorAll('.autocomplete-input.in-item-name').forEach(input => {
            input.removeAttribute('readonly');
        });
        
        return true;
    }
}


// Close autocomplete results on click outside
// document.addEventListener('click', function (event) {
//     const resultsList = document.getElementById('results-1');
//     const searchInput = document.getElementById('item-name-1');
//     if (!searchInput.contains(event.target)) {
//         resultsList.style.display = 'none';
//     }
// });

