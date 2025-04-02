let itemCount = 1;

function add_item() {
    itemCount++;
    const itemDescriptionsContainer = document.getElementById('itemDescriptions');
    const newItemDescriptions = document.createElement('div');
    newItemDescriptions.classList.add('description');
    newItemDescriptions.innerHTML = `
                <h4>Item #${itemCount}</h4>
                <div class="description">
                    <div class="description-items">
                        <div class="autocomplete">
                            <input type="text" id="dpname_${itemCount}" name="dpname[]" class="autocomplete-input" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,${itemCount})">
                            <ul class="autocomplete-results" id="results-${itemCount}"></ul>
                            <input type="hidden" id="item_id_${itemCount}" name="item_id[]" value="">
                        </div>
                    </div>
                    <div class="description-items">
                        <input type="text" id="dpcode_${itemCount}" name="dpcode[]" placeholder="Product Code">
                    </div>
                    <div class="description-items">
                        <select id="dpuom_${itemCount}" name="dpuom[]" required>
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
                    </div>
                    <div class="description-items">
                        <input type="text" id="dpq_${itemCount}" name="dpq[]" placeholder="Quantity">
                        <button type="button" class="circular-button remove-item">x</button>
                    </div>
                </div>`;

    newItemDescriptions.querySelector('.remove-item').addEventListener('click', function () {
        newItemDescriptions.remove();
        updateItemNumbers();
    });

    itemDescriptionsContainer.appendChild(newItemDescriptions);

    updateItemNumbers();
}

function add_item_update(item_length) {
   const itemCount = Number(item_length) + 1;
    const itemDescriptionsContainer = document.getElementById('itemDescriptions');
    const newItemDescriptions = document.createElement('div');
    newItemDescriptions.classList.add('description');
    newItemDescriptions.innerHTML = `
                <h4>Item #${itemCount}</h4>
                <div class="description">
                    <div class="description-items">
                        <div class="autocomplete">
                            <input type="text" id="dpname_${itemCount}" name="dpname[]" class="autocomplete-input" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,${itemCount})">
                            <ul class="autocomplete-results" id="results-${itemCount}"></ul>
                            <input type="hidden" id="itemid_${itemCount}" name="itemid[]">
                            <input type="hidden" id="usedproductid_${itemCount}" name="usedproductid[]" >
                        </div>
                    </div>
                    <div class="description-items">
                        <input type="text" id="dpcode_${itemCount}" name="dpcode[]" placeholder="Product Code" readonly>
                    </div>
                    <div class="description-items">
                        <select id="dpuom_${itemCount}" name="dpuom[]" required>
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
                    </div>
                    <div class="description-items">
                        <input type="text" id="dpq_${itemCount}" name="dpq[]" placeholder="Quantity">
                        <button type="button" class="circular-button remove-item">x</button>
                    </div>
                </div>`;

    newItemDescriptions.querySelector('.remove-item').addEventListener('click', function () {
        newItemDescriptions.remove();
        updateItemNumbers();
    });

    itemDescriptionsContainer.appendChild(newItemDescriptions);

    updateItemNumbers();
    document.getElementById('item_length').value = itemCount;
}

function updateItemNumbers() {
    const descriptions = document.querySelectorAll('#itemDescriptions > .itemDescriptions');
    descriptions.forEach((description, index) => {
        const itemNumber = index + 1;
        description.querySelector('h4').textContent = `Item #${itemNumber}`;

        const inputs = description.querySelectorAll('.description-items input');
        inputs.forEach((input) => {
            if (input.id.startsWith('dpname')) {
                input.id = `dpname_${itemNumber}`;
                input.setAttribute('oninput', `searchProductNameOnInput(event, ${itemNumber})`);
            } else if (input.id.startsWith('item_id')) {
                input.id = `item_id_${itemNumber}`;
            } else if (input.id.startsWith('dpcode')) {
                input.id = `dpcode_${itemNumber}`;
            } else if (input.id.startsWith('dpuom')) {
                input.id = `dpuom_${itemNumber}`;
            } else if (input.id.startsWith('dpq')) {
                input.id = `dpq_${itemNumber}`;
            }

            if (input.id.startsWith('results')) {
                input.id = `results-${itemNumber}`;
            }
        });
    });
}

function searchProductName(products, count) {
    const searchInput = document.getElementById(`dpname_${count}`);
    const resultsList = document.getElementById(`results-${count}`);
    const itemId = document.getElementById(`item_id_${count}`);
    const dpcode = document.getElementById(`dpcode_${count}`);
    const searches = products;

    resultsList.innerHTML = '';

    searches.forEach(result => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="list-name">${result.product_name}</span> <span class="list-code">${result.product_code}</span>`;
        resultsList.appendChild(li);

        li.addEventListener('click', function () {
            searchInput.value = result.product_name;
            itemId.value = result.product_id;
            dpcode.value = result.product_code;
            resultsList.style.display = 'none';
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}

function searchProductNameUpdate(products, count) {
    const searchInput = document.getElementById(`dpname_${count}`);
    const resultsList = document.getElementById(`results-${count}`);
    const itemId = document.getElementById(`usedproductid_${count}`);
    const dpcode = document.getElementById(`dpcode_${count}`);
    const searches = products;

    resultsList.innerHTML = '';

    searches.forEach(result => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="list-name">${result.product_name}</span> <span class="list-code">${result.product_code}</span>`;
        resultsList.appendChild(li);
        li.addEventListener('click', function () {
            searchInput.value = result.product_name;
            itemId.value = result.product_id;
            dpcode.value = result.product_code;
            resultsList.style.display = 'none';
        });
    });

    resultsList.style.display = searches.length > 0 ? 'block' : 'none';
}