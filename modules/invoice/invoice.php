<?php require_once('../../config/sparrow.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $currentLocation ?></title>

    <!-- Include CSS -->

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/style.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/cards.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/buttons.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/table.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/input fields.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/modal.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/badges.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/invoice/css/invoice.css' ?>">
    <link href="<?= PACKAGES . '/datatables/datatables.min.css' ?>" rel="stylesheet">
</head>

<body>
    <header>
        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>

    <div id="toast-container"></div>
    <main id="invoice">
        <!-- bread Crumb -->
        <section id="breadcrumb"></section>

    </main>
    <!-- modal -->
    <section id="invoice-modal"></section>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
            add_invoice();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
            view_invoice();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
            edit_invoice();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'cancel') { ?>
            cancel_invoice();
        <?php } else { ?>
            invoice_home()
        <?php } ?>
        // Load Top Nav Bar
        $.ajax({
            type: 'POST',
            url: '<?= GLOBAL_PATH . '/components/topnavbar.php' ?>',

            success: function(response) {
                $('#topNavBar').html(response);
                initializeTopbar();
            },
            error: function(xhr, status, error) {
                console.error('Error loading top navbar:', error);
            }
        });

        // Load Side Nav Bar
        $.ajax({
            type: 'POST',
            url: '<?= GLOBAL_PATH . '/components/sidenavbar.php' ?>',
            data: {
                getRewrittenUrl: '<?= $getRewrittenUrl ?>'
            },
            success: function(response) {
                $('#sideNavBar').html(response);

                // After loading sidenavbar.php content, initialize sidebar functionality
                initializeSidebar();
            },
            error: function(xhr, status, error) {
                console.error('Error loading side navbar:', error);
            }
        });

        // Load BreadCrumb
        $.ajax({
            type: 'POST',
            url: '<?= GLOBAL_PATH . '/components/breadcrumb.php' ?>',
            data: {
                getRewrittenUrl: '<?= $getRewrittenUrl ?>'
            },
            success: function(response) {
                $('#breadcrumb').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading side navbar:', error);
            }
        });

        function invoice_home() {
            //Load invoice Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/invoice/components/invoice_home.php?page_id=6&access=0' ?>',
                success: function(response) {
                    $('#invoice').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading invoice Home Page:', error);
                }
            });
        }

        function add_invoice() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/invoice/components/add_invoice.php?type=add&page_id=7&access=0' ?>',
                success: function(response) {
                    $('#invoice').html(response);
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add invoice order popup:', error);
                }
            });
        }

        function edit_invoice(invoice_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/invoice/components/edit_invoice.php?type=edit&page_id=8&access=0' ?>',
                data: {
                    invoice_id: invoice_id
                },
                success: function(response) {
                    $('#invoice').html(response);
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Edit invoice order popup:', error);
                }
            });
        }

        function cancel_invoice(invoice_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/invoice/components/confirm_invoice_cancel_popup.php?type=cancel&page_id=8&access=0' ?>',
                data: {
                    invoice_id: invoice_id
                },
                success: function(response) {
                    $('#invoice-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    var newUrl = window.location.pathname + '?type=cancel';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#invoice-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on cancel button click
                    $('#cancelInvoice').on('click', function() {
                        $('#invoice-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('confirmationPopup')) {
                            $('#invoice-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Cancel invoice order popup:', error);
                }
            });
        }

        function view_invoice(invoice_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/invoice/components/view_invoice.php?type=view&page_id=9&access=0' ?>',
                data: {
                    invoice_id: invoice_id
                },
                success: function(response) {
                    $('#invoice').html(response);
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading View invoice order popup:', error);
                }
            });
        }


        function fetch_product(product_id, count) {
            $.ajax({
                url: '<?= MODULES . '/invoice/json/fetch_product.php' ?>',
                type: 'POST',
                data: {
                    product_id: product_id
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        console.log(response)
                        $('#invoice_unit_of_measure-' + count).val(response.data[0].product_unit_of_measure);
                        $('#product_unit_of_measure_' + count).val(response.data[0].product_unit_of_measure);
                        $('#product-rate-' + count).val(response.data[0].unit_price);
                        $('#unit-price-' + count).val(response.data[0].unit_price);
                        $('#product-quantity-' + count).val("1");
                        if (response.data[0].inventory_quantity_in_stock != "" || response.data[0].inventory_quantity_in_stock != null) {
                            $('#quantity-in-stock-' + count).css('display', 'block');
                            $('#quantity-in-stock-' + count).text("In Stock : " + response.data[0].inventory_quantity_in_stock + "(" + response.data[0].inventory_unit_of_measure + ")");
                        } else {
                            $('#quantity-in-stock-' + count).css('display', 'none');
                        }
                        $('#tax-percentage-' + count).val(response.data[0].tax_percentage);
                        if (response.data[0].discountable == 1) {
                            $('#discount-checkboxs-' + count).css('display', 'block');
                            // $('#discount-fields-' + count).css('display', 'block');
                        } else {
                            $('#discount-checkboxs-' + count).css('display', 'none');
                            $('#discount-fields-' + count).css('display', 'none');
                        }
                        if (response.data[0].pricing_type == 0) {
                            $('#tax-inclusive-enable-' + count).prop('checked', true);
                            $('#tax_inclusive_enable_' + count).val(1);
                        } else {
                            $('#tax-inclusive-enable-' + count).prop('checked', false);
                            $('#tax_inclusive_enable_' + count).val(0);
                        }
                        calculateInvoice();
                    } else {
                        $('#response').text(response.message);
                    }
                },
                error: function() {
                    $('#response').text('An error occurred');
                }
            });
        }

        function calculateInvoice() {
            $.ajax({
                url: '<?= MODULES . '/invoice/ajax/calculate_invoice.php' ?>',
                type: 'POST',
                data: $('#invoice-form').serialize(),
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        // Populate summary fields
                        $("#nettotal").val(response.data.nettotal);
                        $("#sgst").val(response.data.sgst);
                        $("#cgst").val(response.data.cgst);
                        $("#igst").val(response.data.igst);
                        $("#total-value").val(response.data.total_value);
                        $("#total-gst-amount").val(response.data.total_gst_amount);
                        $("#grand-total").val(response.data.grand_total);

                        // Populate items
                        response.data.items.forEach((item, index) => {
                            const itemIndex = index + 1; // 1-based index for item rows
                            $(`#item-id-${itemIndex}`).val(item.item_id);
                            $(`#product-quantity-${itemIndex}`).val(item.quantity);
                            $(`#product-rate-${itemIndex}`).val(item.rate);
                            $(`#product-amount-${itemIndex}`).val(item.amount);
                            //$(`#discount-rate-${itemIndex}`).val(item.discount_percentage);
                            // $(`#discount-amount-${itemIndex}`).val(item.discount_amount);
                            $(`#item-gst-amount-${itemIndex}`).val(item.gst_amount);

                            if (item.price_comparison == 0) {
                                //#high style is block
                                $(`#high-${itemIndex}`).css('display', 'none');
                                $(`#low-${itemIndex}`).css('display', 'block');
                            } else {
                                //#high style is none
                                $(`#low-${itemIndex}`).css('display', 'none');
                                $(`#high-${itemIndex}`).css('display', 'block');
                            }
                        });
                        // Populate Amount In Words
                        $("#amount-inwords").val(response.data.amount_in_words + ' Only');


                    } else {
                        $('#response').text('Error: ' + response.message);
                    }
                },
                error: function() {
                    $('#response').text('An error occurred');
                }
            });
        }
    </script>
</body>

</html>