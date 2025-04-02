<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div id="invoice-card" class="dashboard-card funky-card">
        <h3>Recent Invoices</h3>
        <div class="card-content" id="invoice-list">
            <!-- Invoice items will be populated here dynamically -->
        </div>
    </div>
    <div id="purchase-card" class="dashboard-card funky-card">
        <h3>Recent Purchase Orders</h3>
        <div class="card-content" id="purchase-list">
            <!-- Purchase order items will be populated here dynamically -->
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/dashboard/json/invoice_purchase_list.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    const data = response.data;

                    // Populating Invoices
                    const invoices = data.slice(0, 3); // first 3 are invoices
                    const invoiceList = document.getElementById('invoice-list');
                    invoices.forEach((invoice) => {
                        const invoiceStatus = invoice.invoice_status === 0 ? 'Pending' : (invoice.invoice_status === 1 ? 'Paid' : 'Canceled');
                        const invoiceItem = `
                            <div class="invoice-item">
                                <div class="invoice-info">
                                    <span class="invoice-number">#${invoice.invoice_number}</span>
                                    <span class="invoice-date">${new Date(invoice.invoice_date).toLocaleDateString()}</span>
                                </div>
                                <span class="invoice-amount"><?= CURRENCY_SYMBOL ?>${invoice.grand_total}</span>
                                <span class="badge ${invoiceStatus.toLowerCase()}">${invoiceStatus}</span>
                            </div>
                        `;
                        invoiceList.innerHTML += invoiceItem;
                    });

                    // Populating Purchase Orders
                    const purchaseOrders = data[3]; // 4th element is the array of purchase orders
                    const purchaseList = document.getElementById('purchase-list');
                    purchaseOrders.forEach((purchase) => {
                        const purchaseStatus = purchase.purchase_order_status === 0 ? 'Pending' : (purchase.purchase_order_status === 1 ? 'Purchased' : 'Canceled');
                        const purchaseItem = `
                            <div class="purchase-item">
                                <div class="purchase-info">
                                    <span class="purchase-number">#${purchase.purchase_order_number}</span>
                                    <span class="purchase-date">${new Date(purchase.purchase_order_date).toLocaleDateString()}</span>
                                </div>
                                <span class="purchase-amount"><?= CURRENCY_SYMBOL ?>${purchase.grand_total}</span>
                                <span class="badge ${purchaseStatus.toLowerCase()}">${purchaseStatus}</span>
                            </div>
                        `;
                        purchaseList.innerHTML += purchaseItem;
                    });
                } else {
                    showToast(response.status, response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching purchase history: ' + error);
            }
        });
    </script>
<?php } ?>