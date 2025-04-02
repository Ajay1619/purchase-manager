<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inventory Products</h3>
                <p class="statistic" id="total-inventory-products-count">100</p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="50" height="50" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 18062 18062" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <style type="text/css">
                        .fil0 {
                            fill: black
                        }
                    </style>
                </defs>
                <g id="Layer_x0020_1">
                    <metadata id="CorelCorpID_0Corel-Layer" />
                    <g id="_574481040">
                        <polygon class="fil0" points="4657,13290 4657,18036 8749,15674 8749,10930 " />
                        <polygon class="fil0" points="9329,10915 9329,15683 13406,18036 13406,13272 " />
                        <path class="fil0" d="M13970 13290l0 4746 4092 -2362 0 -4744 -1315 752 14 1891c0,203 -195,265 -345,345l-862 502c-177,118 -424,-7 -424,-220l0 -1584 -1160 674z" />
                        <polygon class="fil0" points="9619,10448 13703,12805 14895,12116 10965,9666 " />
                        <polygon class="fil0" points="11496,9358 15410,11800 15914,11504 12004,9063 " />
                        <polygon class="fil0" points="12562,8739 16498,11192 17778,10461 13688,8086 " />
                        <polygon class="fil0" points="16196,11996 15679,12297 15679,13714 16196,13416 " />
                        <path class="fil0" d="M4092 18036l0 -4764 -1195 -695 -12 1608c0,116 -114,282 -235,282 -47,0 -94,-16 -141,-31l-1051 -612c-94,-47 -141,-141 -141,-235l0 -1926 -1317 -758 0 4769 4092 2362z" />
                        <polygon class="fil0" points="2383,13730 2383,12290 1866,11990 1866,13432 " />
                        <polygon class="fil0" points="3189,12112 4390,12805 8465,10461 7105,9671 " />
                        <polygon class="fil0" points="2650,11801 6565,9359 6068,9073 2155,11515 " />
                        <polygon class="fil0" points="1608,11200 5524,8760 4364,8092 305,10448 " />
                        <polygon class="fil0" points="5012,7813 8733,9956 8733,5197 6554,3931 4641,2838 4641,7600 " />
                        <path class="fil0" d="M9298 5189l0 4772 4092 -2361 0 -4762 -1333 761 0 1915c0,196 -186,261 -329,345l-845 483c-192,110 -425,3 -425,-216l0 -1607 -1160 670z" />
                        <polygon class="fil0" points="6836,3460 9031,4730 10223,4041 6298,1595 4941,2378 " />
                        <polygon class="fil0" points="6831,1287 10740,3727 11242,3445 7326,1001 " />
                        <polygon class="fil0" points="7878,683 11800,3128 13092,2379 9015,27 " />
                        <polygon class="fil0" points="11007,4213 11007,5655 11524,5357 11524,3907 " />
                    </g>
                </g>
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Items in Stock</h3>
                <p class="statistic" id="total-in-stock-products-count">99</p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="50" height="50" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 18062 18062" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <style type="text/css">
                        .fil01 {
                            fill: #4CAF50
                        }
                    </style>
                </defs>
                <g id="Layer_x0020_1">
                    <metadata id="CorelCorpID_0Corel-Layer" />
                    <g id="_574481040">
                        <polygon class="fil01" points="4657,13290 4657,18036 8749,15674 8749,10930 " />
                        <polygon class="fil01" points="9329,10915 9329,15683 13406,18036 13406,13272 " />
                        <path class="fil01" d="M13970 13290l0 4746 4092 -2362 0 -4744 -1315 752 14 1891c0,203 -195,265 -345,345l-862 502c-177,118 -424,-7 -424,-220l0 -1584 -1160 674z" />
                        <polygon class="fil01" points="9619,10448 13703,12805 14895,12116 10965,9666 " />
                        <polygon class="fil01" points="11496,9358 15410,11800 15914,11504 12004,9063 " />
                        <polygon class="fil01" points="12562,8739 16498,11192 17778,10461 13688,8086 " />
                        <polygon class="fil01" points="16196,11996 15679,12297 15679,13714 16196,13416 " />
                        <path class="fil01" d="M4092 18036l0 -4764 -1195 -695 -12 1608c0,116 -114,282 -235,282 -47,0 -94,-16 -141,-31l-1051 -612c-94,-47 -141,-141 -141,-235l0 -1926 -1317 -758 0 4769 4092 2362z" />
                        <polygon class="fil01" points="2383,13730 2383,12290 1866,11990 1866,13432 " />
                        <polygon class="fil01" points="3189,12112 4390,12805 8465,10461 7105,9671 " />
                        <polygon class="fil01" points="2650,11801 6565,9359 6068,9073 2155,11515 " />
                        <polygon class="fil01" points="1608,11200 5524,8760 4364,8092 305,10448 " />
                        <polygon class="fil01" points="5012,7813 8733,9956 8733,5197 6554,3931 4641,2838 4641,7600 " />
                        <path class="fil01" d="M9298 5189l0 4772 4092 -2361 0 -4762 -1333 761 0 1915c0,196 -186,261 -329,345l-845 483c-192,110 -425,3 -425,-216l0 -1607 -1160 670z" />
                        <polygon class="fil01" points="6836,3460 9031,4730 10223,4041 6298,1595 4941,2378 " />
                        <polygon class="fil01" points="6831,1287 10740,3727 11242,3445 7326,1001 " />
                        <polygon class="fil01" points="7878,683 11800,3128 13092,2379 9015,27 " />
                        <polygon class="fil01" points="11007,4213 11007,5655 11524,5357 11524,3907 " />
                    </g>
                </g>
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inventory Value</h3>
                <p class="statistic" id="inventory-value">1</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#304463">
                <path d="M549-120 280-400v-80h140q53 0 91.5-34.5T558-600H240v-80h306q-17-35-50.5-57.5T420-760H240v-80h480v80H590q14 17 25 37t17 43h88v80h-81q-8 85-70 142.5T420-400h-29l269 280H549Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/inventory/ajax/fetch_inventory_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_inventory_products_count = response.total_inventory_products_count;
                    const total_in_stock_products_count = response.total_in_stock_products_count;
                    const inventory_value = response.inventory_value;
                    // Populate modal with fetched vendor details
                    $('#total-inventory-products-count').text(total_inventory_products_count);
                    $('#total-in-stock-products-count').text(total_in_stock_products_count);
                    $('#inventory-value').text(inventory_value);

                } else {
                    alert('Failed to fetch vendor details: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching vendor details: ' + error);
            }
        });
    </script>
<?php } ?>