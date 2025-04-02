<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $result = callProcedure('dashboard_inventory_chart');
        if ($result['status'] == 'success') {
            echo json_encode(['status' => 'success', 'data' => $result['data']]);
        }
    } catch (\Throwable $th) {
        echo json_encode(['status' => 'error', 'message' => $th->getMessage()]);
    }
}
