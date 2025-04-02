<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Products</h3>
                <p class="statistic" id="total-out-of-stock-products-count">100</p>
            </div>
            <svg class="card-icon" width="50px" height="50px" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="icon" fill="#4CAF50" transform="translate(64.000000, 34.346667)">
                        <path d="M192,7.10542736e-15 L384,110.851252 L384,332.553755 L192,443.405007 L1.42108547e-14,332.553755 L1.42108547e-14,110.851252 L192,7.10542736e-15 Z M127.999,206.918 L128,357.189 L170.666667,381.824 L170.666667,231.552 L127.999,206.918 Z M42.6666667,157.653333 L42.6666667,307.920144 L85.333,332.555 L85.333,182.286 L42.6666667,157.653333 Z M275.991,97.759 L150.413,170.595 L192,194.605531 L317.866667,121.936377 L275.991,97.759 Z M192,49.267223 L66.1333333,121.936377 L107.795,145.989 L233.374,73.154 L192,49.267223 Z" id="Combined-Shape"></path>
                    </g>
                </g>
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Cancelled Products</h3>
                <p class="statistic" id="total-out-of-stock-canceled-products-count">99</p>
            </div>
            <svg class="card-icon" width="50px" height="50px" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="icon" fill="#FF5733" transform="translate(64.000000, 34.346667)">
                        <path d="M192,7.10542736e-15 L384,110.851252 L384,332.553755 L192,443.405007 L1.42108547e-14,332.553755 L1.42108547e-14,110.851252 L192,7.10542736e-15 Z M127.999,206.918 L128,357.189 L170.666667,381.824 L170.666667,231.552 L127.999,206.918 Z M42.6666667,157.653333 L42.6666667,307.920144 L85.333,332.555 L85.333,182.286 L42.6666667,157.653333 Z M275.991,97.759 L150.413,170.595 L192,194.605531 L317.866667,121.936377 L275.991,97.759 Z M192,49.267223 L66.1333333,121.936377 L107.795,145.989 L233.374,73.154 L192,49.267223 Z" id="Combined-Shape"></path>
                    </g>
                </g>
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/out_of_stock/ajax/fetch_out_of_stock_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const CanceledOutOfStockCount = response.CanceledOutOfStockCount;
                    const OutOfStockCount = response.OutOfStockCount;
                    // Populate modal with fetched vendor details
                    $('#total-out-of-stock-products-count').text(OutOfStockCount);
                    $('#total-out-of-stock-canceled-products-count').text(CanceledOutOfStockCount);

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