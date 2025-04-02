<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    try {
        $result = callProcedure('dashboard_top_section');
        if ($result['status'] == 'success') {
            echo json_encode(array('status' => 'success', 'data' => $result['data'], 'message' => 'Data fetched successfully'));
        }
    } catch (\Throwable $th) {
        echo json_encode(array('status' => 'error', 'message' => $th->getMessage()));
    }
}
