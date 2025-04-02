const addItem = document.getElementById('add-item');
const form = document.getElementById('quotation-form');
const cartBody = document.getElementById('cart-body');
let rowCounter = 1;
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
        updateSerialNumbers(); // Update serial numbers after removal
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
                    <input type="text" id="quotation_item_name_${rowCount + 1}" name="quotation_item_name[]" class="autocomplete-input qo-item-name" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,${rowCount + 1})"  onclick="checkCustomer()" >
                    <ul class="autocomplete-results" id="results-${rowCount + 1}"></ul>
                    <input type="hidden" id="item_id_${rowCount + 1}" name="item_id[]">
                </div>
                    </td>
                                <td>
                                    <input type="hidden" name="product_unit_of_measure[]" id="product_unit_of_measure${rowCount + 1}">
                                    <select id="quotation_unit_of_measure_${rowCount + 1}" name="quotation_unit_of_measure[]" required>
                                        <option value="" disabled selected>Select unit of measure</option>
                                        <option value="piece" >Piece</option>
                                        <option value="tonne" >Tonne</option>
                                        <option value="packets" >Packets</option>
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
                                <td><input type="number" id="quotation_rate_${rowCount + 1}" name="quotation_rate[]" oninput="calculateQuotation()"></td>
                                <td><input type="number" id="quotation_quantity_${rowCount + 1}" name="quotation_quantity[]" placeholder="Enter Quantity" oninput="calculateQuotation()"></td>
                                
                                <td><input type="number" id="quotation_amount_${rowCount + 1}" name="quotation_amount[]" readonly oninput="calculateQuotation()"></td>
                                <td>
                                    <div class="form-row">
                                        <div class="remove-item">
                                            <button type="button" class="circular-button remove-row">x</button>
                                        </div>
                                    </div>
                                </td>
                </tr>
            `;
    cartBody.insertAdjacentHTML('beforeend', newRow);
    rowCounter++;
}

// Function to update serial numbers in the first column of the table
function updateSerialNumbers() {
    const rows = document.querySelectorAll('#cart-body tr');
    rows.forEach((row, index) => {
        const currentIndex = index + 1; // To match 1-based indexing

        // Update the serial number in the first column
        row.querySelector('td:first-child').textContent = currentIndex;

        // Select input fields by class or general selectors, then update their IDs
        const itemNameField = row.querySelector(`[id^="quotation_item_name_"]`);
        if (itemNameField) {
            itemNameField.id = `quotation_item_name_${currentIndex}`;
            itemNameField.setAttribute('oninput', `searchProductNameOnInput(event, ${currentIndex})`);
        }

        const itemIdField = row.querySelector(`[id^="quotation_item_id_"]`);
        if (itemIdField) itemIdField.id = `quotation_item_id_${currentIndex}`;

        const searchResult = row.querySelector(`[id^="results-"]`);
        if (searchResult) searchResult.id = `results-${currentIndex}`;
        
        const productIdField = row.querySelector(`[id^="item_id_"]`);
        if (productIdField) productIdField.id = `item_id_${currentIndex}`;
        
        const productUnitField = row.querySelector(`[id^="product_unit_of_measure"]`);
        if (productUnitField) productUnitField.id = `product_unit_of_measure${currentIndex}`;
        
        const unitOfMeasureField = row.querySelector(`[id^="quotation_unit_of_measure_"]`);
        if (unitOfMeasureField) unitOfMeasureField.id = `quotation_unit_of_measure_${currentIndex}`;
        
        const rateField = row.querySelector(`[id^="quotation_rate_"]`);
        if (rateField) rateField.id = `quotation_rate_${currentIndex}`;
        
        const quantityField = row.querySelector(`[id^="quotation_quantity_"]`);
        if (quantityField) quantityField.id = `quotation_quantity_${currentIndex}`;
        
        const amountField = row.querySelector(`[id^="quotation_amount_"]`);
        if (amountField) amountField.id = `quotation_amount_${currentIndex}`;

        // Update button values and onClick events
        const acceptItemButton = row.querySelector('.accept-item');
        if (acceptItemButton) {
            acceptItemButton.value = currentIndex;
            acceptItemButton.setAttribute('onclick', `acceptItem(${currentIndex})`);
        }

        // Update other buttons or event listeners if necessary
    });

    calculateQuotation(); // Recalculate the total amount after the update
}



function searchVendorName(vendors) {
    const searchInput = document.getElementById(`vendor-name`);
    const resultsList = document.getElementById(`results`);
    const vendorId = document.getElementById(`vendor-id`);
    const searches = vendors;

    resultsList.innerHTML = '';

    searches.forEach(result => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="list-name">${result.vendor_company_name}</span> <span class="list-code">${result.vendor_code}</span>`;
        resultsList.appendChild(li);

        li.addEventListener('click', function () {
            searchInput.value = result.vendor_company_name;
            vendorId.value = result.vendor_id;
            resultsList.style.display = 'none';
            
            //remove class in searchInput
            searchInput.classList.remove('warning-message');
            searchInput.classList.add('success-message');
            //remove readonly with a querySelector which has many element with same class name
            document.querySelector('.qo-item-name').removeAttribute('readonly');


            
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}

function searchProductName(products, count) {
    const searchInput = document.getElementById(`quotation_item_name_${count}`);
    const resultsList = document.getElementById(`results-${count}`);
    const itemId = document.getElementById(`item_id_${count}`);
    const productUnitOfMeasure = document.getElementById(`product_unit_of_measure${count}`);
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
            resultsList.style.display = 'none';
            fetchPreSalesDetails(result.product_id, count);
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}


function searchCustomerName(customers) {
    const searchInput = document.getElementById("customer-name");
    const customerID = document.getElementById("customer-id");
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
            resultsList.style.display = 'none';

            //remove class in searchInput
            searchInput.classList.remove('warning-message');
            searchInput.classList.add('success-message');
            //remove readonly with a querySelector which has many element with same class name
            document.querySelector('.qo-item-name').removeAttribute('readonly');
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
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
        document.querySelectorAll('.autocomplete-input.qo-item-name').forEach(input => {
            input.removeAttribute('readonly');
        });
        
        return true;
    }
}

