<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $purchase_order_item_id = isset($_POST['purchase_order_item_id']) ? sanitizeInput($_POST['purchase_order_item_id'], 'int') : '';

    $purchase_order_procedure_params = [
        ['value' => $purchase_order_item_id, 'type' => 'i'],
    ];

    try {
        // Call stored procedure
        $result = callProcedure('accept_purchase_order_items', $purchase_order_procedure_params);
        if ($result['status'] === 'success') {
            $data = $result['data'][0];
            $product_id = $data['product_id'];
            $convertedQuantity = convertUnitQuantity($data['purchased_quantity'], $data['purchased_unit_of_measure'], $data['product_unit_measure']);
            $inventory_procedure_params = [
                ['value' => $product_id, 'type' => 'i'],
                ['value' => $convertedQuantity, 'type' => 'd'],
                ['value' => $data['purchased_quantity'], 'type' => 'd'],
                ['value' => $data['purchased_unit_of_measure'], 'type' => 's'],
                ['value' => 0, 'type' => 'i'],
                ['value' => $user_id, 'type' => 'i']

            ];
            $result = callProcedure('update_inventory', $inventory_procedure_params);
            //        print_r($result);
            // Assuming $result returns affected rows
            if ($result > 0) {
                echo json_encode(['status' => 'success', 'messages' => 'Purchase order item updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'messages' => 'No rows updated. Purchase order item may not exist or is already processed.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'messages' => 'Failed to update purchase order item.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
}
