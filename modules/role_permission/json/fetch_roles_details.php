<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $role_id = isset($_POST['role_id']) ? sanitizeInput($_POST['role_id'], 'int')  : 0;
    $inputParams = [
        ['type' => 'i', 'value' => $role_id]
    ];
    try {
        $response = callProcedure('fetch_role_details_and_pages', $inputParams);
        if ($response['status'] == 'success') {
            $role_details = $response['data'][0];
            $user_role_details = $response['data'][1];
            $pages_details = $response['data'][3];

            $result = [
                'status' => 'success',
                'role_details' => $role_details,
                'user_role_details' => $user_role_details,
                'pages_details' => $pages_details
            ];

            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching role details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching role details' . $e->getMessage()]);
    }
}
