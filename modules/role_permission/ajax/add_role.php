<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $role_name = isset($_POST['role-name']) ? sanitizeInput($_POST['role-name'], 'string')  : '';
    $role_code = isset($_POST['role-code']) ? sanitizeInput($_POST['role-code'], 'string')  : '';
    $pages = isset($_POST['page']) ? sanitizeInput($_POST['page'], 'int') : [];
    try {
        $page_ids = json_encode($pages);
        $procedure_params = [
            ['value' => $role_name, 'type' => 's'],
            ['value' => $role_code, 'type' => 's'],
            ['value' => $page_ids, 'type' => 's']
        ];

        $result = callProcedure('add_role', $procedure_params);
        if ($result['status'] == 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Role added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add role']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
