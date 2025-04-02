<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $role_name = isset($_POST['role-name']) ? sanitizeInput($_POST['role-name'], 'string') : '';
    $role_code = isset($_POST['role-code']) ? sanitizeInput($_POST['role-code'], 'string') : '';
    $role_id = isset($_POST['role-id']) ? sanitizeInput($_POST['role-id'], 'int') : 0;
    $pages = isset($_POST['page']) ? sanitizeInput($_POST['page'], 'int') : [];

    try {
        $pages_ids = json_encode($pages);
        $procedure_params = [
            ['value' => $role_name, 'type' => 's'],
            ['value' => $role_code, 'type' => 's'],
            ['value' => $role_id, 'type' => 'i'],
            ['value' => $pages_ids, 'type' => 's']
        ];
        $result = callProcedure('edit_role', $procedure_params);
        if ($result['status'] == 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Role updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update role']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
