<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $year = isset($_POST['year']) ? sanitizeInput($_POST['year'], 'string') : date('Y');
    $type = isset($_POST['type']) ? sanitizeInput($_POST['type'], 'int') : 0;

    $procedure_params = [
        ['value' => $year, 'type' => 's'],
        ['value' => $type, 'type' => 'i']
    ];

    try {
        $result = callProcedure('fetch_transactions_table', $procedure_params);
        if ($result && $result['status'] == 'success') {
            $table_data = $result['data'];

            // Initialize variables
            $html = '';
            $current_month = '';
            $month_rows = [];
            $total_subtotal = $total_gst = $total_grand_total = 0;

            // First pass: Count rows for each month
            foreach ($table_data as $row) {
                $month_name = $row['month_name'];

                if (!isset($month_rows[$month_name])) {
                    $month_rows[$month_name] = 0;
                }
                $month_rows[$month_name]++;
            }

            // Second pass: Generate HTML
            $current_month = '';
            foreach ($table_data as $row) {
                $month_name = $row['month_name'];

                if ($current_month != $month_name) {
                    // Add total row for the previous month
                    if ($current_month != '') {
                        $html .= "<tr class='footer'>
                                      <td colspan='3'></td>
                                      <td>" . formatNumberIndian($total_subtotal) . "</td>
                                      <td>" . formatNumberIndian($total_gst) . "</td>
                                      <td>" . formatNumberIndian($total_grand_total) . "</td>
                                  </tr>";
                        // Reset totals
                        $total_subtotal = $total_gst = $total_grand_total = 0;
                    }
                    $current_month = $month_name;
                    $html .= "<tr>
                                  <td rowspan='{$month_rows[$month_name]}' class='highlighted'>{$current_month}</td>";
                } else {
                    $html .= "<tr>";
                }

                $html .= "<td>{$row['date']}</td>
                          <td>{$row['invoice_number']}</td>
                          <td>" . formatNumberIndian($row['subtotal']) . "</td>
                          <td>" . formatNumberIndian($row['gst_amount']) . "</td>
                          <td>" . formatNumberIndian($row['grand_total']) . "</td>
                      </tr>";

                // Accumulate totals
                $total_subtotal += $row['subtotal'];
                $total_gst += $row['gst_amount'];
                $total_grand_total += $row['grand_total'];
            }

            // Add the last month's total row
            if ($current_month != '') {
                $html .= "<tr class='footer'>
                              <td colspan='3'></td>
                              <td>" . formatNumberIndian($total_subtotal) . "</td>
                              <td>" . formatNumberIndian($total_gst) . "</td>
                              <td>" . formatNumberIndian($total_grand_total) . "</td>
                          </tr>";
            }

            echo json_encode(['status' => 'success', 'data' => $html]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch transaction details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching transaction details: ' . $e->getMessage()]);
    }
}
