-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 03:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aquilate_trex`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `accept_purchase_order_items` (IN `p_purchase_order_item_id` INT)   BEGIN
    DECLARE v_purchased_unit_of_measure VARCHAR(50);
    DECLARE v_purchased_quantity INT;
    DECLARE v_product_id INT;
    DECLARE v_product_unit_measure VARCHAR(50);
    DECLARE v_product_name VARCHAR(255);

    -- Update the status to '1' (purchased) for the given purchase_order_item_id
    UPDATE inv_purchase_order_items
    SET purchase_order_item_status = 1
    WHERE purchase_order_item_id = p_purchase_order_item_id
    AND deleted = 0; -- Ensure the item is not marked as deleted

    -- Check if the row was updated
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Purchase order item not found or already processed.';
    END IF;

    -- Fetch the purchased unit_of_measure and quantity from inv_purchase_order_items
    SELECT unit_of_measure, quantity
    INTO v_purchased_unit_of_measure, v_purchased_quantity
    FROM inv_purchase_order_items
    WHERE purchase_order_item_id = p_purchase_order_item_id;

    -- Fetch the unit_of_measure from inv_products based on product_id
    SELECT p.unit_of_measure, p.product_name, p.product_id
    INTO v_product_unit_measure, v_product_name,v_product_id
    FROM inv_products p
    JOIN inv_purchase_order_items poi ON poi.product_id = p.product_id
    WHERE poi.purchase_order_item_id = p_purchase_order_item_id;

    -- Return the results
    SELECT v_purchased_unit_of_measure AS purchased_unit_of_measure, 
           v_purchased_quantity AS purchased_quantity,
           v_product_unit_measure AS product_unit_measure,
           v_product_name AS product_name,
           v_product_id AS product_id;
           END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_role` (IN `p_role_name` VARCHAR(50), IN `p_role_code` VARCHAR(20), IN `p_page_ids` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE page_count INT;
    DECLARE current_page_id INT;

    -- Start transaction
    START TRANSACTION;

    -- Insert into `inv_role_permissions`
    INSERT INTO inv_role_permissions (role_name, role_code)
    VALUES (p_role_name, p_role_code);

    -- Get the new role ID
    SET @new_role_id = LAST_INSERT_ID();

    -- Count the number of page IDs
    SET page_count = JSON_LENGTH(p_page_ids);

    -- Loop through each page ID in the JSON array
    WHILE i < page_count DO
        -- Extract each page ID from the JSON array
        SET current_page_id = JSON_UNQUOTE(JSON_EXTRACT(p_page_ids, CONCAT('$[', i, ']')));

        -- Insert into `inv_role_pages`
        INSERT INTO inv_role_pages (role_id, page_id)
        VALUES (@new_role_id, current_page_id);

        -- Increment index
        SET i = i + 1;
    END WHILE;

    -- Commit transaction
    COMMIT;

    -- Return the new role ID
    SELECT @new_role_id AS role_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_invoice` (IN `p_invoice_id` INT)   BEGIN
    -- Cancel the invoice in the inv_invoices table
    UPDATE inv_invoices
    SET invoice_status = 2,  -- Set status to 'Canceled'
        updated_on = CURRENT_TIMESTAMP()
    WHERE invoice_id = p_invoice_id;

    -- Mark all associated invoice items as 'Canceled'
    UPDATE inv_invoice_items
    SET invoice_items_status = 2 -- Set status to 'Canceled'
    WHERE invoice_id = p_invoice_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_purchase_order` (IN `p_purchase_order_id` INT)   BEGIN
    -- Update the 'deleted' flag in the inv_purchase_orders table
    UPDATE inv_purchase_orders
    SET purchase_order_status=2
    WHERE purchase_order_id = p_purchase_order_id;

    -- Update the 'deleted' flag in the inv_purchase_order_items table
    UPDATE inv_purchase_order_items
    SET deleted = 1
    WHERE purchase_order_id = p_purchase_order_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_quotation` (IN `p_quotation_id` INT)   BEGIN
    -- Cancel the quotation in the inv_quotations table
    UPDATE inv_quotations
    SET quotation_status = 2,  -- Set status to 'Canceled'
        updated_on = CURRENT_TIMESTAMP()
    WHERE quotation_id = p_quotation_id;

    -- Mark all associated quotation items as 'Canceled'
    UPDATE inv_quotation_items
    SET quotation_item_status = 2 -- Set status to 'Canceled'
    WHERE quotation_id = p_quotation_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_access` (IN `p_role_id` INT, IN `p_page_id` INT)   BEGIN
    DECLARE access_result INT;

    -- Check if the role_id has access to the page_id
    SELECT COUNT(*) INTO access_result
    FROM inv_role_pages
    WHERE role_id = p_role_id AND page_id = p_page_id;

    -- If a record is found, set output to 1; otherwise, set to 0
    IF access_result > 0 THEN
        SET access_result = 1;
    ELSE
        SET access_result = 0;
    END IF;
    
    -- Return the result
    SELECT access_result AS result;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_login_status` (IN `p_login_id` INT, IN `p_user_id` INT)   BEGIN
    DECLARE v_login_status INT;
    DECLARE login_result INT;

    -- Check if the provided login_id matches the one in inv_login_logs for the user_id
    SELECT login_status INTO v_login_status
    FROM inv_login_logs l
    WHERE l.login_log_id = p_login_id AND employee_id=p_user_id;

    -- If a record is found and login_status is 1, set output to 1; otherwise, set to 0
    IF v_login_status = 1 THEN
        SET login_result = 1;
    ELSE
        SET login_result = 0;
    END IF;
    
    -- Return the result
    SELECT login_result AS result;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_inventory_chart` ()   BEGIN
    DECLARE total_inventory DECIMAL(15,3);
    DECLARE total_in_stock DECIMAL(15,3);
    DECLARE total_out_of_stock DECIMAL(15,3);
    DECLARE total_products INT;
    DECLARE total_stock_products INT;
    DECLARE total_non_stock_products INT;
    
    -- Get the total quantity in inventory
    SELECT SUM(quantity_in_stock) INTO total_inventory FROM inv_inventory;
    
    -- Get the total in-stock and out-of-stock quantities
    SELECT SUM(quantity_in_stock) INTO total_in_stock FROM inv_inventory WHERE inventory_status = 1;
    SELECT SUM(quantity_in_stock) INTO total_out_of_stock FROM inv_inventory WHERE inventory_status = 0;
    
    -- Get the total number of products
    SELECT COUNT(*) INTO total_products FROM inv_products;
    
    -- Get the total number of stock products and non-stock products
    SELECT COUNT(*) INTO total_stock_products FROM inv_products WHERE product_type = 0;
    SELECT COUNT(*) INTO total_non_stock_products FROM inv_products WHERE product_type = 1;

    -- Calculate percentages
    SELECT
        ROUND((IFNULL(total_in_stock, 0) / total_inventory) * 100, 2) AS in_stock_percentage,
        ROUND((IFNULL(total_out_of_stock, 0) / total_inventory) * 100, 2) AS out_of_stock_percentage,
        ROUND((total_stock_products / total_products) * 100, 2) AS stock_product_percentage,
        ROUND((total_non_stock_products / total_products) * 100, 2) AS non_stock_product_percentage;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_invoice_purchase_list` ()   BEGIN
    -- Retrieve the last three invoices
    SELECT 
        invoice_number, 
        invoice_date, 
        grand_total, 
        invoice_status
    FROM 
        inv_invoices
    ORDER BY 
        invoice_id DESC
    LIMIT 3;
    
    -- Retrieve the last three purchase orders
    SELECT 
        purchase_order_number, 
        purchase_order_date, 
        grand_total, 
        purchase_order_status
    FROM 
        inv_purchase_orders
    ORDER BY 
        purchase_order_id DESC
    LIMIT 3;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_monthly_bill_chart` ()   BEGIN
    DECLARE i INT DEFAULT 0;  -- Start at 0 to include the current month in the loop
    DECLARE last_12_months_start_date DATE;
    
    -- Initialize arrays for purchase order counts and invoice counts
    DECLARE purchase_order_counts TEXT DEFAULT '';
    DECLARE invoice_counts TEXT DEFAULT '';
    DECLARE month_names TEXT DEFAULT '';

    -- Calculate the start date for the last 12 months
    SET last_12_months_start_date = DATE_ADD(CURDATE(), INTERVAL -11 MONTH);

    -- Get purchase order counts for the last 12 months
    SELECT 
        GROUP_CONCAT(COALESCE(purchase_order_count, 0) ORDER BY month_num DESC) INTO purchase_order_counts
    FROM (
        SELECT 
            COUNT(purchase_order_id) AS purchase_order_count, 
            MONTH(purchase_order_date) AS month_num
        FROM inv_purchase_orders 
        WHERE purchase_order_date >= last_12_months_start_date
        GROUP BY month_num
    ) AS subquery;

    -- Get invoice counts for the last 12 months
    SELECT 
        GROUP_CONCAT(COALESCE(invoice_count, 0) ORDER BY month_num DESC) INTO invoice_counts
    FROM (
        SELECT 
            COUNT(invoice_id) AS invoice_count, 
            MONTH(invoice_date) AS month_num
        FROM inv_invoices 
        WHERE invoice_date >= last_12_months_start_date
        GROUP BY month_num
    ) AS subquery;

    -- Ensure there are 12 values for both purchase orders and invoices
    WHILE i < 12 DO  -- Loop from 0 to 11 for the last 12 months
        SET i = i + 1;
        IF FIND_IN_SET(i, purchase_order_counts) = 0 THEN
            SET purchase_order_counts = CONCAT(IF(purchase_order_counts = '', '', purchase_order_counts), ',0');
        END IF;
        IF FIND_IN_SET(i, invoice_counts) = 0 THEN
            SET invoice_counts = CONCAT(IF(invoice_counts = '', '', invoice_counts), ',0');
        END IF;
    END WHILE;

    -- Generate month names for the last 12 months, with current month first
    SET month_names = CONCAT_WS(',', 
        DATE_FORMAT(CURDATE(), '%M'),  -- Current month first
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -2 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -3 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -4 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -5 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -6 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -7 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -8 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -9 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -10 MONTH), '%M'),
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -11 MONTH), '%M')  -- Last month
    );

    -- Print the results
    SELECT 
        purchase_order_counts AS purchase_order_counts, 
        invoice_counts AS invoice_counts,
        month_names AS month_names;  -- Include month names in the result
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_sales_category_chart` ()   BEGIN
    -- Select the top 5 product categories based on the count of products sold
    SELECT 
        p.product_category,
        COUNT(ii.product_id) AS product_count
    FROM 
        inv_invoice_items ii
    JOIN 
        inv_invoices i ON ii.invoice_id = i.invoice_id
    JOIN 
        inv_products p ON ii.product_id = p.product_id
    WHERE 
        i.invoice_status = 1  -- Only count confirmed invoices
    GROUP BY 
        p.product_category
    ORDER BY 
        product_count DESC
    LIMIT 5;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_top_five_customers` ()   BEGIN
    -- Declare a variable to hold the total number of confirmed invoices
    DECLARE total_invoices_count INT;

    -- Calculate the total number of confirmed invoices
    SELECT COUNT(*) INTO total_invoices_count
    FROM inv_invoices
    WHERE invoice_status = 1; -- Only include confirmed invoices

    -- Select top 5 customers with their invoice count and whole number percentage
    SELECT 
        c.customer_id,
        c.customer_name AS full_customer_name,
        COUNT(i.invoice_id) AS total_invoices,
        FLOOR((COUNT(i.invoice_id) / total_invoices_count) * 100) AS purchase_percentage -- Get whole number percentage
    FROM 
        inv_customer c
    JOIN 
        inv_invoices i ON c.customer_id = i.customer_id
    WHERE 
        i.invoice_status = 1  -- Only include confirmed invoices
    GROUP BY 
        c.customer_id, c.salutation, c.customer_name
    ORDER BY 
        total_invoices DESC  -- Order by number of invoices
    LIMIT 5;  -- Get top 5 customers
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_top_section` ()   BEGIN
    DECLARE todayRevenue DECIMAL(10,2);
    DECLARE yesterdayRevenue DECIMAL(10,2);
    DECLARE todayPurchases INT;
    DECLARE yesterdayPurchases INT;
    DECLARE todaySales INT; -- New variable for today's sales count
    DECLARE yesterdaySales INT; -- New variable for yesterday's sales count
    DECLARE revenueDifference DECIMAL(10,2);
    DECLARE revenuePercentage DECIMAL(10,2);
    DECLARE profitOrLoss INT;

    -- Get today's revenue from invoices where invoice_status = 1
    SELECT COALESCE(SUM(subtotal), 0) INTO todayRevenue
    FROM inv_invoices
    WHERE DATE(invoice_date) = CURDATE()
      AND invoice_status = 1;

    -- Get yesterday's revenue from invoices where invoice_status = 1
    SELECT COALESCE(SUM(subtotal), 0) INTO yesterdayRevenue
    FROM inv_invoices
    WHERE DATE(invoice_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
      AND invoice_status = 1;

    -- Get today's count of purchases where purchase_order_status = 1
    SELECT COUNT(*) INTO todayPurchases
    FROM inv_purchase_orders
    WHERE DATE(purchase_order_date) = CURDATE()
      AND purchase_order_status = 1;

    -- Get yesterday's count of purchases where purchase_order_status = 1
    SELECT COUNT(*) INTO yesterdayPurchases
    FROM inv_purchase_orders
    WHERE DATE(purchase_order_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
      AND purchase_order_status = 1;

    -- Get today's count of sales where invoice_status = 1
    SELECT COUNT(*) INTO todaySales
    FROM inv_invoices
    WHERE DATE(invoice_date) = CURDATE()
      AND invoice_status = 1;

    -- Get yesterday's count of sales where invoice_status = 1
    SELECT COUNT(*) INTO yesterdaySales
    FROM inv_invoices
    WHERE DATE(invoice_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
      AND invoice_status = 1;

    -- Calculate revenue difference
    SET revenueDifference = ABS(todayRevenue - yesterdayRevenue); -- Use ABS() to ensure positive difference

    -- Calculate revenue percentage change
    IF yesterdayRevenue > 0 THEN
        SET revenuePercentage = LEAST(100, ABS((todayRevenue - yesterdayRevenue) / yesterdayRevenue * 100)); -- Cap at 100
    ELSE
        SET revenuePercentage = 0; -- No revenue yesterday
    END IF;

    -- Determine profit or loss (1 for profit, 0 for loss)
    IF todayRevenue > yesterdayRevenue THEN
        SET profitOrLoss = 1; -- Profit
    ELSEIF todayRevenue < yesterdayRevenue THEN
        SET profitOrLoss = 0; -- Loss
    ELSE
        SET profitOrLoss = 1; -- No change treated as profit
    END IF;

    -- Return results
    SELECT 
        todayRevenue AS TodaysRevenue,
        yesterdayRevenue AS YesterdaysRevenue,
        revenueDifference AS RevenueDifference,
        revenuePercentage AS RevenuePercentage,
        todayPurchases AS TodaysPurchasesCount,
        yesterdayPurchases AS YesterdaysPurchasesCount,
        todaySales AS TodaysSalesCount, -- Returning today's sales count
        yesterdaySales AS YesterdaysSalesCount, -- Returning yesterday's sales count
        profitOrLoss AS ProfitOrLoss;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dashboard_top_three_products` ()   BEGIN
    SELECT 
        p.product_id,
        p.product_name,
        COUNT(DISTINCT ii.invoice_id) AS total_invoices,
        SUM(ii.amount) AS total_revenue
    FROM 
        inv_invoice_items ii
    JOIN 
        inv_invoices i ON ii.invoice_id = i.invoice_id
    JOIN 
        inv_products p ON ii.product_id = p.product_id
    WHERE 
        i.invoice_status = 1 -- Only include delivered invoices
    GROUP BY 
        p.product_id, p.product_name
    ORDER BY 
        total_revenue DESC -- Order by total revenue
    LIMIT 3; -- Get top 3 products
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_customer` (IN `p_customer_id` INT)   BEGIN
    -- Update the deleted column to 1 (indicating deletion) for the specified customer
    UPDATE inv_customer
    SET deleted = 1
    WHERE customer_id = p_customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_product` (IN `p_product_id` INT)   BEGIN
    -- Update the inv_products table to set the deleted status to 1
    UPDATE inv_products
    SET deleted = 1
    WHERE product_id = p_product_id;

    -- Update the inv_product_items table to set the deleted status to 1 for related items
    UPDATE inv_product_items
    SET deleted = 1
    WHERE product_id = p_product_id;
    
    -- Check if any rows were affected
    IF ROW_COUNT() > 0 THEN
        SELECT 'success' AS status;
    ELSE
        SELECT 'error' AS status;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_role` (IN `p_role_id` INT)   BEGIN
    -- Update the deleted column to 1 for the given role_id
    UPDATE inv_role_permissions
    SET deleted = 1,
        updated_on = CURRENT_TIMESTAMP
    WHERE role_id = p_role_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_vendor` (IN `p_vendor_id` INT(11))   BEGIN
    DECLARE rows_affected INT;

    -- Update the inv_vendors table to set the deleted status to 1
    UPDATE inv_vendors
    SET deleted = 1
    WHERE vendor_id = p_vendor_id;

    -- Check if any rows were affected
    SET rows_affected = ROW_COUNT();
    
    IF rows_affected > 0 THEN
        SELECT 'success' AS status;
    ELSE
        SELECT 'error' AS status;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `edit_role` (IN `p_role_name` VARCHAR(50), IN `p_role_code` VARCHAR(20), IN `p_role_id` INT, IN `p_page_ids` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE total_pages INT;
    DECLARE v_page_id INT;

    -- Update role details in inv_role_permissions
    UPDATE inv_role_permissions
    SET role_name = p_role_name,
        role_code = p_role_code,
        updated_on = CURRENT_TIMESTAMP
    WHERE role_id = p_role_id;

	 -- Delete pages that are no longer part of the current page_ids
    DELETE FROM inv_role_pages
    WHERE role_id = p_role_id
    AND page_id NOT IN (
        SELECT JSON_UNQUOTE(JSON_EXTRACT(p_page_ids, CONCAT('$[', seq, ']')))
        FROM (
            SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
            UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 -- Extend as needed
        ) AS nums
    );
    
    -- Get the total number of page_ids
    SET total_pages = JSON_LENGTH(p_page_ids);

    -- Loop through the page_ids and check for existence in inv_role_pages
    WHILE i < total_pages DO
        -- Extract page_id from the JSON array
        SET v_page_id = JSON_UNQUOTE(JSON_EXTRACT(p_page_ids, CONCAT('$[', i, ']')));

        -- Check if the role_id and page_id combination already exists
        IF NOT EXISTS (
            SELECT 1 FROM inv_role_pages WHERE role_id = p_role_id AND page_id = v_page_id
        ) THEN
            -- Insert a new record if it does not exist
            INSERT INTO inv_role_pages (role_id, page_id) VALUES (p_role_id, v_page_id);
        END IF;

        SET i = i + 1;
    END WHILE;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_customer_card_details` ()   BEGIN
    DECLARE total_customers INT;
    DECLARE total_active_customers INT;
    DECLARE total_inactive_customers INT;

    -- Calculate the total number of customers
    SELECT COUNT(*) INTO total_customers
    FROM inv_customer
    WHERE deleted = 0;

    -- Calculate the total number of active customers
    SELECT COUNT(*) INTO total_active_customers
    FROM inv_customer
    WHERE customer_status = 1 AND deleted = 0;

    -- Calculate the total number of inactive customers
    SELECT COUNT(*) INTO total_inactive_customers
    FROM inv_customer
    WHERE customer_status = 0 AND deleted = 0;

    -- Return the results as a single result set
    SELECT 
        total_customers AS TotalCustomers,
        total_active_customers AS TotalActiveCustomers,
        total_inactive_customers AS TotalInactiveCustomers;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_customer_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_customer 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_customer 
        WHERE (customer_code LIKE ? 
        OR customer_name LIKE ? 
        OR customer_phone_number LIKE ?)
        AND deleted = 0';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT customer_id, customer_code, customer_name, customer_phone_number, customer_status
        FROM inv_customer 
        WHERE (customer_code LIKE ? 
        OR customer_name LIKE ? 
        OR customer_phone_number LIKE ?)
        AND deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_delivery_challan_card_details` ()   BEGIN
    SELECT 
        COUNT(*) AS TotalDeliveryChallans
    FROM 
        inv_delivery_challan
    WHERE 
        deleted = 0; -- Assuming 'deleted' flag indicates soft-deletion
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_delivery_challan_details` (IN `p_dc_id` INT)   BEGIN
    -- Declare a variable to store the status of the procedure execution
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET @error = 1;

    -- Initialize error variable
    SET @error = 0;

    -- Fetch delivery challan details
    SELECT 
        dc.delivery_challan_id,
        dc.delivery_challan_number,
        c.customer_name,
        c.address_street,
        c.address_locality,
        c.address_district,
        c.address_city,
        c.address_state,
        c.address_pincode,
        c.address_country,
        dc.delivery_challan_date,
        dc.created_on,
        dc.updated_on,
        dc.created_by,
        dc.delivery_challan_status,
        dc.delivery_date
    FROM 
        inv_delivery_challan dc
    JOIN 
        inv_customer c ON dc.customer_id = c.customer_id
    WHERE 
        dc.delivery_challan_id = p_dc_id
        AND dc.deleted = 0;

    -- Check if there were no results from the first query
    IF @error = 1 THEN
        SELECT 'No delivery challan found' AS message;
    ELSE
        -- Fetch delivery challan items along with product details
        SELECT 
            dci.delivery_challan_item_id,
            dci.delivery_challan_id,
            c.customer_name,
            p.product_name,
            p.unit_of_measure,
            dci.quantity,
            dci.date
        FROM 
            inv_delivery_challan_items dci
        JOIN 
            inv_customer c ON dci.customer_id = c.customer_id
        JOIN 
            inv_products p ON dci.product_id = p.product_id
        WHERE 
            dci.delivery_challan_id = p_dc_id
            AND dci.deleted = 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_delivery_challan_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(64), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_delivery_challan 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_delivery_challan dc
        JOIN inv_customer c ON dc.customer_id = c.customer_id
        WHERE (dc.delivery_challan_number LIKE ? 
        OR c.customer_name LIKE ?)
        AND dc.deleted = 0';
        
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    -- Retrieve the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT dc.delivery_challan_id, dc.delivery_challan_number, DATE_FORMAT(dc.delivery_challan_date, "%d-%m-%Y") AS delivery_challan_date, 
        c.customer_name, DATE_FORMAT(dc.delivery_date, "%d-%m-%Y") AS delivery_date
        FROM inv_delivery_challan dc
        JOIN inv_customer c ON dc.customer_id = c.customer_id
        WHERE (dc.delivery_challan_number LIKE ? 
        OR c.customer_name LIKE ?)
        AND dc.deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_inventory_card_details` ()   BEGIN
    DECLARE total_inventory_products_count INT;
    DECLARE total_in_stock_products_count INT;
    DECLARE inventory_value DECIMAL(15,2);

    -- Fetch total number of products in the inventory
    SELECT COUNT(*) INTO total_inventory_products_count
    FROM inv_inventory;

    -- Fetch total number of in-stock products where inventory_status = 1
    SELECT COUNT(*) INTO total_in_stock_products_count
    FROM inv_inventory
    WHERE inventory_status = 1;

    -- Calculate total inventory value
    SELECT SUM(i.quantity_in_stock * p.unit_price) INTO inventory_value
    FROM inv_inventory i
    JOIN inv_products p ON i.product_id = p.product_id;

    -- Return results as a single result set
    SELECT 
        total_inventory_products_count, 
        total_in_stock_products_count, 
        inventory_value;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_inventory_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(255), IN `p_order_dir` VARCHAR(10), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_inventory i
    JOIN inv_products p ON i.product_id = p.product_id
    WHERE i.inventory_status = 1;

    -- Get total number of filtered records
    SET @query = CONCAT(
        'SELECT COUNT(*) 
        FROM inv_inventory i
        JOIN inv_products p ON i.product_id = p.product_id
        WHERE (p.product_name LIKE ? 
        OR p.product_code LIKE ? 
        OR i.unit_of_measure LIKE ?)
        AND i.inventory_status = 1');
        
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT p.product_name, p.product_code, p.product_id, i.unit_of_measure,i.inventory_id, i.quantity_in_stock, p.updated_on 
        FROM inv_inventory i
        JOIN inv_products p ON i.product_id = p.product_id
        WHERE (p.product_name LIKE ? 
        OR p.product_code LIKE ? 
        OR i.unit_of_measure LIKE ?)
        AND i.inventory_status = 1 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_invoice_card_details` ()   BEGIN
    DECLARE total_invoices INT;
    DECLARE total_active_invoices INT;
    DECLARE total_inactive_invoices INT;

    -- Fetch the total number of invoices
    SELECT COUNT(*) INTO total_invoices FROM inv_invoices WHERE deleted = 0;

    -- Fetch the total number of active invoices
    SELECT COUNT(*) INTO total_active_invoices FROM inv_invoices WHERE invoice_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive invoices
    SELECT COUNT(*) INTO total_inactive_invoices FROM inv_invoices WHERE invoice_status = 0 AND deleted = 0;

    -- Return the results
    SELECT 
        total_invoices AS TotalInvoice,
        total_active_invoices AS TotalActiveInvoice,
        total_inactive_invoices AS TotalInactiveInvoice;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_invoice_latest_price_unit` (IN `p_product_id` INT)   BEGIN
    DECLARE v_unit_price DECIMAL(10, 2) DEFAULT 0;
    DECLARE v_unit_of_measure VARCHAR(50) DEFAULT '0';

    -- Attempt to retrieve unit_price and unit_of_measure from inv_purchase_order_items
    SELECT unit_price, unit_of_measure
    INTO v_unit_price, v_unit_of_measure
    FROM inv_purchase_order_items
    WHERE product_id = p_product_id
      AND deleted = 0
    ORDER BY date DESC
    LIMIT 1;

    -- If no unit_of_measure was found, fetch it from inv_products
    IF v_unit_of_measure = '0' THEN
        SELECT unit_of_measure
        INTO v_unit_of_measure
        FROM inv_products
        WHERE product_id = p_product_id
          AND deleted = 0;
    END IF;

    -- Return values (or default values if not found)
    SELECT COALESCE(v_unit_price, 0) AS unit_price, 
           COALESCE(v_unit_of_measure, '0') AS unit_of_measure;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_invoice_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_invoices 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_invoices i
        JOIN inv_customer c ON i.customer_id = c.customer_id
        WHERE (i.invoice_number LIKE ? 
        OR c.customer_name LIKE ?)
        AND i.deleted = 0';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    DEALLOCATE PREPARE stmt;

    -- Fetch the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT i.invoice_id, i.invoice_number, DATE_FORMAT(i.invoice_date, "%d-%m-%Y") AS invoice_date, 
        c.customer_name, i.grand_total, i.invoice_status
        FROM inv_invoices i
        JOIN inv_customer c ON i.customer_id = c.customer_id
        WHERE (i.invoice_number LIKE ? 
        OR c.customer_name LIKE ?)
        AND i.deleted = 0 
        ORDER BY i.invoice_status = 0 DESC, i.invoice_date DESC, ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_inv_firm_profile` ()   BEGIN
    -- Select the firm profile based on the provided company_id
    SELECT
        company_id,
        firm_name,
        registration_number,
        logo,
        phone_number,
        email_id,
        street,
        locality,
        city,
        district,
        country,
        state,
        pin_code,
        gstin,
        pan,
        tax_registration_number,
        default_tax_percentage,
        bank_name,
        account_number,
        ifsc_code,
        bank_branch,
        invoice_terms_and_conditions,
        log_id,
        updated_on
    FROM inv_firm_profile
    WHERE company_id = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_last_delivery_challan_number` ()   BEGIN
    DECLARE last_number INT;

    -- Fetch the last delivery challan number and extract the numeric part
    SELECT 
        CAST(SUBSTRING_INDEX(delivery_challan_number, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_delivery_challan
    ORDER BY created_on DESC
    LIMIT 1;

    -- If no delivery challan exists, set the number to 0
    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    -- Generate the new delivery challan number with DC- prefix
    SELECT CONCAT('DC-', LPAD(last_number + 1, 3, '0')) AS new_delivery_challan_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_last_invoice_number` ()   BEGIN
    DECLARE last_number INT;
    DECLARE new_invoice_number VARCHAR(50);

    -- Fetch the last invoice number and extract the numerical part
    SELECT 
        CAST(SUBSTRING_INDEX(invoice_number, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_invoices
    ORDER BY created_on DESC
    LIMIT 1;

    -- If no invoice is found, start with 0
    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    -- Generate the new invoice number
    SET new_invoice_number = CONCAT('INV-', LPAD(last_number + 1, 3, '0'));

    -- Return the new invoice number
    SELECT new_invoice_number AS new_invoice_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_last_purchase_order_number` ()   BEGIN
    DECLARE last_number INT;

    SELECT 
        CAST(SUBSTRING_INDEX(purchase_order_number, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_purchase_orders
    ORDER BY created_on DESC
    LIMIT 1;

    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    SELECT CONCAT('PO-', LPAD(last_number + 1, 3, '0')) AS new_purchase_order_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_last_quotation_number` ()   BEGIN
    DECLARE last_number INT;

    SELECT 
        CAST(SUBSTRING_INDEX(quotation_number, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_quotations
    ORDER BY created_on DESC
    LIMIT 1;

    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    SELECT CONCAT('QTN-', LPAD(last_number + 1, 3, '0')) AS new_quotation_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_last_vendor_invoice_number` ()   BEGIN
    DECLARE last_number INT;
    DECLARE new_invoice_number VARCHAR(50);

    -- Fetch the last vendor invoice number and extract the numerical part
    SELECT 
        CAST(SUBSTRING_INDEX(vendor_invoice_number, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_vendor_invoices
    ORDER BY created_on DESC
    LIMIT 1;

    -- If no invoice is found, start with 0
    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    -- Generate the new vendor invoice number
    SET new_invoice_number = CONCAT('INV-', LPAD(last_number + 1, 3, '0'));

    -- Return the new vendor invoice number
    SELECT new_invoice_number AS new_vendor_invoice_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_order_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(64), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_purchase_orders 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_purchase_orders po
        JOIN inv_vendors v ON po.vendor_id = v.vendor_id
        WHERE (po.purchase_order_number LIKE ? 
        OR v.vendor_company_name LIKE ?)
        AND po.deleted = 0';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    -- Retrieve the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT po.purchase_order_id, po.purchase_order_number, DATE_FORMAT(po.purchase_order_date, "%d-%m-%Y") AS purchase_order_date, 
        v.vendor_company_name, po.grand_total, po.purchase_order_status
        FROM inv_purchase_orders po
        JOIN inv_vendors v ON po.vendor_id = v.vendor_id
        WHERE (po.purchase_order_number LIKE ? 
        OR v.vendor_company_name LIKE ?)
        AND po.deleted = 0 
        ORDER BY po.purchase_order_status = 0 DESC, po.purchase_order_date DESC, ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_out_of_stock_card_details` ()   BEGIN
    -- Declare variables to hold the counts
    DECLARE out_of_stock_count INT DEFAULT 0;
    DECLARE canceled_out_of_stock_count INT DEFAULT 0;
    
    -- Fetch the count of out-of-stock products from inv_inventory
    SELECT COUNT(*) INTO out_of_stock_count
    FROM `inv_inventory`
    WHERE `inventory_status` = 0;
    
    -- Fetch the count of canceled out-of-stock records from inv_out_of_stock
    SELECT COUNT(*) INTO canceled_out_of_stock_count
    FROM `inv_out_of_stock`
    WHERE `out_of_stock_status` = 3 AND `deleted` = 1;
    
    -- Output the results
    SELECT out_of_stock_count AS OutOfStockCount, 
           canceled_out_of_stock_count AS CanceledOutOfStockCount;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_out_of_stock_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(255), IN `p_order_dir` ENUM('asc','desc'), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get the total number of records with out_of_stock_status = 0
    SELECT COUNT(*) INTO total_records 
    FROM inv_out_of_stock inv 
    WHERE inv.out_of_stock_status = 0;

    -- Construct the filtered records query
    SET @query = '
        SELECT COUNT(*)
        FROM inv_out_of_stock inv
        JOIN inv_products prod ON inv.product_id = prod.product_id
        JOIN (
            WITH LatestPOItems AS (
                SELECT 
                    po_items.product_id, 
                    po_items.vendor_id, 
                    po_items.unit_of_measure, 
                    po_items.unit_price, 
                    po_items.date, 
                    po_items.purchase_order_item_id,
                    ROW_NUMBER() OVER (PARTITION BY po_items.product_id, po_items.vendor_id ORDER BY po_items.date DESC) AS rn
                FROM inv_purchase_order_items po_items
                WHERE po_items.deleted = 0
            )
            SELECT *
            FROM LatestPOItems
            WHERE rn = 1
        ) AS po_items ON inv.product_id = po_items.product_id
        JOIN inv_vendors ven ON po_items.vendor_id = ven.vendor_id
        WHERE inv.out_of_stock_status = 0 
        AND (prod.product_name LIKE ? 
        OR po_items.unit_of_measure LIKE ? 
        OR prod.order_quantity LIKE ? 
        OR ven.vendor_company_name LIKE ?)';

    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Construct the main query with sorting and pagination
    SET @data_query = CONCAT(
        'SELECT 
            prod.product_name,
            prod.product_id,
            prod.unit_of_measure,
        	inv.out_of_stock_status,
        	inv.out_of_stock_id,
            CONCAT(
                ''['', 
                GROUP_CONCAT(
                    CONCAT(
                        ''{"vendor_name": "'', ven.vendor_company_name, 
                        ''","vendor_id": "'', ven.vendor_id, 
                        ''", "unit_of_measure": "'', po_items.unit_of_measure, 
                        ''", "unit_price": "'', po_items.unit_price, 
                        ''", "purchase_order_item_id": "'', po_items.purchase_order_item_id, ''"}''
                    )
                ),
                '']''
            ) AS purchase_orders,
            prod.order_quantity
        FROM inv_out_of_stock inv
        JOIN inv_products prod ON inv.product_id = prod.product_id
        JOIN (
            WITH LatestPOItems AS (
                SELECT 
                    po_items.product_id, 
                    po_items.vendor_id, 
                    po_items.unit_of_measure, 
                    po_items.unit_price, 
                    po_items.date, 
                    po_items.purchase_order_item_id,
                    ROW_NUMBER() OVER (PARTITION BY po_items.product_id, po_items.vendor_id ORDER BY po_items.date DESC) AS rn
                FROM inv_purchase_order_items po_items
                WHERE po_items.deleted = 0
            )
            SELECT *
            FROM LatestPOItems
            WHERE rn = 1
        ) AS po_items ON inv.product_id = po_items.product_id
        JOIN inv_vendors ven ON po_items.vendor_id = ven.vendor_id
        WHERE inv.out_of_stock_status = 0 
        AND (prod.product_name LIKE ? 
        OR po_items.unit_of_measure LIKE ? 
        OR prod.order_quantity LIKE ?)
        GROUP BY prod.product_id
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pages_with_pre_role_code` ()   BEGIN
    DECLARE last_number INT;

    -- Fetch the last numeric part of role_code
    SELECT 
        CAST(SUBSTRING_INDEX(role_code, '-', -1) AS UNSIGNED) 
    INTO last_number
    FROM inv_role_permissions
    WHERE deleted = 0
    ORDER BY created_on DESC
    LIMIT 1;

    -- If no role_code exists, start with 0
    IF last_number IS NULL THEN
        SET last_number = 0;
    END IF;

    -- Return the new role_code by incrementing the last number
    SELECT CONCAT('R-', LPAD(last_number + 1, 3, '0')) AS new_role_code;

    -- Fetch all pages from the inv_pages table
    SELECT 
        page_id, 
        page_name, 
        page_type, 
        page_status 
    FROM inv_pages 
    WHERE deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_price_history_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_products 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*)
        FROM inv_products p
        JOIN inv_purchase_order_items poi ON p.product_id = poi.product_id
        WHERE (p.product_name LIKE ? 
        OR p.unit_of_measure LIKE ? 
        OR poi.unit_price LIKE ?)
        AND p.deleted = 0 AND poi.deleted = 0';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT p.product_id, p.product_name, p.unit_of_measure, poi.unit_price AS current_price,poi.unit_of_measure AS 		purchase_unit_of_measure
        FROM inv_products p
        JOIN inv_purchase_order_items poi ON p.product_id = poi.product_id
        WHERE (p.product_name LIKE ? 
        OR p.unit_of_measure LIKE ? 
        OR poi.unit_price LIKE ?)
        AND p.deleted = 0 AND poi.deleted = 0
        GROUP BY p.product_id, p.unit_of_measure
        ORDER BY ', p_sort_column, ' ', p_order_dir, '
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;

    -- Return the actual data in a separate result set
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_product_card_details` ()   BEGIN
    DECLARE total_products INT;
    DECLARE total_active_products INT;
    DECLARE total_inactive_products INT;

    -- Fetch the total number of products
    SELECT COUNT(*) INTO total_products FROM inv_products WHERE deleted = 0;

    -- Fetch the total number of active products
    SELECT COUNT(*) INTO total_active_products FROM inv_products WHERE product_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive products
    SELECT COUNT(*) INTO total_inactive_products FROM inv_products WHERE product_status = 0 AND deleted = 0;

    -- Return the results
    SELECT 
        total_products AS TotalProducts,
        total_active_products AS TotalActiveProducts,
        total_inactive_products AS TotalInactiveProducts;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_product_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(255), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records FROM inv_products WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_products 
        WHERE (product_name LIKE ? 
        OR product_code LIKE ? 
        OR product_category LIKE ? 
        OR product_type LIKE ? 
        OR unit_of_measure LIKE ? 
        OR product_status LIKE ?)
        AND deleted = 0 ';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search, @search, @search, @search;
    DEALLOCATE PREPARE stmt;

    SELECT FOUND_ROWS() INTO filtered_records;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT product_id, product_name, product_code, product_category, 
        CASE product_type
            WHEN 1 THEN "Product"
            WHEN 0 THEN "Stock"
            ELSE "Unknown"
        END AS product_type,
        unit_of_measure, bottom_stock, order_quantity, product_status 
        FROM inv_products 
        WHERE (product_name LIKE ? 
        OR product_code LIKE ? 
        OR product_category LIKE ? 
        OR product_type LIKE ? 
        OR unit_of_measure LIKE ? 
        OR product_status LIKE ?)
        AND deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?'); -- Adjusted for pagination

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, @search, @search, @search, p_start, p_length; -- Added p_start and p_length for pagination
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_purchase_history_card_details` ()   BEGIN
    DECLARE total_purchases INT;
    DECLARE total_active_purchases INT;
    DECLARE total_inactive_purchases INT;

    -- Fetch the total number of purchase orders
    SELECT COUNT(*) INTO total_purchases FROM inv_purchase_orders WHERE deleted = 0;

    -- Fetch the total number of active purchase orders
    SELECT COUNT(*) INTO total_active_purchases FROM inv_purchase_orders WHERE purchase_order_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive purchase orders
    SELECT COUNT(*) INTO total_inactive_purchases FROM inv_purchase_orders WHERE purchase_order_status = 2 AND deleted = 0;

    -- Return the results
    SELECT 
        total_purchases AS TotalPurchases,
        total_active_purchases AS TotalActivePurchases,
        total_inactive_purchases AS TotalInactivePurchases;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_purchase_order_details` (IN `p_purchase_order_id` INT)   BEGIN
    -- Fetch purchase order details
    SELECT 
        po.purchase_order_id,
        po.purchase_order_number,
        po.purchase_order_date,
        po.subtotal,
        po.discount,
        po.adjustment,
        po.discount_amount,
        po.grand_total,
        po.amount_in_words,
        po.created_on,
        po.updated_on,
        po.created_by,
        po.purchase_order_status,
        po.purchased_date,
        po.deleted,
        v.vendor_id,
        v.vendor_company_name,
        v.vendor_contact_name,
        v.vendor_phone_number,
        v.vendor_email_id,
        v.vendor_gstin,
        v.billing_address_street,
        v.billing_address_locality,
        v.billing_address_city,
        v.billing_address_state,
        v.billing_address_pincode,
        v.shipping_address_street,
        v.shipping_address_locality,
        v.shipping_address_city,
        v.shipping_address_state,
        v.shipping_address_district,
        v.shipping_address_country,
        v.shipping_address_pincode,
        v.billing_address_district,
        v.billing_address_country
    FROM inv_purchase_orders po
    JOIN inv_vendors v ON po.vendor_id = v.vendor_id
    WHERE po.purchase_order_id = p_purchase_order_id AND po.deleted=0 AND v.deleted=0;

    -- Fetch purchase order items along with product name
    SELECT 
        poi.purchase_order_item_id,
        poi.product_id,
        p.product_name,
        p.unit_of_measure AS product_unit_of_measure,
        poi.unit_of_measure,
        poi.quantity,
        poi.unit_price,
        poi.amount,
        poi.purchase_order_item_status,
        poi.deleted
    FROM inv_purchase_order_items poi
    JOIN inv_products p ON poi.product_id = p.product_id
    WHERE poi.purchase_order_id = p_purchase_order_id AND poi.deleted=0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_quotation_card_details` ()   BEGIN
    DECLARE total_quotations INT;
    DECLARE total_active_quotations INT;
    DECLARE total_inactive_quotations INT;

    -- Fetch the total number of quotations
    SELECT COUNT(*) INTO total_quotations FROM inv_quotations WHERE deleted = 0;

    -- Fetch the total number of active quotations
    SELECT COUNT(*) INTO total_active_quotations FROM inv_quotations WHERE quotation_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive quotations
    SELECT COUNT(*) INTO total_inactive_quotations FROM inv_quotations WHERE quotation_status = 2 AND deleted = 0;

    -- Return the results
    SELECT 
        total_quotations AS TotalQuotations,
        total_active_quotations AS TotalActiveQuotations,
        total_inactive_quotations AS TotalInactiveQuotations;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_quotation_details` (IN `quotation_id` INT)   BEGIN
    -- Fetch quotation details along with customer details
    SELECT 
        q.quotation_id,
        q.quotation_number,
        q.customer_id,
        q.quotation_date,
        q.subtotal,
        q.discount,
        q.adjustment,
        q.discount_amount,
        q.grand_total,
        q.amount_in_words,
        q.created_on,
        q.updated_on,
        q.created_by,
        q.quotation_status,
        q.deleted,
        q.finalized_date,
        c.customer_code,
        c.salutation,
        c.customer_name,
        c.customer_phone_number,
        c.customer_email_id,
        c.address_street,
        c.address_locality,
        c.address_district,
        c.address_city,
        c.address_state,
        c.address_pincode,
        c.address_country,
        c.customer_gstin
    FROM 
        inv_quotations q
    JOIN 
        inv_customer c ON q.customer_id = c.customer_id
    WHERE 
        q.quotation_id = quotation_id AND q.deleted = 0 AND c.deleted = 0;

    -- Fetch quotation items along with product details
    SELECT 
        qi.quotation_item_id,
        qi.product_id,
        p.product_name,
        p.product_code,
        p.unit_of_measure AS product_unit_of_measure,
        qi.unit_of_measure AS item_unit_of_measure,
        qi.quantity,
        qi.unit_price,
        qi.amount,
        qi.quotation_item_status,
        qi.deleted,
        inv.quantity_in_stock,
        inv.unit_of_measure AS inventory_unit_of_measure
    FROM 
        inv_quotation_items qi
    JOIN 
        inv_products p ON qi.product_id = p.product_id
    LEFT JOIN 
        inv_inventory inv ON qi.product_id = inv.product_id
    WHERE 
        qi.quotation_id = quotation_id AND qi.deleted = 0;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_quotation_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(255), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records (not deleted)
    SELECT COUNT(*) INTO total_records 
    FROM inv_quotations 
    WHERE deleted = 0;

    -- Get total number of filtered records based on search value
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_quotations q
        JOIN inv_customer v ON q.customer_id = v.customer_id
        WHERE (q.quotation_number LIKE ? 
        OR v.customer_name LIKE ?)
        AND q.deleted = 0';
    
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    -- Retrieve the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT q.quotation_id, q.quotation_number, 
        DATE_FORMAT(q.quotation_date, "%d-%m-%Y") AS quotation_date, 
        v.customer_name, q.grand_total, q.quotation_status
        FROM inv_quotations q
        JOIN inv_customer v ON q.customer_id = v.customer_id
        WHERE (q.quotation_number LIKE ? 
        OR v.customer_name LIKE ?)
        AND q.deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return the total records and filtered records in separate result sets
    SELECT total_records AS total_records, filtered_records AS filtered_records;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_restock_details` (IN `p_purchase_order_item_id` INT)   BEGIN
    DECLARE last_number INT DEFAULT 0;
    DECLARE next_number INT DEFAULT 0;
    DECLARE prefix VARCHAR(10) DEFAULT 'PO-'; -- Adjust prefix as needed
    DECLARE last_purchase_order_number VARCHAR(50) DEFAULT NULL;
    DECLARE next_purchase_order_number VARCHAR(50);

    -- Retrieve purchase order item details
    SELECT 
        po_items.purchase_order_item_id,
        po_items.vendor_id,
        po_items.product_id,
        po_items.unit_price,
        po_items.unit_of_measure AS po_unit_of_measure,
        prod.product_name,
        prod.unit_of_measure AS product_unit_of_measure,
        prod.order_quantity AS order_quantity
    FROM 
        inv_purchase_order_items po_items
    JOIN 
        inv_products prod ON po_items.product_id = prod.product_id
    WHERE 
        po_items.purchase_order_item_id = p_purchase_order_item_id
        AND po_items.deleted = 0
        AND prod.deleted = 0;

    -- Get the last purchase order number
    SELECT 
        purchase_order_number 
    INTO 
        last_purchase_order_number
    FROM 
        inv_purchase_orders
    ORDER BY 
        purchase_order_date DESC
    LIMIT 1;

    -- Extract the numeric part and increment
    IF last_purchase_order_number IS NOT NULL THEN
        SET last_number = CAST(SUBSTRING_INDEX(last_purchase_order_number, '-', -1) AS UNSIGNED);
        SET next_number = last_number + 1;
    ELSE
        SET next_number = 1; -- Start with 1 if no previous order exists
    END IF;

    -- Format the next purchase order number without the date
    SET next_purchase_order_number = CONCAT(prefix, LPAD(next_number, 3, '0'));

    -- Return the results
    SELECT
        last_purchase_order_number AS last_purchase_order_number,
        next_purchase_order_number AS next_purchase_order_number;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_role_details_and_pages` (IN `p_role_id` INT)   BEGIN

    -- Fetch role details from inv_role_permissions
    SELECT role_name, role_code, role_permission_status
    FROM inv_role_permissions
    WHERE role_id = p_role_id AND role_permission_status = 1 AND deleted = 0;



    -- Fetch associated pages from inv_role_pages
    SELECT page_id,role_page_id
    FROM inv_role_pages
    WHERE role_id = p_role_id;
    
     -- Call fetch_pages_with_pre_role_code procedure
    CALL fetch_pages_with_pre_role_code();
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_role_permission_card_details` ()   BEGIN
    -- Fetch the total number of roles, active roles, and inactive roles
    SELECT 
        COUNT(*) AS TotalRoles,
        SUM(CASE WHEN role_permission_status = 1 THEN 1 ELSE 0 END) AS TotalActiveRoles,
        SUM(CASE WHEN role_permission_status = 0 THEN 1 ELSE 0 END) AS TotalInactiveRoles
    FROM 
        inv_role_permissions
    WHERE 
        deleted = 0; -- Only consider roles that are not deleted
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_role_permission_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(255), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_role_permissions 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_role_permissions 
        WHERE (role_name LIKE ? 
        OR role_code LIKE ?)
        AND deleted = 0';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT role_id, role_name, role_code, role_permission_status 
        FROM inv_role_permissions 
        WHERE (role_name LIKE ? 
        OR role_code LIKE ?)
        AND deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_sales_report_card_details` ()   BEGIN
    SELECT 
        COUNT(invoice_id) AS TotalSales, 
        IFNULL(SUM(subtotal), 0) AS TotalRevenue
    FROM 
        inv_invoices
    WHERE 
        invoice_status = 1;  -- Assuming '1' indicates a confirmed invoice
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_sales_report_table_data` (IN `p_search_value` VARCHAR(255), IN `p_start` INT, IN `p_length` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of active products
    SELECT COUNT(*) INTO total_records 
    FROM inv_products 
    WHERE product_status = 1;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_products p
        LEFT JOIN inv_invoice_items i ON p.product_id = i.product_id AND i.invoice_items_status = 1
        WHERE p.product_status = 1
        AND (p.product_name LIKE ? 
        OR p.product_code LIKE ? 
        OR p.unit_of_measure LIKE ?)';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search;
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt;

    -- Fetch the data with pagination
    SET @data_query = '
        SELECT 
            p.product_id, 
            p.product_name, 
            p.product_code, 
            p.unit_of_measure AS product_unit_of_measure,
            IFNULL(i.unit_of_measure, p.unit_of_measure) AS sales_unit_of_measure,
            COALESCE(i.amount, 0) AS amount,
            COALESCE(i.quantity, 0) AS quantity
        FROM inv_products p
        LEFT JOIN inv_invoice_items i ON p.product_id = i.product_id AND i.invoice_items_status = 1
        WHERE p.product_status = 1
        AND (p.product_name LIKE ? 
        OR p.product_code LIKE ? 
        OR p.unit_of_measure LIKE ?)
        LIMIT ?, ?';

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    -- Return the actual data in a separate result set
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_transactions_card_details` ()   BEGIN
    -- Declare variables to hold the results
    DECLARE total_income DECIMAL(10,2);
    DECLARE total_expense DECIMAL(10,2);
    DECLARE today_income DECIMAL(10,2);
    DECLARE today_expense DECIMAL(10,2);

    -- Calculate total income
    SELECT COALESCE(SUM(subtotal), 0) INTO total_income
    FROM inv_invoices
    WHERE invoice_status = 1;

    -- Calculate total expense
    SELECT COALESCE(SUM(grand_total), 0) INTO total_expense
    FROM inv_purchase_orders
    WHERE purchase_order_status = 1;

    -- Calculate today's income
    SELECT COALESCE(SUM(subtotal), 0) INTO today_income
    FROM inv_invoices
    WHERE DATE(invoice_date) = CURDATE()
    AND invoice_status = 1;

    -- Calculate today's expense
    SELECT COALESCE(SUM(grand_total), 0) INTO today_expense
    FROM inv_purchase_orders
    WHERE DATE(purchase_order_date) = CURDATE()
    AND purchase_order_status = 1;

    -- Return the results
    SELECT 
        total_income,
        total_expense,
        today_income,
        today_expense;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_transactions_chart_details` (IN `timeframe` VARCHAR(10))   BEGIN
    IF timeframe = 'daily' THEN
        SELECT
            period_label,
            SUM(income) AS income,
            SUM(expense) AS expense
        FROM (
            SELECT
                DATE(invoice_date) AS period_label,
                COALESCE(SUM(grand_total), 0) AS income,
                0 AS expense
            FROM inv_invoices
            WHERE invoice_status = 1
            AND DATE(invoice_date) >= CURDATE() - INTERVAL 7 DAY
            GROUP BY DATE(invoice_date)
            
            UNION ALL
            
            SELECT
                DATE(purchase_order_date) AS period_label,
                0 AS income,
                COALESCE(SUM(grand_total), 0) AS expense
            FROM inv_purchase_orders
            WHERE purchase_order_status = 1
            AND DATE(purchase_order_date) >= CURDATE() - INTERVAL 7 DAY
            GROUP BY DATE(purchase_order_date)
        ) AS combined_data
        GROUP BY period_label
        ORDER BY period_label;

    ELSEIF timeframe = 'monthly' THEN
        SELECT
            period_label,
            SUM(income) AS income,
            SUM(expense) AS expense
        FROM (
            SELECT
                DATE_FORMAT(invoice_date, '%Y-%m') AS period_label,
                COALESCE(SUM(grand_total), 0) AS income,
                0 AS expense
            FROM inv_invoices
            WHERE invoice_status = 1
            AND DATE(invoice_date) >= CURDATE() - INTERVAL 12 MONTH
            GROUP BY DATE_FORMAT(invoice_date, '%Y-%m')
            
            UNION ALL
            
            SELECT
                DATE_FORMAT(purchase_order_date, '%Y-%m') AS period_label,
                0 AS income,
                COALESCE(SUM(grand_total), 0) AS expense
            FROM inv_purchase_orders
            WHERE purchase_order_status = 1
            AND DATE(purchase_order_date) >= CURDATE() - INTERVAL 12 MONTH
            GROUP BY DATE_FORMAT(purchase_order_date, '%Y-%m')
        ) AS combined_data
        GROUP BY period_label
        ORDER BY period_label;

    ELSEIF timeframe = 'yearly' THEN
        SELECT
            period_label,
            SUM(income) AS income,
            SUM(expense) AS expense
        FROM (
            SELECT
                YEAR(invoice_date) AS period_label,
                COALESCE(SUM(grand_total), 0) AS income,
                0 AS expense
            FROM inv_invoices
            WHERE invoice_status = 1
            AND DATE(invoice_date) >= CURDATE() - INTERVAL 7 YEAR
            GROUP BY YEAR(invoice_date)
            
            UNION ALL
            
            SELECT
                YEAR(purchase_order_date) AS period_label,
                0 AS income,
                COALESCE(SUM(grand_total), 0) AS expense
            FROM inv_purchase_orders
            WHERE purchase_order_status = 1
            AND DATE(purchase_order_date) >= CURDATE() - INTERVAL 7 YEAR
            GROUP BY YEAR(purchase_order_date)
        ) AS combined_data
        GROUP BY period_label
        ORDER BY period_label;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_transactions_table` (IN `year` VARCHAR(4), IN `type` INT)   BEGIN
    IF type = 0 THEN
        -- Fetch data from inv_invoices
        SELECT 
            DATE_FORMAT(invoice_date, '%M') AS month_name,
            DATE(invoice_date) AS date,
            invoice_number AS invoice_number,
            subtotal AS subtotal,
            total_gst_amount AS gst_amount,
            grand_total AS grand_total
        FROM inv_invoices
        WHERE YEAR(invoice_date) = year AND invoice_status = 1
        ORDER BY invoice_date;

    ELSEIF type = 1 THEN
        -- Fetch data from inv_purchase_orders
        SELECT 
            DATE_FORMAT(purchase_order_date, '%M') AS month_name,
            DATE(purchase_order_date) AS date,
            purchase_order_number AS invoice_number,
            subtotal AS subtotal,
            grand_total - subtotal AS gst_amount, -- Assuming GST amount is derived like this
            grand_total AS grand_total
        FROM inv_purchase_orders
        WHERE YEAR(purchase_order_date) = year AND purchase_order_status = 1
        ORDER BY purchase_order_date;
        
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendors_from_purchase_order` (IN `p_product_id` INT)   BEGIN
    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        DECLARE error_code VARCHAR(5);
        DECLARE error_message VARCHAR(255);

        -- Get the error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Main Query Block
    IF EXISTS (
        SELECT 1 
        FROM inv_purchase_order_items po
        WHERE po.product_id = p_product_id
        AND po.purchase_order_item_status = 1
    ) THEN
        -- Fetch latest purchase order details by product_id if records exist
        WITH LatestPurchaseOrder AS (
            SELECT 
                po.vendor_id, 
                po.unit_price, 
                po.unit_of_measure, 
                po.date,
                ROW_NUMBER() OVER (PARTITION BY po.vendor_id ORDER BY po.date DESC) AS row_num
            FROM inv_purchase_order_items po
            WHERE po.product_id = p_product_id
            AND po.purchase_order_item_status = 1 -- Only purchased items
        )
        SELECT v.vendor_id, 
               v.vendor_company_name, 
               lpo.unit_price, 
               lpo.unit_of_measure, 
               lpo.date
        FROM inv_vendors v
        JOIN LatestPurchaseOrder lpo
        ON v.vendor_id = lpo.vendor_id
        WHERE lpo.row_num = 1 -- Select only the latest record for each vendor
        AND v.vendor_status = 1 -- Only active vendors
        AND v.deleted = 0;

    ELSE
        -- If no purchase orders exist, fetch unit_price and unit_of_measure from inv_products
        SELECT v.vendor_id, 
               v.vendor_company_name,
               p.unit_price,
               p.unit_of_measure,
               NULL AS date
        FROM inv_vendors v
        CROSS JOIN inv_products p
        WHERE p.product_id = p_product_id
        AND v.vendor_status = 1 -- Only active vendors
        AND v.deleted = 0;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendor_card_details` ()   BEGIN
    DECLARE total_vendors INT;
    DECLARE total_active_vendors INT;
    DECLARE total_inactive_vendors INT;

    -- Fetch the total number of vendors
    SELECT COUNT(*) INTO total_vendors FROM inv_vendors WHERE deleted = 0;

    -- Fetch the total number of active vendors
    SELECT COUNT(*) INTO total_active_vendors FROM inv_vendors WHERE vendor_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive vendors
    SELECT COUNT(*) INTO total_inactive_vendors FROM inv_vendors WHERE vendor_status = 0 AND deleted = 0;

    -- Return the results
    SELECT 
        total_vendors AS TotalVendors,
        total_active_vendors AS TotalActiveVendors,
        total_inactive_vendors AS TotalInactiveVendors;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendor_details` (IN `p_vendor_id` INT)   BEGIN
    SELECT 
        vendor_id,
        vendor_code,
        salutation,
        vendor_company_name,
        vendor_contact_name,
        vendor_phone_number,
        vendor_email_id,
        billing_address_street,
        billing_address_locality,
        billing_address_city,
        billing_address_district,
        billing_address_state,
        billing_address_pincode,
        billing_address_country,
        shipping_address_street,
        shipping_address_locality,
        shipping_address_city,
        shipping_address_district,
        shipping_address_state,
        shipping_address_country,
        shipping_address_pincode,
        vendor_gstin,
        vendor_pan_number,
        vendor_bank_name,
        vendor_account_number,
        vendor_ifsc_code,
        vendor_branch_name,
        created_on,
        updated_on,
        created_by,
        vendor_status
    FROM inv_vendors
    WHERE vendor_id = p_vendor_id
    AND deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendor_invoice_card_details` ()   BEGIN
    DECLARE total_vendor_invoices INT;
    DECLARE total_active_vendor_invoices INT;
    DECLARE total_inactive_vendor_invoices INT;

    -- Fetch the total number of vendor invoices
    SELECT COUNT(*) INTO total_vendor_invoices FROM inv_vendor_invoices WHERE deleted = 0;

    -- Fetch the total number of active vendor invoices
    SELECT COUNT(*) INTO total_active_vendor_invoices FROM inv_vendor_invoices WHERE invoice_status = 1 AND deleted = 0;

    -- Fetch the total number of inactive vendor invoices
    SELECT COUNT(*) INTO total_inactive_vendor_invoices FROM inv_vendor_invoices WHERE invoice_status = 0 AND deleted = 0;

    -- Return the results
    SELECT 
        total_vendor_invoices AS TotalVendorInvoice,
        total_active_vendor_invoices AS TotalActiveVendorInvoice,
        total_inactive_vendor_invoices AS TotalInactiveVendorInvoice;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendor_invoice_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_vendor_invoices 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_vendor_invoices v
        JOIN inv_vendors ve ON v.vendor_id = ve.vendor_id
        WHERE (v.vendor_invoice_number LIKE ? 
        OR ve.vendor_company_name LIKE ?)
        AND v.deleted = 0';
    
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search;
    DEALLOCATE PREPARE stmt;

    -- Fetch the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT v.vendor_invoice_id, v.vendor_invoice_number, DATE_FORMAT(v.invoice_date, "%d-%m-%Y") AS invoice_date, 
        ve.vendor_company_name, v.grand_total, v.invoice_status
        FROM inv_vendor_invoices v
        JOIN inv_vendors ve ON v.vendor_id = ve.vendor_id
        WHERE (v.vendor_invoice_number LIKE ? 
        OR ve.vendor_company_name LIKE ?)
        AND v.deleted = 0 
        ORDER BY v.invoice_status = 0 DESC, v.invoice_date DESC, ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?');

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, p_start, p_length;
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_vendor_table_data` (IN `p_start` INT, IN `p_length` INT, IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4))   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM inv_vendors 
    WHERE deleted = 0;

    -- Get total number of filtered records
    SET @query = '
        SELECT COUNT(*) 
        FROM inv_vendors 
        WHERE (vendor_code LIKE ? 
        OR vendor_company_name LIKE ? 
        OR vendor_contact_name LIKE ? 
        OR vendor_phone_number LIKE ?)
        AND deleted = 0 ';
    PREPARE stmt FROM @query;
    SET @search = CONCAT('%', p_search_value, '%');
    EXECUTE stmt USING @search, @search, @search, @search;
    DEALLOCATE PREPARE stmt;

    -- Use FOUND_ROWS() to get the number of filtered records
    SELECT FOUND_ROWS() INTO filtered_records;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT vendor_id, vendor_code, vendor_company_name, vendor_contact_name, 
        vendor_phone_number,vendor_status
        FROM inv_vendors 
        WHERE (vendor_code LIKE ? 
        OR vendor_company_name LIKE ? 
        OR vendor_contact_name LIKE ? 
        OR vendor_phone_number LIKE ?)
        AND deleted = 0 
        ORDER BY ', p_sort_column, ' ', p_order_dir, ' 
        LIMIT ?, ?'); -- Adjusted for pagination

    PREPARE data_stmt FROM @data_query;
    EXECUTE data_stmt USING @search, @search, @search, @search, p_start, p_length; -- Added p_start and p_length for pagination
    DEALLOCATE PREPARE data_stmt;

    -- Return total records and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_customer_details` (IN `p_customer_id` INT)   BEGIN
    -- Fetch customer details
    SELECT customer_id, customer_code, salutation, customer_name, customer_phone_number, customer_email_id,
           address_street, address_locality, address_city, address_state, address_pincode, customer_gstin,
           created_on, updated_on, created_by, customer_status
    FROM inv_customer
    WHERE customer_id = p_customer_id AND deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_inventory_history_details` (IN `p_inventory_id` INT, IN `p_period` VARCHAR(10))   BEGIN

-- Fetch product details
    SELECT 
        p.product_name, 
        p.product_code, 
        i.unit_of_measure, 
        i.quantity_in_stock
    FROM 
        inv_inventory i
    JOIN 
        inv_products p ON i.product_id = p.product_id
    WHERE 
        i.inventory_id = p_inventory_id;

    -- Fetch inventory history
    SELECT 
        inventory_id, 
        product_id, 
        unit_of_measure, 
        quantity, 
        inventory_history_status, 
        created_on
    FROM 
        inv_inventory_history
    WHERE 
        inventory_id = p_inventory_id;

    SET @sql = '';

    -- Determine the SQL query based on the period
    IF p_period = 'daily' THEN
        SET @sql = CONCAT(
            'SELECT DAYNAME(created_on) AS day, ',
            'SUM(CASE WHEN inventory_history_status = 1 THEN quantity ELSE 0 END) AS stock_out, ',
            'SUM(CASE WHEN inventory_history_status = 0 THEN quantity ELSE 0 END) AS stock_in ',
            'FROM inv_inventory_history ',
            'WHERE inventory_id = ', p_inventory_id, ' ',
            'AND created_on >= CURDATE() - INTERVAL 7 DAY ',
            'GROUP BY DAYOFWEEK(created_on)'
        );
    ELSEIF p_period = 'monthly' THEN
        SET @sql = CONCAT(
            'SELECT DATE_FORMAT(created_on, "%Y-%m") AS month, ',
            'SUM(CASE WHEN inventory_history_status = 1 THEN quantity ELSE 0 END) AS stock_out, ',
            'SUM(CASE WHEN inventory_history_status = 0 THEN quantity ELSE 0 END) AS stock_in ',
            'FROM inv_inventory_history ',
            'WHERE inventory_id = ', p_inventory_id, ' ',
            'AND created_on >= CURDATE() - INTERVAL 1 YEAR ',
            'GROUP BY DATE_FORMAT(created_on, "%Y-%m")'
        );
    ELSEIF p_period = 'yearly' THEN
        SET @sql = CONCAT(
            'SELECT DATE_FORMAT(created_on, "%Y") AS year, ',
            'SUM(CASE WHEN inventory_history_status = 1 THEN quantity ELSE 0 END) AS stock_out, ',
            'SUM(CASE WHEN inventory_history_status = 0 THEN quantity ELSE 0 END) AS stock_in ',
            'FROM inv_inventory_history ',
            'WHERE inventory_id = ', p_inventory_id, ' ',
            'AND created_on >= CURDATE() - INTERVAL 7 YEAR ',
            'GROUP BY DATE_FORMAT(created_on, "%Y")'
        );
    END IF;

    -- Execute the SQL query for stock usage data
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_invoice_details` (IN `p_invoice_id` INT)   BEGIN
    -- Fetch invoice details along with customer details and address
    SELECT 
        i.invoice_id,
        i.invoice_number,
        i.customer_id,
        c.customer_name,
        c.customer_phone_number,
        c.customer_email_id,
        c.address_street,
        c.address_locality,
        c.address_district,
        c.address_city,
        c.address_state,
        c.address_pincode,
        c.address_country,
        c.customer_gstin,
        i.invoice_date,
        i.invoice_due_date,
        i.subtotal,
        i.gst,
        i.sgst,
        i.cgst,
        i.igst,
        i.total_gst_amount,
        i.adjustments,
        i.grand_total,
        i.total_amount,
        i.amount_in_words,
        i.payment_mode,
        i.invoice_status,
        i.created_on,
        i.updated_on
    FROM inv_invoices i
    JOIN inv_customer c ON i.customer_id = c.customer_id
    WHERE i.invoice_id = p_invoice_id AND i.deleted = 0;

    -- Fetch invoice items
    SELECT 
        ii.invoice_item_id,
        ii.product_id,
        p.product_name,
        p.unit_of_measure AS product_unit_of_measure,
        p.unit_price AS product_price,
        p.tax_percentage,
        ii.unit_of_measure,
        ii.quantity,
        ii.unit_price,
        ii.amount,
        ii.tax_inclusive_enable,
        ii.discount_enable,
        ii.discount_rate,
        ii.discount_amount,
        ii.gst_amount
    FROM inv_invoice_items ii
    JOIN inv_products p ON ii.product_id = p.product_id
    WHERE ii.invoice_id = p_invoice_id AND ii.deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_price_history_data` (IN `p_product_id` INT, IN `p_price_period` VARCHAR(10))   BEGIN
    DECLARE date_threshold DATE;

    -- Determine the date threshold based on the price_period
    SET date_threshold = CASE
        WHEN p_price_period = 'daily' THEN DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        WHEN p_price_period = 'monthly' THEN DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        WHEN p_price_period = 'yearly' THEN DATE_SUB(CURDATE(), INTERVAL 7 YEAR)
        ELSE '1900-01-01'
    END;

    -- Fetch product details
    SELECT 
        product_name, 
        product_code, 
        unit_of_measure 
    FROM inv_products 
    WHERE product_id = p_product_id;

    -- Fetch price history data aggregated by vendor
    SELECT 
        iv.vendor_id,
        iv.vendor_company_name,
        ipo.purchase_order_item_id,
        ipo.purchase_order_id,
        po.purchase_order_number,
        ipo.unit_price,
        ipo.unit_of_measure,
        ipo.date
    FROM inv_vendors iv
    JOIN inv_purchase_order_items ipo ON iv.vendor_id = ipo.vendor_id
    JOIN inv_purchase_orders po ON ipo.purchase_order_id = po.purchase_order_id
    WHERE ipo.product_id = p_product_id
      AND ipo.date >= date_threshold
      AND ipo.deleted = 0
      AND ipo.purchase_order_item_status = 1
    ORDER BY ipo.date DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_product_details` (IN `p_product_id` INT)   BEGIN
    -- Declare variables for product details
    DECLARE product_name VARCHAR(255);
    DECLARE hsn_code VARCHAR(10);
    DECLARE product_code VARCHAR(50);
    DECLARE product_category VARCHAR(100);
    DECLARE product_type INT;
    DECLARE unit_of_measure VARCHAR(50);
    DECLARE bottom_stock INT;
    DECLARE order_quantity INT;
    DECLARE unit_price DECIMAL(10,2);
    DECLARE pricing_type INT;
    DECLARE tax_percentage DECIMAL(10,2);
    DECLARE prouct_terms_and_conditions TEXT;
    DECLARE created_on TIMESTAMP;
    DECLARE updated_on TIMESTAMP;
    DECLARE created_by INT;
    DECLARE product_status INT;
    DECLARE discountable INT;

    -- Fetch product details
    SELECT 
        ip.product_name,
        ip.hsn_code,
        ip.product_code,
        ip.product_category,
        ip.product_type,
        ip.unit_of_measure,
        ip.bottom_stock,
        ip.order_quantity,
        ip.unit_price,
        ip.pricing_type,
        ip.discountable,
        ip.tax_percentage,
        ip.prouct_terms_and_conditions,
        ip.created_on,
        ip.updated_on,
        ip.created_by,
        ip.product_status
    INTO
        product_name,
        hsn_code,
        product_code,
        product_category,
        product_type,
        unit_of_measure,
        bottom_stock,
        order_quantity,
        unit_price,
        pricing_type,
        discountable,
        tax_percentage,
        prouct_terms_and_conditions,
        created_on,
        updated_on,
        created_by,
        product_status
    FROM inv_products ip
    WHERE ip.product_id = p_product_id;

    -- Return product details
    SELECT 
        product_name, hsn_code, product_code, product_category,
        product_type, unit_of_measure, bottom_stock, order_quantity,
        unit_price, pricing_type, tax_percentage, prouct_terms_and_conditions,
        created_on, updated_on, created_by, product_status, discountable;

    -- Return item details
    SELECT 
        pii.quantity_used AS item_quantity_used,
        pii.unit_of_measure AS item_unit_of_measure,
        pii.item_id AS item_id,
        pii.used_product_id AS used_product_id,
        ip.product_name AS item_product_name,
        ip.product_code AS item_product_code
    FROM inv_product_items pii
    LEFT JOIN inv_products ip ON pii.used_product_id = ip.product_id
    WHERE pii.product_id = p_product_id AND pii.deleted=0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_sales_report_data` (IN `p_product_id` INT, IN `p_sales_period` VARCHAR(10))   BEGIN
    DECLARE date_threshold DATE;

    -- Determine the date threshold based on the sales_period
    SET date_threshold = CASE
        WHEN p_sales_period = 'daily' THEN DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        WHEN p_sales_period = 'monthly' THEN DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        WHEN p_sales_period = 'yearly' THEN DATE_SUB(CURDATE(), INTERVAL 7 YEAR)
        ELSE '1900-01-01'
    END;

    -- Fetch product details
    SELECT 
    	product_id,
        product_name, 
        product_code, 
        unit_of_measure 
    FROM inv_products 
    WHERE product_id = p_product_id;

    -- Fetch sales history data aggregated by invoice
    SELECT 
        ii.invoice_item_id,
        ii.invoice_id,
        i.invoice_number,
        ii.unit_price,
        ii.unit_of_measure,
        ii.quantity,
        ii.amount,
        i.invoice_date,
        c.customer_id,
        c.customer_name
    FROM inv_invoice_items ii
    JOIN inv_invoices i ON ii.invoice_id = i.invoice_id
    JOIN inv_customer c ON i.customer_id = c.customer_id
    WHERE ii.product_id = p_product_id
      AND i.invoice_date >= date_threshold
      AND ii.deleted = 0
      AND ii.invoice_items_status = 1 -- Assuming 1 indicates a delivered item
    ORDER BY i.invoice_date DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_view_vendor_invoice_details` (IN `p_invoice_id` INT)   BEGIN
    -- Fetch vendor invoice details along with vendor details
    SELECT 
        i.vendor_invoice_id,
        i.vendor_invoice_number,
        v.vendor_contact_name,
        v.vendor_id,
        v.vendor_company_name,
        v.vendor_phone_number,
        v.vendor_email_id,
        v.billing_address_street,
        v.billing_address_locality,
        v.billing_address_district,
        v.billing_address_city,
        v.billing_address_state,
        v.billing_address_pincode,
        v.billing_address_country,
        v.shipping_address_street,
        v.shipping_address_locality,
        v.shipping_address_district,
        v.shipping_address_city,
        v.shipping_address_state,
        v.shipping_address_pincode,
        v.shipping_address_country,
        v.vendor_gstin,
        i.invoice_date,
        i.invoice_due_date,
        i.subtotal,
        i.gst,
        i.sgst,
        i.cgst,
        i.igst,
        i.total_gst_amount,
        i.adjustments,
        i.shipping_charges,
        i.handling_fees,
        i.storage_fees,
        i.grand_total,
        i.total_amount,
        i.amount_in_words,
        i.payment_mode,
        i.invoice_status,
        i.created_on,
        i.updated_on
    FROM inv_vendor_invoices i
    JOIN inv_vendors v ON i.vendor_id = v.vendor_id
    WHERE i.vendor_invoice_id = p_invoice_id 
      AND i.deleted = 0;

    -- Fetch vendor invoice items
    SELECT 
        ii.vendor_invoice_item_id,
        ii.product_id,
        p.product_name,
        p.unit_of_measure AS product_unit_of_measure,
        p.unit_price AS product_price,
        p.tax_percentage,
        ii.unit_of_measure,
        ii.quantity,
        ii.unit_price,
        ii.amount,
        ii.tax_inclusive_enable,
        ii.discount_enable,
        ii.discount_rate,
        ii.discount_amount,
        ii.gst_amount
    FROM inv_vendor_invoice_items ii
    JOIN inv_products p ON ii.product_id = p.product_id
    WHERE ii.vendor_invoice_id = p_invoice_id 
      AND ii.deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFirmProfileLogo` ()   BEGIN
    -- Fetch firm profile logo
    SELECT 
        logo
    FROM inv_firm_profile
    WHERE company_id = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_customers_by_name` (IN `search_term` VARCHAR(255))   BEGIN
    SELECT
        customer_id,
        customer_code,
        customer_name,
        address_state
    FROM
        inv_customer
    WHERE
        customer_name LIKE CONCAT('%', search_term, '%')
        AND customer_status = 1
        AND deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_products_by_name` (IN `search_term` VARCHAR(255))   BEGIN
    SELECT
        product_id,
        product_name,
        product_code,
        unit_of_measure
    FROM
        inv_products
    WHERE
        product_name LIKE CONCAT('%', search_term, '%') AND product_status=1 and deleted=0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_product_details_invoice_with_id` (IN `input_product_id` INT)   BEGIN
    SELECT
        p.product_name,
        p.hsn_code,
        p.unit_price,
        p.pricing_type,
        p.tax_percentage,
        p.unit_of_measure AS product_unit_of_measure,
        p.discountable,
        p.prouct_terms_and_conditions,
        i.quantity_in_stock AS inventory_quantity_in_stock,
        i.unit_of_measure AS inventory_unit_of_measure
    FROM
        inv_products p
    LEFT JOIN
        inv_inventory i ON p.product_id = i.product_id
    WHERE
        p.product_id = input_product_id
        AND p.product_status = 1
        AND p.deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_vendors_by_name` (IN `search_term` VARCHAR(255))   BEGIN
    SELECT
        vendor_id,
        vendor_code,
        vendor_company_name,
        billing_address_state
    FROM
        inv_vendors
    WHERE
        vendor_company_name LIKE CONCAT('%', search_term, '%')
        AND vendor_status = 1
        AND deleted = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_customer` (IN `p_salutation` VARCHAR(10), IN `p_customer_name` VARCHAR(255), IN `p_contact_number` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_gstin` VARCHAR(20), IN `p_street` VARCHAR(255), IN `p_locality` VARCHAR(50), IN `p_pincode` VARCHAR(10), IN `p_city` VARCHAR(50), IN `p_district` VARCHAR(50), IN `p_state` VARCHAR(50), IN `p_country` VARCHAR(50), IN `p_created_by` INT)   BEGIN
    DECLARE next_code INT;
    DECLARE formatted_code VARCHAR(50);

    -- Find the highest customer_code number
    SELECT COALESCE(MAX(CAST(SUBSTRING(customer_code, 5) AS UNSIGNED)), 0) INTO next_code
    FROM inv_customer;

    -- Increment the code
    SET next_code = next_code + 1;

    -- Format the code to 'CUS-XXX'
    SET formatted_code = CONCAT('CUS-', LPAD(next_code, 3, '0'));

    -- Insert the new customer record with the generated customer_code
    INSERT INTO `inv_customer` (
        `customer_code`,
        `salutation`,
        `customer_name`,
        `customer_phone_number`,
        `customer_email_id`,
        `customer_gstin`,
        `address_street`,
        `address_locality`,
        `address_pincode`,
        `address_city`,
        `address_district`,
        `address_state`,
        `address_country`,
        `created_by`
    ) VALUES (
        formatted_code,
        p_salutation,
        p_customer_name,
        p_contact_number,
        p_email,
        p_gstin,
        p_street,
        p_locality,
        p_pincode,
        p_city,
        p_district,
        p_state,
        p_country,
        p_created_by
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_delivery_challan` (IN `p_customer_id` INT, IN `p_challan_number` VARCHAR(50), IN `p_user_id` INT, IN `p_items_count` INT, IN `p_item_ids_json` TEXT, IN `p_units_of_measure_json` TEXT, IN `p_quantities_json` TEXT, IN `p_delivery_date` DATE)   BEGIN
    DECLARE p_delivery_challan_id INT;
    DECLARE idx INT DEFAULT 0;
    
    -- Insert into the delivery challan table
    INSERT INTO inv_delivery_challan (
        delivery_challan_number, 
        customer_id, 
        delivery_challan_date, 
        created_by, 
        delivery_date
    ) VALUES (
        p_challan_number, 
        p_customer_id, 
        NOW(), 
        p_user_id, 
        p_delivery_date
    );
    
    -- Get the last inserted delivery challan ID
    SET p_delivery_challan_id = LAST_INSERT_ID();
    
    -- Loop through the JSON arrays to insert items into the delivery challan items table
    WHILE idx < p_items_count DO
        INSERT INTO inv_delivery_challan_items (
            delivery_challan_id, 
            customer_id, 
            product_id, 
            unit_of_measure, 
            quantity, 
            date
        ) VALUES (
            p_delivery_challan_id, 
            p_customer_id, 
            JSON_UNQUOTE(JSON_EXTRACT(p_item_ids_json, CONCAT('$[', idx, ']'))), 
            JSON_UNQUOTE(JSON_EXTRACT(p_units_of_measure_json, CONCAT('$[', idx, ']'))), 
            JSON_UNQUOTE(JSON_EXTRACT(p_quantities_json, CONCAT('$[', idx, ']'))), 
            NOW()
        );
        SET idx = idx + 1;
    END WHILE;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_guest_room_registration` (IN `p_reservation_no` VARCHAR(225), IN `p_name` VARCHAR(255), IN `p_surname` VARCHAR(255), IN `p_first_name` VARCHAR(255), IN `p_birthday` DATE, IN `p_anniversary` DATE, IN `p_nationality` VARCHAR(255), IN `p_employed_in_india` BOOLEAN, IN `p_mobile_no` VARCHAR(20), IN `p_email_id` VARCHAR(255), IN `p_phone` VARCHAR(20), IN `p_address_street` TEXT, IN `p_address_locality` TEXT, IN `p_address_city` VARCHAR(100), IN `p_address_district` VARCHAR(100), IN `p_address_state` VARCHAR(100), IN `p_adults` INT, IN `p_children` INT, IN `p_extra_bed` BOOLEAN, IN `p_designation` VARCHAR(100), IN `p_voucher_no` VARCHAR(50), IN `p_gstin` VARCHAR(50), IN `p_purpose_of_visit` TEXT, IN `p_proceeding_to` VARCHAR(100), IN `p_arrival_date_time` DATETIME, IN `p_departure_date_time` DATETIME, IN `p_mode_of_payment` VARCHAR(50), IN `p_card_no` VARCHAR(50), IN `p_card_expiry_date` DATE, IN `p_passport_no` VARCHAR(50), IN `p_place_of_issue` VARCHAR(100), IN `p_date_of_issue` DATE, IN `p_passport_expiry` DATE, IN `p_visa_type` VARCHAR(100), IN `p_visa_expiry` DATE)   BEGIN
    DECLARE record_type INT;

    -- Check if the reservation already exists
    SELECT COUNT(id) INTO record_type
    FROM guest_room_registration
    WHERE reservation_no = p_reservation_no;

    -- Determine the record type based on the existence of the reservation
    IF record_type > 0 THEN
        SET record_type = 1;
    ELSE
        SET record_type = 0;
    END IF;

    -- Insert the record into the guest_room_registration table
    INSERT INTO guest_room_registration (
        reservation_no,
        name,
        surname,
        first_name,
        birthday,
        anniversary,
        nationality,
        employed_in_india,
        mobile_no,
        email_id,
        phone,
        address_street,
        address_locality,
        address_city,
        address_district,
        address_state,
        adults,
        children,
        extra_bed,
        designation,
        voucher_no,
        gstin,
        purpose_of_visit,
        proceeding_to,
        arrival_date_time,
        departure_date_time,
        mode_of_payment,
        card_no,
        card_expiry_date,
        passport_no,
        place_of_issue,
        date_of_issue,
        passport_expiry,
        visa_type,
        visa_expiry,
        record_type
    ) VALUES (
        p_reservation_no,
        p_name,
        p_surname,
        p_first_name,
        p_birthday,
        p_anniversary,
        p_nationality,
        p_employed_in_india,
        p_mobile_no,
        p_email_id,
        p_phone,
        p_address_street,
        p_address_locality,
        p_address_city,
        p_address_district,
        p_address_state,
        p_adults,
        p_children,
        p_extra_bed,
        p_designation,
        p_voucher_no,
        p_gstin,
        p_purpose_of_visit,
        p_proceeding_to,
        p_arrival_date_time,
        p_departure_date_time,
        p_mode_of_payment,
        p_card_no,
        p_card_expiry_date,
        p_passport_no,
        p_place_of_issue,
        p_date_of_issue,
        p_passport_expiry,
        p_visa_type,
        p_visa_expiry,
        record_type
    );

    -- Output success message (can be removed or adjusted as needed)
    SELECT 'Success' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_inventory_item` (IN `p_product_id` INT, IN `p_quantity_added` DECIMAL(15,3), IN `p_converted_quantity` DECIMAL(15,3), IN `p_unit_of_quantity` VARCHAR(20), IN `p_product_unit_of_measure` VARCHAR(20), IN `p_created_by` INT)   BEGIN
    DECLARE v_inventory_id INT;

    -- Check if the inventory record already exists for the given product_id
    SELECT inventory_id INTO v_inventory_id
    FROM inv_inventory
    WHERE product_id = p_product_id;

    IF v_inventory_id IS NOT NULL THEN
        -- If inventory record exists, update the quantity in stock
        UPDATE inv_inventory
        SET quantity_in_stock = quantity_in_stock + p_converted_quantity,
            unit_of_measure = p_product_unit_of_measure
        WHERE inventory_id = v_inventory_id;
    ELSE
        -- If inventory record does not exist, insert a new record
        INSERT INTO inv_inventory (product_id, unit_of_measure, quantity_in_stock, inventory_status)
        VALUES (p_product_id, p_product_unit_of_measure, p_converted_quantity, 1);
        
        -- Get the last inserted inventory_id
        SET v_inventory_id = LAST_INSERT_ID();
    END IF;

    -- Insert the transaction into inv_inventory_history
    INSERT INTO inv_inventory_history (inventory_id, product_id, unit_of_measure, quantity, inventory_history_status, created_by)
    VALUES (v_inventory_id, p_product_id, p_product_unit_of_measure, p_converted_quantity, 0, p_created_by);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_invoice_with_items` (IN `p_customer_id` INT, IN `p_invoice_number` VARCHAR(50), IN `p_invoice_date` DATE, IN `p_due_date` DATE, IN `p_subtotal` DECIMAL(15,2), IN `p_adjustments` DECIMAL(15,2), IN `p_grand_total` DECIMAL(15,2), IN `p_total_gst_amount` DECIMAL(15,2), IN `p_amount_in_words` VARCHAR(255), IN `p_gst` TINYINT(1), IN `p_sgst` DECIMAL(15,2), IN `p_cgst` DECIMAL(15,2), IN `p_igst` DECIMAL(15,2), IN `p_payment_mode` VARCHAR(50), IN `p_created_by` INT, IN `p_items_count` INT, IN `p_item_ids_json` JSON, IN `p_units_of_measure_json` JSON, IN `p_quantities_json` JSON, IN `p_rates_json` JSON, IN `p_amounts_json` JSON, IN `p_discount_enable_json` JSON, IN `p_discount_rate_json` JSON, IN `p_discount_amount_json` JSON, IN `p_item_gst_amount_json` JSON, IN `p_tax_inclusive_enable_json` JSON, IN `p_tax_percentage_json` JSON, IN `p_total_value` DECIMAL(15,2), IN `p_invoice_status` INT, IN `p_converted_quantities_json` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_item_id INT;
    DECLARE v_unit_of_measure VARCHAR(50);
    DECLARE v_quantity INT;
    DECLARE v_rate DECIMAL(15,2);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_discount_enable TINYINT(1);
    DECLARE v_discount_rate DECIMAL(5,2);
    DECLARE v_discount_amount DECIMAL(15,2);
    DECLARE v_item_gst_amount DECIMAL(15,2); -- New variable for item GST amount
    DECLARE v_tax_inclusive_enable TINYINT(1);
    DECLARE v_tax_percentage DECIMAL(5,2);
    DECLARE v_converted_quantity DECIMAL(15,3);

    -- Insert the invoice data
    INSERT INTO inv_invoices(
        customer_id, invoice_number, invoice_date, invoice_due_date, subtotal, adjustments, 
        grand_total, total_amount, total_gst_amount, amount_in_words, gst, sgst, cgst, igst, payment_mode, invoice_status, created_by
    )
    VALUES (
        p_customer_id, p_invoice_number, p_invoice_date, p_due_date, p_subtotal, p_adjustments, 
        p_grand_total, p_total_value, p_total_gst_amount, p_amount_in_words, p_gst, p_sgst, p_cgst, p_igst, p_payment_mode, p_invoice_status, p_created_by
    );

    SET @invoice_id = LAST_INSERT_ID();

    -- Loop through the items
    WHILE i < p_items_count DO
        SET v_item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids_json, CONCAT('$[', i, ']')));
        SET v_unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_units_of_measure_json, CONCAT('$[', i, ']')));
        SET v_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_quantities_json, CONCAT('$[', i, ']')));
        SET v_rate = JSON_UNQUOTE(JSON_EXTRACT(p_rates_json, CONCAT('$[', i, ']')));
        SET v_amount = JSON_UNQUOTE(JSON_EXTRACT(p_amounts_json, CONCAT('$[', i, ']')));
        SET v_discount_enable = JSON_UNQUOTE(JSON_EXTRACT(p_discount_enable_json, CONCAT('$[', i, ']')));
        SET v_discount_rate = JSON_UNQUOTE(JSON_EXTRACT(p_discount_rate_json, CONCAT('$[', i, ']')));
        SET v_discount_amount = JSON_UNQUOTE(JSON_EXTRACT(p_discount_amount_json, CONCAT('$[', i, ']')));
        SET v_item_gst_amount = JSON_UNQUOTE(JSON_EXTRACT(p_item_gst_amount_json, CONCAT('$[', i, ']'))); -- Extract item GST amount
        SET v_tax_inclusive_enable = JSON_UNQUOTE(JSON_EXTRACT(p_tax_inclusive_enable_json, CONCAT('$[', i, ']')));
        SET v_tax_percentage = JSON_UNQUOTE(JSON_EXTRACT(p_tax_percentage_json, CONCAT('$[', i, ']')));
        SET v_converted_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_converted_quantities_json, CONCAT('$[', i, ']')));

        -- Insert the item into the invoice items table
        INSERT INTO inv_invoice_items(
            invoice_id, product_id, unit_of_measure, quantity, unit_price, amount, discount_rate, 
            discount_enable, discount_amount, gst_amount, tax_inclusive_enable, invoice_items_status
        )
        VALUES (
            @invoice_id, v_item_id, v_unit_of_measure, v_quantity, v_rate, v_amount, v_discount_rate, 
            v_discount_enable, v_discount_amount, v_item_gst_amount, v_tax_inclusive_enable, p_invoice_status
        );

        -- Update inventory if invoice status is 1
        IF p_invoice_status = 1 THEN
            CALL update_inventory(v_item_id, v_converted_quantity, v_quantity, v_unit_of_measure, 1, p_created_by);
        END IF;

        SET i = i + 1;
    END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_inv_product_with_items` (IN `p_product_name` VARCHAR(255), IN `p_hsn_code` VARCHAR(50), IN `p_product_category` VARCHAR(255), IN `p_product_type` INT, IN `p_unit_of_measure` VARCHAR(50), IN `p_bottom_stock` INT, IN `p_order_quantity` INT, IN `p_unit_price` DECIMAL(10,2), IN `p_pricing_type` INT, IN `p_discount_enable` INT, IN `p_tax_percentage` DECIMAL(5,2), IN `p_product_notes` TEXT, IN `p_created_by` INT, IN `p_item_unit_of_measure` JSON, IN `p_item_quantity_used` JSON, IN `p_item_code` JSON)   BEGIN
    DECLARE last_product_id INT;
    DECLARE i INT DEFAULT 0;
    DECLARE total_items INT;
    DECLARE new_product_code VARCHAR(10);
    DECLARE product_exists INT;

    -- Label for the entire procedure
    main_block: BEGIN

        -- Check if product name already exists
        SELECT COUNT(*)
        INTO product_exists
        FROM inv_products
        WHERE product_name = p_product_name;

        IF product_exists > 0 THEN
            SELECT 'Product already exists' AS status;
            LEAVE main_block;
        END IF;

        -- Fetch the last product code and increment it
        SELECT CONCAT('PR-', LPAD(CAST(SUBSTRING_INDEX(MAX(product_code), '-', -1) AS UNSIGNED) + 1, 3, '0'))
        INTO new_product_code
        FROM inv_products
        WHERE product_code LIKE 'PR-%';

        IF new_product_code IS NULL THEN
            SET new_product_code = 'PR-001';
        END IF;

        -- Insert into inv_products
        INSERT INTO inv_products (
            product_name,
            hsn_code,
            product_code,
            product_category,
            product_type,
            unit_of_measure,
            bottom_stock,
            order_quantity,
            unit_price,
            pricing_type,
            discountable,
            tax_percentage,
            prouct_terms_and_conditions,
            created_by,
            product_status,
            deleted
        ) VALUES (
            p_product_name,
            p_hsn_code,
            new_product_code,
            p_product_category,
            p_product_type,
            p_unit_of_measure,
            p_bottom_stock,
            p_order_quantity,
            p_unit_price,
            p_pricing_type,
            p_discount_enable,
            p_tax_percentage,
            p_product_notes,
            p_created_by,
            1,  -- default product_status
            0   -- default deleted status
        );

        SET last_product_id = LAST_INSERT_ID();

		INSERT INTO inv_inventory (product_id, unit_of_measure)
        VALUES (last_product_id, p_unit_of_measure)
        ON DUPLICATE KEY UPDATE
            unit_of_measure = p_unit_of_measure;
            
        -- Determine the number of items to insert
        SET total_items = JSON_LENGTH(p_item_unit_of_measure);

        -- Loop to insert each item
        WHILE i < total_items DO
            INSERT INTO inv_product_items (
                product_id,
                unit_of_measure,
                quantity_used,
                used_product_id
            ) VALUES (
                last_product_id,
                JSON_UNQUOTE(JSON_EXTRACT(p_item_unit_of_measure, CONCAT('$[', i, ']'))),
                JSON_UNQUOTE(JSON_EXTRACT(p_item_quantity_used, CONCAT('$[', i, ']'))),
                JSON_UNQUOTE(JSON_EXTRACT(p_item_code, CONCAT('$[', i, ']')))
            );
            SET i = i + 1;
        END WHILE;

        SELECT 'success' AS status;
    END main_block;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_purchase_order_with_items` (IN `p_vendor_id` INT, IN `p_purchase_order_number` VARCHAR(50), IN `p_subtotal` DECIMAL(10,2), IN `p_discount` DECIMAL(10,2), IN `p_adjustment` DECIMAL(10,2), IN `p_discount_amount` DECIMAL(10,2), IN `p_grand_total` DECIMAL(10,2), IN `p_amount_in_words` TEXT, IN `p_created_by` INT, IN `p_items_count` INT, IN `p_item_ids` JSON, IN `p_item_names` JSON, IN `p_item_uoms` JSON, IN `p_item_quantities` JSON, IN `p_item_rates` JSON, IN `p_item_amounts` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE item_id INT;
    DECLARE item_name VARCHAR(255);
    DECLARE item_uom VARCHAR(50);
    DECLARE item_quantity INT;
    DECLARE item_rate DECIMAL(10,2);
    DECLARE item_amount DECIMAL(10,2);

    -- Insert into inv_purchase_orders table
    INSERT INTO inv_purchase_orders (
        purchase_order_number,
        vendor_id,
        subtotal,
        discount,
        adjustment,
        discount_amount,
        grand_total,
        amount_in_words,
        created_by
    ) VALUES (
        p_purchase_order_number,
        p_vendor_id,
        p_subtotal,
        p_discount,
        p_adjustment,
        p_discount_amount,
        p_grand_total,
        p_amount_in_words,
        p_created_by
    );

    -- Get the last inserted purchase_order_id
    SET @purchase_order_id = LAST_INSERT_ID();

    -- Loop through each item and insert into inv_purchase_order_items table
    WHILE i < p_items_count DO
        SET item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids, CONCAT('$[', i, ']')));
        SET item_name = JSON_UNQUOTE(JSON_EXTRACT(p_item_names, CONCAT('$[', i, ']')));
        SET item_uom = JSON_UNQUOTE(JSON_EXTRACT(p_item_uoms, CONCAT('$[', i, ']')));
        SET item_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_item_quantities, CONCAT('$[', i, ']')));
        SET item_rate = JSON_UNQUOTE(JSON_EXTRACT(p_item_rates, CONCAT('$[', i, ']')));
        SET item_amount = JSON_UNQUOTE(JSON_EXTRACT(p_item_amounts, CONCAT('$[', i, ']')));

        INSERT INTO inv_purchase_order_items (
            purchase_order_id,
            vendor_id,
            product_id,
            unit_of_measure,
            quantity,
            unit_price,
            amount
        ) VALUES (
            @purchase_order_id,
            p_vendor_id,
            item_id,
            item_uom,
            item_quantity,
            item_rate,
            item_amount
        );

        SET i = i + 1;
    END WHILE;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_quotation_with_items` (IN `p_customer_id` INT, IN `p_quotation_number` VARCHAR(50), IN `p_date` TIMESTAMP, IN `p_subtotal` DECIMAL(10,2), IN `p_discount` DECIMAL(10,2), IN `p_adjustment` VARCHAR(10), IN `p_discount_amount` DECIMAL(10,2), IN `p_grand_total` DECIMAL(10,2), IN `p_amount_in_words` TEXT, IN `p_created_by` INT, IN `p_items_count` INT, IN `p_item_ids` JSON, IN `p_item_names` JSON, IN `p_item_uoms` JSON, IN `p_item_quantities` JSON, IN `p_item_rates` JSON, IN `p_item_amounts` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE item_id INT;
    DECLARE item_name VARCHAR(255);
    DECLARE item_uom VARCHAR(50);
    DECLARE item_quantity INT;
    DECLARE item_rate DECIMAL(10,2);
    DECLARE item_amount DECIMAL(10,2);

    -- Insert into inv_quotations table
    INSERT INTO inv_quotations (
        quotation_number,
        customer_id,
        quotation_date,
        subtotal,
        discount,
        adjustment,
        discount_amount,
        grand_total,
        amount_in_words,
        created_by
    ) VALUES (
        p_quotation_number,
        p_customer_id,
        p_date,
        p_subtotal,
        p_discount,
        p_adjustment,
        p_discount_amount,
        p_grand_total,
        p_amount_in_words,
        p_created_by
    );

    -- Get the last inserted quotation_id
    SET @quotation_id = LAST_INSERT_ID();

    -- Loop through each item and insert into inv_quotation_items table
    WHILE i < p_items_count DO
        SET item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids, CONCAT('$[', i, ']')));
        SET item_name = JSON_UNQUOTE(JSON_EXTRACT(p_item_names, CONCAT('$[', i, ']')));
        SET item_uom = JSON_UNQUOTE(JSON_EXTRACT(p_item_uoms, CONCAT('$[', i, ']')));
        SET item_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_item_quantities, CONCAT('$[', i, ']')));
        SET item_rate = JSON_UNQUOTE(JSON_EXTRACT(p_item_rates, CONCAT('$[', i, ']')));
        SET item_amount = JSON_UNQUOTE(JSON_EXTRACT(p_item_amounts, CONCAT('$[', i, ']')));

        INSERT INTO inv_quotation_items (
            quotation_id,
            customer_id,
            product_id,
            unit_of_measure,
            quantity,
            unit_price,
            amount
        ) VALUES (
            @quotation_id,
            p_customer_id,
            item_id,
            item_uom,
            item_quantity,
            item_rate,
            item_amount
        );

        SET i = i + 1;
    END WHILE;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_vendor` (IN `p_salutation` VARCHAR(10), IN `p_company_name` VARCHAR(100), IN `p_contact_name` VARCHAR(50), IN `p_contact_number` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_billing_street` VARCHAR(255), IN `p_billing_locality` VARCHAR(50), IN `p_billing_city` VARCHAR(50), IN `p_billing_district` VARCHAR(50), IN `p_billing_state` VARCHAR(50), IN `p_billing_pincode` VARCHAR(10), IN `p_billing_country` VARCHAR(50), IN `p_shipping_street` VARCHAR(255), IN `p_shipping_locality` VARCHAR(50), IN `p_shipping_city` VARCHAR(50), IN `p_shipping_district` VARCHAR(50), IN `p_shipping_state` VARCHAR(50), IN `p_shipping_pincode` VARCHAR(10), IN `p_shipping_country` VARCHAR(50), IN `p_gstin` VARCHAR(20), IN `p_pan` VARCHAR(20), IN `p_bank_name` VARCHAR(100), IN `p_account_number` VARCHAR(50), IN `p_ifsc_code` VARCHAR(20), IN `p_branch_name` VARCHAR(100), IN `p_created_by` INT(11))   BEGIN
    DECLARE v_last_vendor_code VARCHAR(50);
    DECLARE v_new_vendor_code VARCHAR(50);
    DECLARE v_numeric_code INT;

    -- Get the last vendor code
    SELECT vendor_code
    INTO v_last_vendor_code
    FROM inv_vendors
    ORDER BY vendor_id DESC
    LIMIT 1;

    IF v_last_vendor_code IS NULL THEN
        -- If no vendor exists, set the first vendor code
        SET v_new_vendor_code = 'V-001';
    ELSE
        -- Extract numeric part from the last vendor code
        SET v_numeric_code = CAST(SUBSTRING(v_last_vendor_code, 3) AS UNSIGNED) + 1;

        -- Generate the new vendor code with leading zeros
        SET v_new_vendor_code = CONCAT('V-', LPAD(v_numeric_code, 3, '0'));
    END IF;

    -- Insert new vendor
    INSERT INTO inv_vendors (
        vendor_code, salutation, vendor_company_name, vendor_contact_name, vendor_phone_number,
        vendor_email_id, billing_address_street, billing_address_locality, billing_address_city,
        billing_address_district, billing_address_state, billing_address_pincode, billing_address_country,
        shipping_address_street, shipping_address_locality, shipping_address_city,
        shipping_address_district, shipping_address_state, shipping_address_pincode, shipping_address_country,
        vendor_gstin, vendor_pan_number, vendor_bank_name, vendor_account_number, vendor_ifsc_code, vendor_branch_name, created_by
    ) VALUES (
        v_new_vendor_code, p_salutation, p_company_name, p_contact_name, p_contact_number,
        p_email, p_billing_street, p_billing_locality, p_billing_city, p_billing_district, p_billing_state, p_billing_pincode, p_billing_country,
        p_shipping_street, p_shipping_locality, p_shipping_city, p_shipping_district, p_shipping_state, p_shipping_pincode, p_shipping_country,
        p_gstin, p_pan, p_bank_name, p_account_number, p_ifsc_code, p_branch_name, p_created_by
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_vendor_invoice_with_items` (IN `p_vendor_id` INT, IN `p_vendor_invoice_number` VARCHAR(50), IN `p_invoice_date` DATE, IN `p_due_date` DATE, IN `p_subtotal` DECIMAL(15,2), IN `p_adjustments` DECIMAL(15,2), IN `p_grand_total` DECIMAL(15,2), IN `p_total_gst_amount` DECIMAL(15,2), IN `p_amount_in_words` VARCHAR(255), IN `p_gst` TINYINT(1), IN `p_sgst` DECIMAL(15,2), IN `p_cgst` DECIMAL(15,2), IN `p_igst` DECIMAL(15,2), IN `p_shipping_charges` DECIMAL(10,2), IN `p_handling_fees` DECIMAL(10,2), IN `p_storage_fees` DECIMAL(10,2), IN `p_payment_mode` VARCHAR(50), IN `p_created_by` INT, IN `p_items_count` INT, IN `p_item_ids_json` JSON, IN `p_units_of_measure_json` JSON, IN `p_quantities_json` JSON, IN `p_rates_json` JSON, IN `p_amounts_json` JSON, IN `p_discount_enable_json` JSON, IN `p_discount_rate_json` JSON, IN `p_discount_amount_json` JSON, IN `p_item_gst_amount_json` JSON, IN `p_tax_inclusive_enable_json` JSON, IN `p_tax_percentage_json` JSON, IN `p_total_value` DECIMAL(15,2), IN `p_invoice_status` INT, IN `p_converted_quantities_json` JSON)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_item_id INT;
    DECLARE v_unit_of_measure VARCHAR(50);
    DECLARE v_quantity INT;
    DECLARE v_rate DECIMAL(15,2);
    DECLARE v_amount DECIMAL(15,2);
    DECLARE v_discount_enable TINYINT(1);
    DECLARE v_discount_rate DECIMAL(5,2);
    DECLARE v_discount_amount DECIMAL(15,2);
    DECLARE v_item_gst_amount DECIMAL(15,2);
    DECLARE v_tax_inclusive_enable TINYINT(1);
    DECLARE v_tax_percentage DECIMAL(5,2);
    DECLARE v_converted_quantity DECIMAL(15,3);

    -- Insert the vendor invoice data
    INSERT INTO inv_vendor_invoices(
        vendor_invoice_number, vendor_id, invoice_date, invoice_due_date, subtotal, adjustments,
        shipping_charges, handling_fees, storage_fees, grand_total, total_amount, total_gst_amount,
        amount_in_words, gst, sgst, cgst, igst, payment_mode, invoice_status, created_by
    )
    VALUES (
        p_vendor_invoice_number, p_vendor_id, p_invoice_date, p_due_date, p_subtotal, p_adjustments,
        p_shipping_charges, p_handling_fees, p_storage_fees, p_grand_total, p_total_value, 
        p_total_gst_amount, p_amount_in_words, p_gst, p_sgst, p_cgst, p_igst, p_payment_mode, 
        p_invoice_status, p_created_by
    );

    SET @vendor_invoice_id = LAST_INSERT_ID();

    -- Loop through the items
    WHILE i < p_items_count DO
        SET v_item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids_json, CONCAT('$[', i, ']')));
        SET v_unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_units_of_measure_json, CONCAT('$[', i, ']')));
        SET v_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_quantities_json, CONCAT('$[', i, ']')));
        SET v_rate = JSON_UNQUOTE(JSON_EXTRACT(p_rates_json, CONCAT('$[', i, ']')));
        SET v_amount = JSON_UNQUOTE(JSON_EXTRACT(p_amounts_json, CONCAT('$[', i, ']')));
        SET v_discount_enable = JSON_UNQUOTE(JSON_EXTRACT(p_discount_enable_json, CONCAT('$[', i, ']')));
        SET v_discount_rate = JSON_UNQUOTE(JSON_EXTRACT(p_discount_rate_json, CONCAT('$[', i, ']')));
        SET v_discount_amount = JSON_UNQUOTE(JSON_EXTRACT(p_discount_amount_json, CONCAT('$[', i, ']')));
        SET v_item_gst_amount = JSON_UNQUOTE(JSON_EXTRACT(p_item_gst_amount_json, CONCAT('$[', i, ']')));
        SET v_tax_inclusive_enable = JSON_UNQUOTE(JSON_EXTRACT(p_tax_inclusive_enable_json, CONCAT('$[', i, ']')));
        SET v_tax_percentage = JSON_UNQUOTE(JSON_EXTRACT(p_tax_percentage_json, CONCAT('$[', i, ']')));
        SET v_converted_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_converted_quantities_json, CONCAT('$[', i, ']')));

        -- Insert the item into the vendor invoice items table
        INSERT INTO inv_vendor_invoice_items(
            vendor_invoice_id, product_id, unit_of_measure, quantity, unit_price, amount, discount_rate,
            discount_enable, discount_amount, gst_amount, tax_inclusive_enable, invoice_items_status
        )
        VALUES (
            @vendor_invoice_id, v_item_id, v_unit_of_measure, v_quantity, v_rate, v_amount, v_discount_rate,
            v_discount_enable, v_discount_amount, v_item_gst_amount, v_tax_inclusive_enable, p_invoice_status
        );

        SET i = i + 1;
    END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_pre_purchase_details` (IN `p_vendor_id` INT, IN `p_product_id` INT)   BEGIN
    SELECT 
        unit_price, 
        unit_of_measure, 
        date
    FROM 
        inv_purchase_order_items
    WHERE 
        vendor_id = p_vendor_id AND 
        product_id = p_product_id
    ORDER BY 
        date DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_pre_sales_details` (IN `p_product_id` INT)   BEGIN
    SELECT 
        unit_price, 
        unit_of_measure, 
        quantity, 
        amount  -- Removed the extra comma here
    FROM 
        inv_invoice_items
    WHERE 
        product_id = p_product_id AND 
        invoice_items_status = 1 AND  -- Corrected the column name to match the table definition
        deleted = 0  -- Assuming you want to exclude deleted items
    ORDER BY 
        invoice_item_id DESC  -- Assuming the most recent sales detail is desired
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `logout` (IN `p_id` INT)   BEGIN
    DECLARE v_rows_affected INT;
    DECLARE check_result INT;
    
    -- Update the logout_time and login_status to 'LoggedOut' for the provided id
    UPDATE inv_login_logs
    SET logout_time = CURRENT_TIMESTAMP,
        login_status = 0
    WHERE login_log_id = p_id;

    -- Get the number of rows affected by the update
    SELECT ROW_COUNT() INTO v_rows_affected;

    -- Set the check_result based on the number of rows affected
    IF v_rows_affected > 0 THEN
        SET check_result = 1; -- Success
    ELSE
        SET check_result = 0; -- Failure
    END IF;

    -- Return the check_result
    SELECT check_result AS result;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_customer` (IN `p_customer_id` INT, IN `p_salutation` VARCHAR(10), IN `p_customer_name` VARCHAR(255), IN `p_contact_number` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_gstin` VARCHAR(20), IN `p_street` VARCHAR(255), IN `p_locality` VARCHAR(50), IN `p_pincode` VARCHAR(10), IN `p_city` VARCHAR(50), IN `p_district` VARCHAR(50), IN `p_state` VARCHAR(50), IN `p_country` VARCHAR(50), IN `p_created_by` INT)   BEGIN
    -- Update the customer details
    UPDATE inv_customer
    SET salutation = p_salutation,
        customer_name = p_customer_name,
        customer_phone_number = p_contact_number,
        customer_email_id = p_email,
        customer_gstin = p_gstin,
        address_street = p_street,
        address_locality = p_locality,
        address_city = p_city,
        address_district = p_district,
        address_state = p_state,
        address_country = p_country,
        updated_on = NOW(), -- Update the timestamp to the current time
        created_by = p_created_by
    WHERE customer_id = p_customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_customer_status` (IN `p_customer_id` INT, IN `p_status` INT)   BEGIN
    -- Update the customer status in the inv_customer table
    UPDATE inv_customer
    SET customer_status = p_status
    WHERE customer_id = p_customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_inventory` (IN `p_product_id` INT, IN `p_converted_quantity` DECIMAL(15,3), IN `p_quantity` DECIMAL(15,3), IN `p_unit_of_measure` VARCHAR(20), IN `p_inventory_action` INT, IN `p_user_id` INT)   BEGIN
    DECLARE v_inventory_id INT;
    DECLARE v_quantity_in_stock DECIMAL(15,3);

    -- Find the inventory record for the product
    SELECT inventory_id, quantity_in_stock
    INTO v_inventory_id, v_quantity_in_stock
    FROM inv_inventory
    WHERE product_id = p_product_id
    LIMIT 1;

    -- If no record found, insert a new one
    IF v_inventory_id IS NULL THEN
        INSERT INTO inv_inventory (product_id, unit_of_measure, quantity_in_stock, inventory_status)
        VALUES (p_product_id, p_unit_of_measure, p_quantity, 1);
        SET v_inventory_id = LAST_INSERT_ID();
    ELSE
        -- Update the existing record based on the action
        IF p_inventory_action = 0 THEN
            -- Add stock
            UPDATE inv_inventory
            SET quantity_in_stock = v_quantity_in_stock + p_converted_quantity
            WHERE inventory_id = v_inventory_id;
        ELSEIF p_inventory_action = 1 THEN
            -- Subtract stock
            UPDATE inv_inventory
            SET quantity_in_stock = v_quantity_in_stock - p_converted_quantity
            WHERE inventory_id = v_inventory_id;
        END IF;
    END IF;

    -- Insert a new record into inv_inventory_history
    INSERT INTO inv_inventory_history (
        inventory_id,
        product_id,
        unit_of_measure,
        quantity,
        inventory_history_status,
        created_by
    )
    VALUES (
        v_inventory_id,
        p_product_id,
        p_unit_of_measure,
        p_quantity,
        p_inventory_action,
        p_user_id
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_inventory_item` (IN `p_inventory_id` INT, IN `p_product_id` INT, IN `p_quantity_used` DECIMAL(15,3), IN `p_converted_quantity` DECIMAL(15,3), IN `p_unit_of_measure` VARCHAR(20), IN `p_user_id` INT)   BEGIN
    DECLARE current_stock DECIMAL(15,3);
    DECLARE new_stock DECIMAL(15,3);

    -- Get the current stock for the product
    SELECT quantity_in_stock INTO current_stock
    FROM inv_inventory
    WHERE inventory_id = p_inventory_id
    AND product_id = p_product_id;

    -- Calculate the new stock after subtraction
    SET new_stock = current_stock - p_converted_quantity;

    -- Check if the new stock would be negative
    IF new_stock < 0 THEN
        -- If yes, return an error
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Not enough stock to complete the operation.';

    ELSE
        -- If no, update the inventory
        UPDATE inv_inventory
        SET quantity_in_stock = new_stock
        WHERE inventory_id = p_inventory_id
        AND product_id = p_product_id;

        -- Insert a record into the inventory history
        INSERT INTO inv_inventory_history (
            inventory_id,
            product_id,
            unit_of_measure,
            quantity,
            inventory_history_status,
            created_by
        ) VALUES (
            p_inventory_id,
            p_product_id,
            p_unit_of_measure,
            p_converted_quantity,
            1, -- 1 indicates the quantity was subtracted
            p_user_id
        );
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_invoice_with_items` (IN `p_invoice_id` INT, IN `p_customer_id` INT, IN `p_invoice_number` VARCHAR(50), IN `p_invoice_date` VARCHAR(50), IN `p_due_date` VARCHAR(50), IN `p_subtotal` DECIMAL(10,2), IN `p_adjustment_amount` DECIMAL(10,2), IN `p_grand_total` DECIMAL(10,2), IN `p_total_gst_amount` DECIMAL(10,2), IN `p_amount_in_words` TEXT, IN `p_gst_enable` INT, IN `p_sgst` DECIMAL(10,2), IN `p_cgst` DECIMAL(10,2), IN `p_igst` DECIMAL(10,2), IN `p_payment_mode` VARCHAR(20), IN `p_items_count` INT, IN `p_invoice_item_ids` JSON, IN `p_item_ids` JSON, IN `p_units_of_measure` JSON, IN `p_quantities` JSON, IN `p_rates` JSON, IN `p_amounts` JSON, IN `p_discount_enable` JSON, IN `p_discount_rate` JSON, IN `p_discount_amount` JSON, IN `p_item_gst_amount` JSON, IN `p_tax_inclusive_enable` JSON, IN `p_tax_percentage` JSON, IN `p_invoice_status` INT, IN `p_user_id` INT)   BEGIN
    -- Declare variables
    DECLARE i INT DEFAULT 0;
    DECLARE item_length INT;
    DECLARE invoice_item_ids_length INT;
    DECLARE i_invoice_item_id INT;
    DECLARE i_product_id INT;
    DECLARE unit_of_measure VARCHAR(50);
    DECLARE quantity DECIMAL(15,3);
    DECLARE unit_price DECIMAL(10,2);
    DECLARE amount DECIMAL(10,2);
    DECLARE discount_enable INT;
    DECLARE discount_rate DECIMAL(10,2);
    DECLARE discount_amount DECIMAL(10,2);
    DECLARE gst_amount DECIMAL(10,2);
    DECLARE tax_inclusive_enable VARCHAR(3);
    DECLARE current_stock DECIMAL(15,3);
    DECLARE bottom_stock INT;

    -- Set item_length to the value of p_items_count
    SET item_length = p_items_count;

    -- Calculate the length of the JSON array
    SET invoice_item_ids_length = JSON_LENGTH(p_invoice_item_ids);

    -- Update the inv_invoices table
    UPDATE inv_invoices
    SET
        customer_id = p_customer_id,
        invoice_number = p_invoice_number,
        invoice_date = p_invoice_date,
        invoice_due_date = p_due_date,
        subtotal = p_subtotal,
        adjustments = p_adjustment_amount,
        grand_total = p_grand_total,
        total_gst_amount = p_total_gst_amount,
        amount_in_words = p_amount_in_words,
        gst = p_gst_enable,
        sgst = p_sgst,
        cgst = p_cgst,
        igst = p_igst,
        payment_mode = p_payment_mode,
        invoice_status = p_invoice_status,
        updated_on = CURRENT_TIMESTAMP()
    WHERE invoice_id = p_invoice_id;

    -- Mark items as deleted if they are not in the input JSON array of invoice_item_ids
    UPDATE inv_invoice_items
    SET deleted = 1
    WHERE invoice_id = p_invoice_id
    AND invoice_item_id NOT IN (
        SELECT DISTINCT CAST(JSON_UNQUOTE(JSON_EXTRACT(p_invoice_item_ids, CONCAT('$[', idx, ']'))) AS INT)
        FROM (
            SELECT 0 idx UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
            UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
        ) AS indices
        WHERE idx < invoice_item_ids_length
    );

    -- Loop through the JSON arrays and insert/update items in inv_invoice_items
    WHILE i < item_length DO
        -- Extract individual item details
        SET i_invoice_item_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_invoice_item_ids, CONCAT('$[', i, ']'))) AS INT);
        SET i_product_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_item_ids, CONCAT('$[', i, ']'))) AS INT);
        SET unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_units_of_measure, CONCAT('$[', i, ']')));
        SET quantity = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_quantities, CONCAT('$[', i, ']'))) AS DECIMAL(15,3));
        SET unit_price = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_rates, CONCAT('$[', i, ']'))) AS DECIMAL(10,2));
        SET amount = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_amounts, CONCAT('$[', i, ']'))) AS DECIMAL(10,2));
        SET discount_enable = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_discount_enable, CONCAT('$[', i, ']'))) AS INT);
        SET discount_rate = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_discount_rate, CONCAT('$[', i, ']'))) AS DECIMAL(10,2));
        SET discount_amount = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_discount_amount, CONCAT('$[', i, ']'))) AS DECIMAL(10,2));
        SET gst_amount = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_item_gst_amount, CONCAT('$[', i, ']'))) AS DECIMAL(10,2));
        SET tax_inclusive_enable = JSON_UNQUOTE(JSON_EXTRACT(p_tax_inclusive_enable, CONCAT('$[', i, ']')));

        -- Check if the item already exists using invoice_item_id
        IF EXISTS (
            SELECT 1 FROM inv_invoice_items 
            WHERE invoice_item_id = i_invoice_item_id
        ) THEN
            -- Update the existing item
            UPDATE inv_invoice_items
            SET
                product_id = i_product_id,
                unit_of_measure = unit_of_measure,
                quantity = quantity,
                unit_price = unit_price,
                amount = amount,
                discount_rate = discount_rate,
                discount_amount = discount_amount,
                gst_amount = gst_amount,
                tax_inclusive_enable = tax_inclusive_enable,
                discount_enable = discount_enable,
                invoice_items_status = p_invoice_status,
                deleted = 0 -- Mark as not deleted
            WHERE invoice_item_id = i_invoice_item_id;
        ELSE
            -- Insert new item
            INSERT INTO inv_invoice_items (
                invoice_id,
                product_id,
                unit_of_measure,
                quantity,
                unit_price,
                amount,
                discount_rate,
                discount_amount,
                gst_amount,
                tax_inclusive_enable,
                discount_enable,
                invoice_items_status,
                deleted
            ) VALUES (
                p_invoice_id,
                i_product_id,
                unit_of_measure,
                quantity,
                unit_price,
                amount,
                discount_rate,
                discount_amount,
                gst_amount,
                tax_inclusive_enable,
                discount_enable,
                p_invoice_status,
                0 -- Not deleted
            );
        END IF;

        -- Update inventory if invoice status is 1 (Purchased)
        IF p_invoice_status = 1 THEN
            -- Check current stock
            SET current_stock = (
                SELECT quantity_in_stock
                FROM inv_inventory
                WHERE product_id = i_product_id
                LIMIT 1
            );

            -- Subtract the quantity from inventory
            UPDATE inv_inventory
            SET quantity_in_stock = current_stock - quantity
            WHERE product_id = i_product_id;

            -- Insert stock-out record into inv_inventory_history table
            INSERT INTO inv_inventory_history (
                inventory_id, 
                product_id, 
                unit_of_measure, 
                quantity, 
                inventory_history_status, 
                created_by
            ) VALUES (
                (SELECT inventory_id FROM inv_inventory WHERE product_id = i_product_id), -- Fetch the correct inventory_id
                i_product_id,
                unit_of_measure,
                quantity,
                1, -- 'Out' status
                p_user_id -- Assuming p_user_id is the current user who performed the action
            );

            -- Check if stock is below bottom stock level
            SET bottom_stock = (
                SELECT bottom_stock
                FROM inv_products
                WHERE product_id = i_product_id
                LIMIT 1
            );

            IF current_stock - quantity < bottom_stock THEN
                -- Insert into out_of_stock if below bottom stock level
                INSERT INTO inv_out_of_stock (
                    product_id, 
                    unit_of_measure, 
                    quantity, 
                    created_on
                ) VALUES (
                    i_product_id, 
                    unit_of_measure, 
                    quantity, 
                    CURRENT_TIMESTAMP()
                );
            ELSE
                -- Update inventory status to in-stock (1) if above or equal to bottom stock level
                UPDATE inv_inventory
                SET inventory_status = 1
                WHERE product_id = i_product_id;
            END IF;
            CALL fetch_last_delivery_challan_number();
        END IF;

        SET i = i + 1;
    END WHILE;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_inv_firm_profile` (IN `p_firm_name` VARCHAR(255), IN `p_registration_number` VARCHAR(50), IN `p_logo` TEXT, IN `p_phone_number` VARCHAR(12), IN `p_email_id` VARCHAR(255), IN `p_street` VARCHAR(255), IN `p_locality` VARCHAR(50), IN `p_city` VARCHAR(50), IN `p_district` VARCHAR(50), IN `p_state` VARCHAR(50), IN `p_country` VARCHAR(50), IN `p_pin_code` VARCHAR(10), IN `p_gstin` VARCHAR(15), IN `p_pan` VARCHAR(10), IN `p_tax_registration_number` VARCHAR(20), IN `p_default_tax_percentage` DECIMAL(5,2), IN `p_bank_name` VARCHAR(255), IN `p_account_number` VARCHAR(50), IN `p_ifsc_code` VARCHAR(20), IN `p_bank_branch` VARCHAR(100), IN `p_invoice_terms_and_conditions` TEXT)   BEGIN
    -- Update the firm profile where company_id is 1
    UPDATE inv_firm_profile
    SET
        firm_name = p_firm_name,
        registration_number = p_registration_number,
        logo = p_logo, -- Updating the logo path
        phone_number = p_phone_number,
        email_id = p_email_id,
        street = p_street,
        locality = p_locality,
        city = p_city,
        district = p_district,
        state = p_state,
        country = p_country,
        pin_code = p_pin_code,
        gstin = p_gstin,
        pan = p_pan,
        tax_registration_number = p_tax_registration_number,
        default_tax_percentage = p_default_tax_percentage,
        bank_name = p_bank_name,
        account_number = p_account_number,
        ifsc_code = p_ifsc_code,
        bank_branch = p_bank_branch,
        invoice_terms_and_conditions = p_invoice_terms_and_conditions,
        updated_on = CURRENT_TIMESTAMP()
    WHERE company_id = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_product_details` (IN `p_product_id` INT, IN `p_product_name` VARCHAR(255), IN `p_hsn_code` VARCHAR(10), IN `p_product_category` VARCHAR(100), IN `p_product_type` INT, IN `p_unit_of_measure` VARCHAR(50), IN `p_bottom_stock` INT, IN `p_order_quantity` INT, IN `p_unit_price` DECIMAL(10,2), IN `p_pricing_type` INT, IN `p_discount_enable` INT, IN `p_tax_percentage` DECIMAL(50,2), IN `p_product_notes` TEXT, IN `p_created_by` INT, IN `p_item_id` JSON, IN `p_item_unit_of_measure` JSON, IN `p_item_quantity_used` JSON, IN `p_used_product_id` JSON)   BEGIN
    -- Declare variables
    DECLARE i INT DEFAULT 0;
    DECLARE item_length INT;

    -- Set item_length to the length of JSON array
    SET item_length = JSON_LENGTH(p_item_id);

    -- Update the inv_products table
    UPDATE inv_products
    SET
        product_name = p_product_name,
        hsn_code = p_hsn_code,
        product_category = p_product_category,
        product_type = p_product_type,
        unit_of_measure = p_unit_of_measure,
        bottom_stock = p_bottom_stock,
        order_quantity = p_order_quantity,
        unit_price = p_unit_price,
        pricing_type = p_pricing_type,
        discountable = p_discount_enable,
        tax_percentage = p_tax_percentage,
        prouct_terms_and_conditions = p_product_notes,
        created_by = p_created_by,
        updated_on = CURRENT_TIMESTAMP()
    WHERE product_id = p_product_id;

    -- Loop through the JSON arrays and insert/update items in inv_product_items
    WHILE i < item_length DO
        -- Check if the item already exists
        IF EXISTS (
            SELECT 1 FROM inv_product_items 
            WHERE item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_id, CONCAT('$[', i, ']')))
            AND product_id = p_product_id
        ) THEN
            -- Update the existing item
            UPDATE inv_product_items
            SET
                used_product_id = JSON_UNQUOTE(JSON_EXTRACT(p_used_product_id, CONCAT('$[', i, ']'))),
                unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_item_unit_of_measure, CONCAT('$[', i, ']'))),
                quantity_used = JSON_UNQUOTE(JSON_EXTRACT(p_item_quantity_used, CONCAT('$[', i, ']')))
            WHERE item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_id, CONCAT('$[', i, ']')))
            AND product_id = p_product_id;
        ELSE
            -- Insert new item
            INSERT INTO inv_product_items (
                item_id,
                product_id,
                used_product_id,
                unit_of_measure,
                quantity_used,
                product_item_status,
                deleted
            ) VALUES (
                JSON_UNQUOTE(JSON_EXTRACT(p_item_id, CONCAT('$[', i, ']'))),
                p_product_id,
                JSON_UNQUOTE(JSON_EXTRACT(p_used_product_id, CONCAT('$[', i, ']'))),
                JSON_UNQUOTE(JSON_EXTRACT(p_item_unit_of_measure, CONCAT('$[', i, ']'))),
                JSON_UNQUOTE(JSON_EXTRACT(p_item_quantity_used, CONCAT('$[', i, ']'))),
                1,  -- Active status
                0   -- Not deleted
            );
        END IF;

        SET i = i + 1;
    END WHILE;

    -- Update deleted column for items not included in the input JSON arrays
    UPDATE inv_product_items
    SET deleted = 1
    WHERE product_id = p_product_id
    AND item_id NOT IN (
        SELECT JSON_UNQUOTE(JSON_EXTRACT(p_item_id, CONCAT('$[', idx, ']')))
        FROM (
            SELECT 0 idx UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
            UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
        ) AS indices
        WHERE idx < item_length
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_product_status` (IN `p_product_id` INT, IN `p_status` INT)   BEGIN
    -- Update the product status in the inv_products table
    UPDATE inv_products
    SET product_status = p_status
    WHERE product_id = p_product_id;

    -- Update the product_item_status in the inv_product_item table
    UPDATE inv_product_items
    SET product_item_status = p_status
    WHERE product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_purchase_order_with_items` (IN `p_purchase_order_id` INT, IN `p_vendor_id` INT, IN `p_purchase_order_number` VARCHAR(255), IN `p_subtotal` DECIMAL(15,2), IN `p_discount_percentage` DECIMAL(5,2), IN `p_discount_amount` DECIMAL(15,2), IN `p_adjustment_amount` DECIMAL(15,2), IN `p_grand_total` DECIMAL(15,2), IN `p_amount_in_words` VARCHAR(255), IN `p_item_count` INT, IN `p_item_ids` JSON, IN `p_purchase_order_item_ids` JSON, IN `p_units_of_measure` JSON, IN `p_quantities` JSON, IN `p_rates` JSON, IN `p_amounts` JSON, IN `p_purchase_order_status` INT, IN `p_purchased_date` VARCHAR(10), IN `p_user_id` INT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE item_length INT;
    DECLARE purchase_order_item_ids_length INT;
    DECLARE v_purchase_order_item_id INT;
    DECLARE v_product_id INT;
    DECLARE v_unit_of_measure VARCHAR(50);
    DECLARE v_quantity DECIMAL(15,3);
    DECLARE v_unit_price DECIMAL(10,2);
    DECLARE v_amount DECIMAL(10,2);
    DECLARE v_bottom_stock INT;
    DECLARE v_current_stock DECIMAL(15,3);

    -- Set the length of items based on the count
    SET item_length = p_item_count;

    -- Calculate the length of the JSON array
    SET purchase_order_item_ids_length = JSON_LENGTH(p_purchase_order_item_ids);

    -- Update the purchase order in inv_purchase_orders table
    UPDATE inv_purchase_orders
    SET
        vendor_id = p_vendor_id,
        purchase_order_number = p_purchase_order_number,
        subtotal = p_subtotal,
        discount = p_discount_percentage,
        discount_amount = p_discount_amount,
        adjustment = p_adjustment_amount,
        grand_total = p_grand_total,
        amount_in_words = p_amount_in_words,
        purchase_order_status = p_purchase_order_status,
        purchased_date = IF(p_purchase_order_status = 1, STR_TO_DATE(p_purchased_date, '%d-%m-%Y'), NULL),
        updated_on = CURRENT_TIMESTAMP()
    WHERE purchase_order_id = p_purchase_order_id;

    -- Mark items as deleted if they are not in the input JSON array of purchase_order_item_ids
    UPDATE inv_purchase_order_items
    SET deleted = 1
    WHERE purchase_order_id = p_purchase_order_id
    AND purchase_order_item_id NOT IN (
        SELECT JSON_UNQUOTE(JSON_EXTRACT(p_purchase_order_item_ids, CONCAT('$[', idx, ']')))
        FROM (
            SELECT 0 AS idx UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 
            UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
            -- Extend this UNION ALL if necessary to cover more indices
        ) AS indices
        WHERE idx < purchase_order_item_ids_length
    );

	 -- Loop through the items and insert/update them in inv_purchase_order_items
    WHILE i < item_length DO
        -- Extract item details from JSON
        SET v_purchase_order_item_id = JSON_UNQUOTE(JSON_EXTRACT(p_purchase_order_item_ids, CONCAT('$[', i, ']')));
        SET v_product_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids, CONCAT('$[', i, ']')));
        SET v_unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_units_of_measure, CONCAT('$[', i, ']')));
        SET v_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_quantities, CONCAT('$[', i, ']')));
        SET v_unit_price = JSON_UNQUOTE(JSON_EXTRACT(p_rates, CONCAT('$[', i, ']')));
        SET v_amount = JSON_UNQUOTE(JSON_EXTRACT(p_amounts, CONCAT('$[', i, ']')));

        -- Check if the item already exists
        IF EXISTS (
            SELECT 1 FROM inv_purchase_order_items 
            WHERE purchase_order_item_id = v_purchase_order_item_id
        ) THEN
            -- Update the existing item
            UPDATE inv_purchase_order_items
            SET
                unit_of_measure = v_unit_of_measure,
                quantity = v_quantity,
                unit_price = v_unit_price,
                amount = v_amount,
                purchase_order_item_status=p_purchase_order_status,
                deleted = 0, -- Mark as not deleted
                date = CURRENT_TIMESTAMP() -- Update timestamp to current time
            WHERE purchase_order_item_id = v_purchase_order_item_id;
        ELSE
            -- Insert new item
            INSERT INTO inv_purchase_order_items (
                purchase_order_id,
                vendor_id,
                product_id,
                unit_of_measure,
                quantity,
                unit_price,
                amount,
                deleted
            ) VALUES (
                p_purchase_order_id,
                p_vendor_id,
                v_product_id,
                v_unit_of_measure,
                v_quantity,
                v_unit_price,
                v_amount,
                0 -- Not deleted
            );
        END IF;

        -- Check and update inventory if purchase order status is 'Purchased'
      -- Check and update inventory if purchase order status is 'Purchased'
        IF p_purchase_order_status = 1 THEN
            -- Get current stock and bottom stock
            SELECT 
                inv.quantity_in_stock,
                prod.bottom_stock
            INTO 
                v_current_stock,
                v_bottom_stock
            FROM 
                inv_inventory inv
            JOIN 
                inv_products prod ON inv.product_id = prod.product_id
            WHERE 
                inv.product_id = v_product_id;

            -- Update inventory by adding the purchased quantity
            UPDATE inv_inventory
            SET quantity_in_stock = quantity_in_stock + v_quantity
            WHERE product_id = v_product_id;

            -- Insert purchase data into inv_inventory_history table
            INSERT INTO inv_inventory_history (
                inventory_id, 
                product_id, 
                unit_of_measure, 
                quantity, 
                inventory_history_status, 
                created_by
            ) VALUES (
                (SELECT inventory_id FROM inv_inventory WHERE product_id = v_product_id), -- Fetch the correct inventory_id
                v_product_id,
                v_unit_of_measure,
                v_quantity,
                0, -- 'In' status
                p_user_id -- Assuming p_user_id is the current user who performed the action
            );

            -- If stock is less than bottom stock, update inventory status and insert out of stock record
           IF v_current_stock < v_bottom_stock THEN
                -- Update inventory status to out of stock
                UPDATE inv_inventory
                SET inventory_status = 0
                WHERE product_id = v_product_id;

                -- Insert out of stock record
                INSERT INTO inv_out_of_stock (
                    product_id,
                    out_of_stock_status,
                    created_by
                ) VALUES (
                    v_product_id,
                    0, -- Pending status
                    p_user_id
                );
            ELSE
                -- Update inventory status to in stock
                UPDATE inv_inventory
                SET inventory_status = 1
                WHERE product_id = v_product_id;
            END IF;
        END IF;


        SET i = i + 1;
    END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_quotation_with_items` (IN `p_quotation_id` INT, IN `p_customer_id` INT, IN `p_quotation_number` VARCHAR(50), IN `p_subtotal` DECIMAL(10,2), IN `p_discount_percentage` DECIMAL(10,2), IN `p_discount_amount` DECIMAL(10,2), IN `p_adjustment_amount` DECIMAL(10,2), IN `p_grand_total` DECIMAL(10,2), IN `p_amount_in_words` TEXT, IN `p_item_count` INT, IN `p_item_ids` JSON, IN `p_quotation_item_ids` JSON, IN `p_unit_of_measure` JSON, IN `p_converted_quantities` JSON, IN `p_rates` JSON, IN `p_amounts` JSON, IN `p_quotation_status` INT, IN `p_user_id` INT)   BEGIN
    DECLARE item_index INT DEFAULT 0;
    DECLARE last_invoice_number VARCHAR(20);

    -- Update the quotation details
    UPDATE inv_quotations
    SET 
        customer_id = p_customer_id,
        quotation_number = p_quotation_number,
        subtotal = p_subtotal,
        discount = p_discount_percentage,
        discount_amount = p_discount_amount,
        adjustment = p_adjustment_amount,
        grand_total = p_grand_total,
        amount_in_words = p_amount_in_words,
        updated_on = NOW(),
        quotation_status = p_quotation_status,
        created_by = p_user_id
    WHERE quotation_id = p_quotation_id;

    -- Update or insert items for the quotation
    WHILE item_index < p_item_count DO
        -- Extract individual item details
        SET @item_id = JSON_UNQUOTE(JSON_EXTRACT(p_item_ids, CONCAT('$[', item_index, ']')));
        SET @quotation_item_id = JSON_UNQUOTE(JSON_EXTRACT(p_quotation_item_ids, CONCAT('$[', item_index, ']')));
        SET @unit_of_measure = JSON_UNQUOTE(JSON_EXTRACT(p_unit_of_measure, CONCAT('$[', item_index, ']')));
        SET @converted_quantity = JSON_UNQUOTE(JSON_EXTRACT(p_converted_quantities, CONCAT('$[', item_index, ']')));
        SET @rate = JSON_UNQUOTE(JSON_EXTRACT(p_rates, CONCAT('$[', item_index, ']')));
        SET @amount = JSON_UNQUOTE(JSON_EXTRACT(p_amounts, CONCAT('$[', item_index, ']')));

        -- Check if the item already exists in the quotation
        IF @quotation_item_id IS NOT NULL THEN
            -- Update existing quotation item
            UPDATE inv_quotation_items
            SET 
                product_id = @item_id,
                unit_of_measure = @unit_of_measure,
                quantity = @converted_quantity,
                unit_price = @rate,
                amount = @amount,
                date = NOW()
            WHERE quotation_item_id = @quotation_item_id;
        ELSE
            -- Insert new quotation item
            INSERT INTO inv_quotation_items (
                quotation_id,
                customer_id,
                product_id,
                unit_of_measure,
                quantity,
                unit_price,
                amount,
                date
            ) VALUES (
                p_quotation_id,
                p_customer_id,
                @item_id,
                @unit_of_measure,
                @converted_quantity,
                @rate,
                @amount,
                NOW()
            );
        END IF;

        -- Move to the next item
        SET item_index = item_index + 1;
    END WHILE;

    -- If the quotation status is 'Finalized' (1), call fetch_last_invoice_number procedure
    IF p_quotation_status = 1 THEN
        CALL fetch_last_invoice_number();
    ELSE
        SET last_invoice_number = NULL;
    END IF;
    
    -- Return success message along with last_invoice_number if applicable
    SELECT 'Quotation updated successfully.' AS status, last_invoice_number;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_restock` (IN `p_out_of_stock_id` INT, IN `p_out_of_stock_status` INT)   BEGIN
    -- Update the out_of_stock_status based on the input value
    UPDATE inv_out_of_stock
    SET out_of_stock_status = p_out_of_stock_status, updated_on = NOW()
    WHERE out_of_stock_id = p_out_of_stock_id;
    
    -- Check if the row was updated
    IF ROW_COUNT() > 0 THEN
        SELECT 'success' AS status;
    ELSE
        SELECT 'error' AS status;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_role_status` (IN `p_role_id` INT, IN `p_status` INT)   BEGIN
    -- Update the role_permission_status column based on the provided role_id
    UPDATE inv_role_permissions
    SET role_permission_status = p_status,
        updated_on = CURRENT_TIMESTAMP
    WHERE role_id = p_role_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_vendor` (IN `p_vendor_id` INT, IN `p_salutation` VARCHAR(10), IN `p_company_name` VARCHAR(255), IN `p_contact_name` VARCHAR(255), IN `p_contact_number` VARCHAR(20), IN `p_email` VARCHAR(255), IN `p_billing_street` VARCHAR(255), IN `p_billing_locality` VARCHAR(255), IN `p_billing_city` VARCHAR(100), IN `p_billing_district` VARCHAR(100), IN `p_billing_state` VARCHAR(100), IN `p_billing_pincode` VARCHAR(10), IN `p_billing_country` VARCHAR(100), IN `p_shipping_street` VARCHAR(255), IN `p_shipping_locality` VARCHAR(255), IN `p_shipping_city` VARCHAR(100), IN `p_shipping_district` VARCHAR(100), IN `p_shipping_state` VARCHAR(100), IN `p_shipping_pincode` VARCHAR(10), IN `p_shipping_country` VARCHAR(100), IN `p_gstin` VARCHAR(20), IN `p_pan` VARCHAR(20), IN `p_bank_name` VARCHAR(255), IN `p_account_number` VARCHAR(20), IN `p_ifsc_code` VARCHAR(20), IN `p_branch_name` VARCHAR(255))   BEGIN
    -- Update the inv_vendors table with the provided values
    UPDATE inv_vendors
    SET
        salutation = p_salutation,
        vendor_company_name = p_company_name,
        vendor_contact_name = p_contact_name,
        vendor_phone_number = p_contact_number,
        vendor_email_id = p_email,
        billing_address_street = p_billing_street,
        billing_address_locality = p_billing_locality,
        billing_address_city = p_billing_city,
        billing_address_district = p_billing_district,
        billing_address_state = p_billing_state,
        billing_address_pincode = p_billing_pincode,
        billing_address_country = p_billing_country,
        shipping_address_street = p_shipping_street,
        shipping_address_locality = p_shipping_locality,
        shipping_address_city = p_shipping_city,
        shipping_address_district = p_shipping_district,
        shipping_address_state = p_shipping_state,
        shipping_address_pincode = p_shipping_pincode,
        shipping_address_country = p_shipping_country,
        vendor_gstin = p_gstin,
        vendor_pan_number = p_pan,
        vendor_bank_name = p_bank_name,
        vendor_account_number = p_account_number,
        vendor_ifsc_code = p_ifsc_code,
        vendor_branch_name = p_branch_name,
        updated_on = CURRENT_TIMESTAMP
    WHERE vendor_id = p_vendor_id;

    -- Check if any rows were affected
    IF ROW_COUNT() > 0 THEN
        SELECT 'success' AS status;
    ELSE
        SELECT 'error' AS status;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_vendor_status` (IN `p_vendor_id` INT(11), IN `p_status` INT(11))   BEGIN
    -- Update the vendor status in the inv_vendors table
    UPDATE inv_vendors
    SET vendor_status = p_status
    WHERE vendor_id = p_vendor_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validate_and_log_login` (IN `p_username` VARCHAR(255), IN `p_ip_address` VARCHAR(45))   BEGIN
    DECLARE v_user_id INT;
    DECLARE v_role_id INT;
    DECLARE v_employee_name VARCHAR(255);
    DECLARE v_login_id INT;

    -- Check if the user exists with the provided username
    SELECT employee_account_id, employee_role_id, employee_name INTO v_user_id, v_role_id, v_employee_name
    FROM inv_employee_accounts
    WHERE employee_username = p_username AND employee_status = 1 AND deleted = 0;

    IF v_user_id IS NOT NULL THEN
        -- User exists, log the login
        UPDATE inv_login_logs
        SET login_status = 'LoggedIn'
        WHERE employee_id = v_user_id AND logout_time IS NULL;       

        INSERT INTO inv_login_logs (employee_id, login_time, login_status, ip_address) 
        VALUES (v_user_id, CURRENT_TIMESTAMP, 1, p_ip_address);

        -- Get the last inserted login ID
        SELECT LAST_INSERT_ID() INTO v_login_id;

        -- Update inv_employee_accounts with the last login ID
        UPDATE inv_employee_accounts
        SET log_id = v_login_id
        WHERE employee_account_id = v_user_id;

        -- Return the status with username, role_id, and employee_name
        SELECT p_username AS username, v_role_id AS role_id, v_employee_name AS employee_name, v_login_id AS login_id, v_user_id AS user_id;
    ELSE
        -- User does not exist
        SELECT 'error' AS status, 'Invalid Username or Password' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `validate_login` (IN `p_username` VARCHAR(255))   BEGIN
    -- Declare variables to hold the fetched values
    DECLARE v_employee_password TEXT;
    DECLARE v_row_count INT;

    -- Initialize variables
    SET v_row_count = 0;

    -- Fetch the employee details based on the provided username
    SELECT 
        employee_password
    INTO 
        v_employee_password
    FROM 
        inv_employee_accounts
    WHERE 
        employee_username = p_username 
        AND employee_status = 1 
        AND deleted = 0;
    
    -- Check if any row was fetched
    IF FOUND_ROWS() = 0 THEN
        -- No rows found, return an error message
        SELECT 'error' AS status, 0 AS code;
    ELSE
        -- Return the fetched details
        SELECT 
        	'success' AS status,
            v_employee_password AS employee_password;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inv_customer`
--

CREATE TABLE `inv_customer` (
  `customer_id` int(11) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone_number` varchar(20) DEFAULT NULL,
  `customer_email_id` varchar(100) DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_locality` varchar(50) DEFAULT NULL,
  `address_district` varchar(50) NOT NULL,
  `address_city` varchar(50) DEFAULT NULL,
  `address_state` varchar(50) DEFAULT NULL,
  `address_pincode` varchar(10) DEFAULT NULL,
  `address_country` varchar(20) NOT NULL,
  `customer_gstin` varchar(20) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `customer_status` int(11) DEFAULT 1 COMMENT '0 - Inactive | 1- Active',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_customer`
--

INSERT INTO `inv_customer` (`customer_id`, `customer_code`, `salutation`, `customer_name`, `customer_phone_number`, `customer_email_id`, `address_street`, `address_locality`, `address_district`, `address_city`, `address_state`, `address_pincode`, `address_country`, `customer_gstin`, `created_on`, `updated_on`, `created_by`, `customer_status`, `deleted`) VALUES
(1, 'CUS-001', 'Ms.', 'Arul Jenifer A', '7589246210', 'jeni@gmail.com', 'NO.4, Avengers Street', 'Marvel Nagar', 'Pondicherry', 'Pondicherry', 'Pondicherry', '605001', 'India', '752HJ989NH956', '2024-09-13 00:37:32', '2024-09-13 00:44:19', 4, 1, 0),
(2, 'CUS-002', 'Messrs.', 'Alex Engineering', '8796541330', 'alex@gmail.com', 'No.88, Gotham Street', 'DC Nagar', 'Chennai', 'Raja Annamalaipuram', 'Tamil Nadu', '600028', 'India', '849GH99224', '2024-09-13 00:49:30', '2024-09-13 00:49:56', 4, 1, 0),
(3, 'CUS-003', 'Mr.', 'aswin satish', '8608226852', 'aswinsadhasivam@gmail.com', '29 thendral nagar main road, new saram , pondicherry', 'nellithope', 'Pondicherry', 'Saram(Py)', 'Pondicherry', '605013', 'India', 'sdfsdfsdf34534543', '2024-10-03 06:25:27', '2024-10-03 06:25:27', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_delivery_challan`
--

CREATE TABLE `inv_delivery_challan` (
  `delivery_challan_id` int(11) NOT NULL,
  `delivery_challan_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `delivery_challan_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `delivery_challan_status` int(11) DEFAULT 0 COMMENT '0 - Pending | 1 - Delivered | 2 - Canceled',
  `deleted` tinyint(1) DEFAULT 0,
  `delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_delivery_challan`
--

INSERT INTO `inv_delivery_challan` (`delivery_challan_id`, `delivery_challan_number`, `customer_id`, `delivery_challan_date`, `created_on`, `updated_on`, `created_by`, `delivery_challan_status`, `deleted`, `delivery_date`) VALUES
(2, 'DC-001', 2, '2024-09-28 11:01:38', '2024-09-28 11:01:38', '2024-09-28 11:01:38', 4, 0, 0, '2024-09-30'),
(3, 'DC-002', 1, '2024-09-28 13:25:17', '2024-09-28 13:25:17', '2024-09-28 13:25:17', 4, 0, 0, '2024-09-29'),
(4, 'DC-003', 1, '2024-09-28 13:39:17', '2024-09-28 13:39:17', '2024-09-28 13:39:17', 4, 0, 0, '2024-10-02'),
(5, 'DC-004', 3, '2024-10-03 06:31:26', '2024-10-03 06:31:26', '2024-10-03 06:31:26', 4, 0, 0, '2024-10-04'),
(6, 'DC-005', 1, '2024-10-04 13:19:06', '2024-10-04 13:19:06', '2024-10-04 13:19:06', 4, 0, 0, '2024-10-10'),
(7, 'DC-006', 1, '2024-10-07 13:01:17', '2024-10-07 13:01:17', '2024-10-07 13:01:17', 4, 0, 0, '2024-10-10');

-- --------------------------------------------------------

--
-- Table structure for table `inv_delivery_challan_items`
--

CREATE TABLE `inv_delivery_challan_items` (
  `delivery_challan_item_id` int(11) NOT NULL,
  `delivery_challan_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - not deleted | 1 - deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_delivery_challan_items`
--

INSERT INTO `inv_delivery_challan_items` (`delivery_challan_item_id`, `delivery_challan_id`, `customer_id`, `product_id`, `unit_of_measure`, `quantity`, `date`, `deleted`) VALUES
(2, 2, 2, 2, 'packets', 1, '2024-09-28 11:01:38', 0),
(3, 3, 1, 1, 'piece', 1, '2024-09-28 13:25:17', 0),
(4, 3, 1, 3, 'g', 1500, '2024-09-28 13:25:17', 0),
(5, 4, 1, 2, 'packets', 1, '2024-09-28 13:39:17', 0),
(6, 5, 3, 7, 'piece', 235, '2024-10-03 06:31:26', 0),
(7, 6, 1, 3, 'g', 6, '2024-10-04 13:19:06', 0),
(8, 7, 1, 7, 'piece', 56, '2024-10-07 13:01:17', 0),
(9, 7, 1, 1, 'piece', 789, '2024-10-07 13:01:17', 0),
(10, 7, 1, 5, 'piece', 166, '2024-10-07 13:01:17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_employee_accounts`
--

CREATE TABLE `inv_employee_accounts` (
  `employee_account_id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_contact_number` varchar(12) DEFAULT NULL,
  `employee_designation` varchar(100) DEFAULT NULL,
  `employee_role_id` int(11) DEFAULT NULL,
  `employee_username` varchar(255) DEFAULT NULL,
  `employee_password` text DEFAULT NULL,
  `log_id` int(11) NOT NULL DEFAULT 0,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `employee_status` int(11) DEFAULT 1 COMMENT '0 - Inactive | 1- Active',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_employee_accounts`
--

INSERT INTO `inv_employee_accounts` (`employee_account_id`, `employee_id`, `employee_name`, `employee_contact_number`, `employee_designation`, `employee_role_id`, `employee_username`, `employee_password`, `log_id`, `created_on`, `created_by`, `updated_on`, `employee_status`, `deleted`) VALUES
(4, 'EMP - 001', 'AJAY S', NULL, NULL, 1, 'ajay', '$argon2id$v=19$m=65536,t=4,p=1$OWc5eWkvQzgvTjkxdHVtUw$IirK6bD+SzRbTBiCOaaoKvcG0kcmV+vTeUbpWGOnPeM', 38, '2024-07-06 08:59:16', 0, '2024-10-07 12:55:05', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_firm_profile`
--

CREATE TABLE `inv_firm_profile` (
  `company_id` int(11) NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `locality` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `gstin` varchar(15) DEFAULT NULL,
  `pan` varchar(10) DEFAULT NULL,
  `tax_registration_number` varchar(20) DEFAULT NULL,
  `default_tax_percentage` decimal(5,2) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `bank_branch` varchar(100) DEFAULT NULL,
  `invoice_terms_and_conditions` text NOT NULL,
  `log_id` int(11) DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `district` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_firm_profile`
--

INSERT INTO `inv_firm_profile` (`company_id`, `firm_name`, `registration_number`, `logo`, `phone_number`, `email_id`, `street`, `locality`, `city`, `state`, `pin_code`, `gstin`, `pan`, `tax_registration_number`, `default_tax_percentage`, `bank_name`, `account_number`, `ifsc_code`, `bank_branch`, `invoice_terms_and_conditions`, `log_id`, `updated_on`, `district`, `country`) VALUES
(1, 'thiru', '123', 'firm_logo_designer.png', '1234567890', 'thiru@gmail.com', '1', '1', '1', '1', '605008', '123', '12312', '123', 123.00, '123', '123', '123', '123', 'Your invoice terms here', NULL, '2024-10-07 11:46:08', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `inv_inventory`
--

CREATE TABLE `inv_inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `quantity_in_stock` decimal(15,3) DEFAULT 0.000,
  `inventory_status` int(11) DEFAULT 1 COMMENT '0 - Out Of Stock | 1 - InStock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_inventory`
--

INSERT INTO `inv_inventory` (`inventory_id`, `product_id`, `unit_of_measure`, `quantity_in_stock`, `inventory_status`) VALUES
(1, 1, 'piece', 557.000, 1),
(2, 2, 'packets', 508.000, 1),
(3, 3, 'kg', 781.000, 1),
(4, 4, 'packets', -9.000, 1),
(8, 5, 'piece', 899.000, 1),
(9, 6, 'piece', 0.000, 1),
(10, 7, 'piece', 9.000, 1),
(11, 8, 'piece', 0.000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inv_inventory_history`
--

CREATE TABLE `inv_inventory_history` (
  `inventory_history_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `quantity` decimal(15,3) DEFAULT 0.000,
  `inventory_history_status` int(11) DEFAULT 0 COMMENT '0 - In | 1 - Out ',
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_inventory_history`
--

INSERT INTO `inv_inventory_history` (`inventory_history_id`, `inventory_id`, `product_id`, `unit_of_measure`, `quantity`, `inventory_history_status`, `created_on`, `created_by`) VALUES
(1, 2, 2, 'packets', 25.000, 0, '2024-09-13 07:03:54', 4),
(2, 3, 3, 'kg', 42.000, 0, '2024-09-13 07:03:54', 4),
(3, 2, 2, 'packets', 250.000, 0, '2024-09-13 07:07:50', 4),
(4, 2, 2, 'packets', 250.000, 0, '2024-09-13 07:18:05', 4),
(6, 2, 2, 'packets', 12.000, 1, '2024-09-13 09:08:56', 4),
(7, 1, 1, 'piece', 52.000, 1, '2024-09-14 00:42:52', 4),
(8, 3, 3, 'g', 600.000, 1, '2024-09-14 00:44:40', 4),
(9, 4, 4, 'packets', 3.000, 1, '2024-09-14 00:44:40', 4),
(12, 3, 3, 'kg', 526.000, 0, '2024-09-14 09:35:35', 4),
(13, 2, 2, 'packets', 1.000, 1, '2024-09-28 10:22:43', 4),
(14, 2, 2, 'packets', 1.000, 1, '2024-09-28 10:25:24', 4),
(15, 2, 2, 'packets', 1.000, 1, '2024-09-28 10:26:39', 4),
(16, 2, 2, 'packets', 1.000, 1, '2024-09-28 11:01:38', 4),
(17, 1, 1, 'piece', 3.000, 0, '2024-09-28 13:21:41', 4),
(18, 1, 1, 'piece', 1500.000, 0, '2024-09-28 13:23:04', 4),
(19, 1, 1, 'piece', 1.000, 1, '2024-09-28 13:25:17', 4),
(20, 3, 3, 'g', 1500.000, 1, '2024-09-28 13:25:17', 4),
(21, 2, 2, 'packets', 1.000, 1, '2024-09-28 13:39:17', 4),
(22, 10, 7, 'piece', 235.000, 1, '2024-10-03 06:31:26', 4),
(23, 10, 7, 'piece', 300.000, 0, '2024-10-03 07:01:30', 4),
(24, 3, 3, 'kg', 5.000, 0, '2024-10-04 13:12:59', 4),
(25, 3, 3, 'kg', 50.000, 0, '2024-10-04 13:16:49', 4),
(26, 3, 3, 'kg', 1000.000, 0, '2024-10-04 13:17:41', 4),
(27, 3, 3, 'g', 6.000, 1, '2024-10-04 13:19:06', 4),
(28, 8, 5, 'piece', 1065.000, 0, '2024-10-07 12:58:49', 4),
(29, 10, 7, 'piece', 56.000, 1, '2024-10-07 13:01:17', 4),
(30, 1, 1, 'piece', 789.000, 1, '2024-10-07 13:01:17', 4),
(31, 8, 5, 'piece', 166.000, 1, '2024-10-07 13:01:17', 4);

-- --------------------------------------------------------

--
-- Table structure for table `inv_invoices`
--

CREATE TABLE `inv_invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `invoice_due_date` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `gst` int(11) NOT NULL COMMENT '0 - Without GST | 1 - With GST',
  `sgst` decimal(10,2) DEFAULT 0.00,
  `cgst` decimal(10,2) DEFAULT 0.00,
  `igst` decimal(10,2) DEFAULT 0.00,
  `total_gst_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `adjustments` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) DEFAULT 0.00,
  `amount_in_words` text DEFAULT NULL,
  `payment_mode` varchar(20) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `invoice_status` int(11) DEFAULT 0 COMMENT '0 - Pending | 1 - Confirmed | 2 - Canceled',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_invoices`
--

INSERT INTO `inv_invoices` (`invoice_id`, `invoice_number`, `customer_id`, `invoice_date`, `invoice_due_date`, `subtotal`, `gst`, `sgst`, `cgst`, `igst`, `total_gst_amount`, `total_amount`, `adjustments`, `grand_total`, `amount_in_words`, `payment_mode`, `created_on`, `updated_on`, `created_by`, `invoice_status`, `deleted`) VALUES
(1, 'INV-001', 1, '2024-09-13 18:30:00', '2024-09-12 18:30:00', 3455.71, 1, 0.00, 0.00, 173.43, 0.00, 257.14, 0.00, 3629.14, 'Three Thousand And Six Hundred And Twenty Nine And Fourteen Paise Only', 'Debit/Credit Card', '2024-09-13 10:24:10', '2024-09-14 00:44:40', 4, 1, 0),
(2, 'INV-002', 2, '2024-09-12 18:30:00', '2024-09-12 18:30:00', 4680000.00, 0, 0.00, 0.00, 0.00, 0.00, 4680000.00, 0.00, 4680000.00, 'Forty Six Lakh And Eighty Thousand Only', 'cash', '2024-09-13 10:25:03', '2024-09-14 00:42:52', 4, 1, 0),
(10, 'INV-003', 2, '2024-09-27 18:30:00', '2024-09-27 18:30:00', 50.48, 1, 0.00, 0.00, 2.52, 0.00, 53.00, 0.00, 53.00, 'Fifty Three Only', 'cash', '2024-09-28 10:10:34', '2024-09-28 11:01:38', 4, 1, 0),
(11, 'INV-004', 1, '2024-09-27 18:30:00', '2024-09-27 18:30:00', 98025.00, 1, 0.00, 0.00, 0.00, 0.00, 98025.00, 0.00, 98025.00, 'Ninety Thousand And Eighty One And Forty Three Paise Only', 'cash', '2024-09-28 13:24:07', '2024-09-28 13:25:17', 4, 1, 0),
(12, 'INV-005', 1, '2024-09-28 18:30:00', '2024-09-28 18:30:00', 50.48, 1, 0.00, 0.00, 0.00, 0.00, 50.48, 0.00, 50.48, 'Fifty And Forty Eight Paise Only', '', '2024-09-28 13:39:01', '2024-09-28 13:39:17', 4, 1, 0),
(13, 'INV-006', 3, '2024-10-03 18:30:00', '2024-10-05 18:30:00', 67142.86, 1, 1678.57, 1678.57, 0.00, 0.00, 70500.00, 0.00, 70500.00, 'Seventy Thousand And Five Hundred Only', 'cash', '2024-10-03 06:30:34', '2024-10-03 06:31:26', 4, 1, 0),
(14, 'INV-007', 1, '2024-10-05 03:59:37', '2024-10-04 18:30:00', 32.10, 1, 0.00, 0.00, 0.00, 0.00, 32.10, 0.00, 32.10, 'Thirty Two And Ten Paise Only', '', '2024-10-04 13:17:58', '2024-10-04 13:17:58', 4, 1, 0),
(15, 'INV-008', 1, '2024-10-04 18:30:00', '2024-10-04 18:30:00', 32.10, 1, 0.00, 0.00, 0.00, 0.00, 32.10, 0.00, 32.10, 'Thirty Two And Ten Paise Only', 'cash', '2024-10-04 13:18:31', '2024-10-04 13:19:06', 4, 1, 0),
(16, 'INV-009', 1, '2024-10-05 03:08:54', '2024-10-04 18:30:00', 32.10, 1, 0.00, 0.00, 0.00, 0.00, 32.10, 0.00, 32.10, 'Thirty Two And Ten Paise Only', '', '2024-10-04 13:18:32', '2024-10-04 13:18:32', 4, 1, 0),
(17, 'INV-010', 1, '2024-10-04 18:30:00', '2024-10-04 18:30:00', 90000.00, 1, 0.00, 0.00, 0.00, 0.00, 90000.00, 0.00, 90000.00, 'Ninety Thousand Only', '', '2024-10-04 13:18:45', '2024-10-04 13:18:45', 4, 0, 0),
(18, 'INV-011', 1, '2024-10-07 18:30:00', '2024-10-07 18:30:00', 72112250.64, 1, 0.00, 0.00, 0.00, 0.00, 72112250.64, -0.74, 68506637.37, 'Six Crore And Eighty Five Lakh And Six Thousand And Six Hundred And Thirty Eight Only', 'UPI', '2024-10-07 13:00:46', '2024-10-07 13:01:17', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_invoice_items`
--

CREATE TABLE `inv_invoice_items` (
  `invoice_item_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT 0.00,
  `invoice_items_status` int(11) DEFAULT 0 COMMENT '0 - Pending | 1 - Delivered | 2 - Returned',
  `deleted` tinyint(1) DEFAULT 0,
  `discount_rate` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `gst_amount` decimal(10,2) DEFAULT 0.00,
  `tax_inclusive_enable` int(11) DEFAULT NULL COMMENT '0 - inclusive | 1 - exclusive',
  `discount_enable` int(11) DEFAULT NULL COMMENT '0 - discountable | 1 - non discountable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_invoice_items`
--

INSERT INTO `inv_invoice_items` (`invoice_item_id`, `invoice_id`, `product_id`, `unit_of_measure`, `quantity`, `unit_price`, `amount`, `invoice_items_status`, `deleted`, `discount_rate`, `discount_amount`, `gst_amount`, `tax_inclusive_enable`, `discount_enable`) VALUES
(1, 1, 4, 'packets', 3, 81.43, 244.29, 1, 0, 5.00, 0.00, 12.86, 1, 1),
(2, 2, 1, 'piece', 52, 90000.00, 4680000.00, 1, 0, 10.00, 0.00, 0.00, 1, 1),
(10, 10, 2, 'packets', 1, 50.48, 50.48, 1, 0, 0.00, 0.00, 2.52, 1, 0),
(11, 11, 1, 'piece', 1, 90000.00, 90000.00, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(12, 11, 3, 'g', 1500, 5.35, 8025.00, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(13, 12, 2, 'packets', 1, 50.48, 50.48, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(14, 13, 7, 'piece', 235, 285.71, 67142.86, 1, 0, 0.00, 0.00, 3357.14, 1, 0),
(15, 14, 3, 'g', 6, 5.35, 32.10, 0, 0, NULL, NULL, NULL, NULL, NULL),
(16, 15, 3, 'g', 6, 5.35, 32.10, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(17, 16, 3, 'g', 6, 5.35, 32.10, 1, 0, NULL, NULL, NULL, NULL, NULL),
(18, 17, 1, 'piece', 1, 90000.00, 90000.00, 0, 0, NULL, NULL, NULL, NULL, NULL),
(19, 18, 7, 'piece', 56, 285.71, 15999.76, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(20, 18, 1, 'piece', 789, 90000.00, 71010000.00, 1, 0, 0.00, 0.00, 0.00, 0, 0),
(21, 18, 5, 'piece', 166, 6543.68, 1086250.88, 1, 0, 0.00, 0.00, 0.00, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_login_logs`
--

CREATE TABLE `inv_login_logs` (
  `login_log_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `login_status` int(20) DEFAULT 1 COMMENT '0 - logout | 1- login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_login_logs`
--

INSERT INTO `inv_login_logs` (`login_log_id`, `employee_id`, `ip_address`, `login_time`, `logout_time`, `login_status`) VALUES
(1, 4, '::1', '2024-09-12 16:49:30', NULL, 0),
(2, 4, '::1', '2024-09-13 00:23:27', NULL, 0),
(3, 4, '::1', '2024-09-13 06:05:41', NULL, 0),
(4, 4, '::1', '2024-09-14 00:34:40', NULL, 0),
(5, 4, '::1', '2024-09-14 09:16:56', NULL, 0),
(6, 4, '::1', '2024-09-18 04:46:28', NULL, 0),
(7, 4, '::1', '2024-09-21 05:23:04', NULL, 0),
(8, 4, '::1', '2024-09-21 05:24:18', NULL, 0),
(9, 4, '::1', '2024-09-21 05:25:42', NULL, 0),
(10, 4, '::1', '2024-09-24 10:20:25', NULL, 0),
(11, 4, '::1', '2024-09-26 13:21:02', NULL, 0),
(12, 4, '::1', '2024-09-26 13:30:19', NULL, 0),
(13, 4, '::1', '2024-09-26 13:31:52', NULL, 0),
(14, 4, '::1', '2024-09-26 13:35:31', NULL, 0),
(15, 4, '::1', '2024-09-26 13:39:20', NULL, 0),
(16, 4, '::1', '2024-09-26 13:51:34', NULL, 0),
(17, 4, '::1', '2024-09-27 00:09:13', NULL, 0),
(18, 4, '::1', '2024-09-27 04:39:27', NULL, 0),
(19, 4, '::1', '2024-09-27 13:50:03', NULL, 0),
(20, 4, '::1', '2024-09-28 05:30:00', NULL, 0),
(21, 4, '223.178.86.27', '2024-09-28 13:02:54', NULL, 0),
(22, 4, '223.178.86.27', '2024-09-28 13:03:44', NULL, 0),
(23, 4, '223.178.86.27', '2024-09-28 13:06:38', NULL, 0),
(24, 4, '223.178.86.27', '2024-09-28 13:14:32', NULL, 0),
(25, 4, '223.178.86.27', '2024-09-28 13:15:29', NULL, 0),
(26, 4, '223.178.86.27', '2024-09-28 13:18:36', NULL, 0),
(27, 4, '123.63.127.153', '2024-10-03 04:48:30', NULL, 0),
(28, 4, '223.178.82.48', '2024-10-03 04:53:21', NULL, 0),
(29, 4, '223.178.82.48', '2024-10-03 06:17:25', NULL, 0),
(30, 4, '123.63.127.153', '2024-10-03 09:41:51', NULL, 0),
(31, 4, '123.63.127.153', '2024-10-04 09:04:42', NULL, 0),
(32, 4, '223.178.84.142', '2024-10-04 12:27:04', NULL, 0),
(33, 4, '223.178.84.142', '2024-10-04 12:37:55', NULL, 0),
(34, 4, '::1', '2024-10-04 15:07:16', NULL, 0),
(35, 4, '::1', '2024-10-04 15:08:09', NULL, 0),
(36, 4, '::1', '2024-10-05 03:07:03', NULL, 0),
(37, 4, '::1', '2024-10-07 09:36:48', NULL, 0),
(38, 4, '::1', '2024-10-07 12:55:05', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inv_out_of_stock`
--

CREATE TABLE `inv_out_of_stock` (
  `out_of_stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `out_of_stock_status` int(11) DEFAULT 1 COMMENT '0 - pending | 1 - confirmed | 2 - purhcased | 3 - canceled',
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_out_of_stock`
--

INSERT INTO `inv_out_of_stock` (`out_of_stock_id`, `product_id`, `vendor_id`, `out_of_stock_status`, `created_on`, `updated_on`, `created_by`, `deleted`) VALUES
(1, 2, 0, 1, '2024-09-13 07:03:54', '2024-09-13 07:07:16', 4, 0),
(2, 3, 0, 0, '2024-09-13 07:03:54', '2024-09-13 07:03:54', 4, 0),
(3, 2, 0, 1, '2024-09-13 07:07:50', '2024-09-13 07:17:50', 4, 0),
(4, 1, 0, 0, '2024-09-28 13:21:41', '2024-09-28 13:21:41', 4, 0),
(5, 1, 0, 0, '2024-09-28 13:23:04', '2024-09-28 13:23:04', 4, 0),
(6, 3, 0, 0, '2024-10-04 13:12:59', '2024-10-04 13:12:59', 4, 0),
(7, 3, 0, 0, '2024-10-04 13:16:49', '2024-10-04 13:16:49', 4, 0),
(8, 3, 0, 0, '2024-10-04 13:17:41', '2024-10-04 13:17:41', 4, 0),
(9, 5, 0, 0, '2024-10-07 12:58:49', '2024-10-07 12:58:49', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_pages`
--

CREATE TABLE `inv_pages` (
  `page_id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `page_type` int(11) NOT NULL DEFAULT 1 COMMENT '0 - module | 1 - page',
  `page_status` int(11) NOT NULL COMMENT '0 - inactive | 1 - active',
  `deleted` int(11) NOT NULL COMMENT '0 - not deleted| 1 - deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_pages`
--

INSERT INTO `inv_pages` (`page_id`, `page_name`, `page_type`, `page_status`, `deleted`) VALUES
(1, 'Dashboard', 0, 1, 0),
(2, 'Top Section', 1, 1, 0),
(3, 'Middle Charts', 1, 1, 0),
(4, 'Column Charts', 1, 1, 0),
(5, 'Invoice Purchase Cards', 1, 1, 0),
(6, 'Invoice', 0, 1, 0),
(7, 'Add Invoice', 1, 1, 0),
(8, 'Edit Invoice', 1, 1, 0),
(9, 'View Invoice', 1, 1, 0),
(10, 'Inventory', 0, 1, 0),
(11, 'Add Inventory Item', 1, 1, 0),
(12, 'View Inventory', 1, 1, 0),
(13, 'Edit Inventory', 1, 1, 0),
(14, 'Out Of Stock', 0, 1, 0),
(15, 'Restock Product', 1, 1, 0),
(16, 'Price History', 0, 1, 0),
(17, 'View Price History', 1, 1, 0),
(18, 'Purchase Order', 0, 1, 0),
(19, 'Add Purchase Order', 1, 1, 0),
(20, 'Edit Purchase Order', 1, 1, 0),
(21, 'View Purchase Order', 1, 1, 0),
(22, 'Products', 0, 1, 0),
(23, 'Add Products', 1, 1, 0),
(24, 'View Products', 1, 1, 0),
(25, 'Edit Products', 1, 1, 0),
(26, 'Vendors', 0, 1, 0),
(27, 'Add Vendors', 1, 1, 0),
(28, 'Edit Vendors', 1, 1, 0),
(29, 'View Vendors', 1, 1, 0),
(30, 'Transactions', 0, 1, 0),
(31, 'Transactions Chart', 1, 1, 0),
(32, 'Transactions Table', 1, 1, 0),
(33, 'Sales Report', 0, 1, 0),
(34, 'View Sales Report', 1, 1, 0),
(35, 'Customer', 0, 1, 0),
(36, 'Add Customer', 1, 1, 0),
(37, 'Edit Customer', 1, 1, 0),
(38, 'View Customer', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_price_history`
--

CREATE TABLE `inv_price_history` (
  `price_history_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `price_history_status` int(11) DEFAULT NULL COMMENT '0 - low | 1 - high',
  `created_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_products`
--

CREATE TABLE `inv_products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `hsn_code` varchar(10) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_type` int(11) NOT NULL COMMENT '0 - Stock | 1- Product',
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `bottom_stock` int(11) DEFAULT NULL,
  `order_quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `pricing_type` int(11) NOT NULL COMMENT '0 - Inclusive | 1 - Exclusive',
  `discountable` int(11) NOT NULL COMMENT '0 - non discountable | 1 - Discountable',
  `tax_percentage` decimal(50,2) NOT NULL,
  `prouct_terms_and_conditions` text DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `product_status` int(11) DEFAULT 1 COMMENT '0 - Inactive | 1- Active',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_products`
--

INSERT INTO `inv_products` (`product_id`, `product_name`, `hsn_code`, `product_code`, `product_category`, `product_type`, `unit_of_measure`, `bottom_stock`, `order_quantity`, `unit_price`, `pricing_type`, `discountable`, `tax_percentage`, `prouct_terms_and_conditions`, `created_on`, `updated_on`, `created_by`, `product_status`, `deleted`) VALUES
(1, 'Samsung S24 Ultra Pro', '8517', 'PR-001', 'Electronics', 1, 'piece', 50, 135, 100000.00, 0, 1, 18.00, 'Use with Care', '2024-09-13 06:43:40', '2024-09-13 06:49:08', 4, 1, 0),
(2, 'Noodles', '1902', 'PR-002', 'Edible', 0, 'packets', 100, 250, 53.00, 0, 1, 5.00, 'Use Before Expiry', '2024-09-13 06:46:50', '2024-09-13 06:48:34', 4, 1, 0),
(3, 'Salt', '2501', 'PR-003', 'Edible', 0, 'kg', 60, 178, 25.00, 0, 1, 5.00, 'Use within 6 months', '2024-09-13 06:50:19', '2024-09-13 06:50:19', 4, 1, 0),
(4, 'Chicken Noodles', '1902', 'PR-004', 'Edible', 1, 'packets', 5, 10, 90.00, 0, 1, 5.00, 'Eat within 30 minutes after cooked', '2024-09-13 06:52:53', '2024-09-13 06:53:08', 4, 1, 0),
(5, 'Dictonery', 'dic-01', 'PR-005', 'Books', 1, 'piece', 20, 5, 200.00, 0, 1, 18.00, '', '2024-09-28 15:11:10', '2024-09-28 15:11:10', 4, 1, 0),
(6, 'Deddy', 'de-111', 'PR-006', 'Toys', 1, 'piece', 5, 2, 300.00, 1, 1, 0.00, '', '2024-09-28 15:13:13', '2024-09-28 15:13:13', 4, 1, 0),
(7, 'test product1', 'tp001', 'PR-007', 'Books', 1, 'piece', 50, 200, 300.00, 0, 1, 5.00, 'test product descritpion termss', '2024-10-03 06:19:37', '2024-10-03 06:19:37', 4, 1, 0),
(8, 'test product2', 'tp001', 'PR-008', 'Edible', 1, 'piece', 50, 200, 300.00, 1, 1, 5.00, 'test prod 2 description', '2024-10-03 06:20:58', '2024-10-03 06:20:58', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_product_items`
--

CREATE TABLE `inv_product_items` (
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `used_product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity_used` int(11) DEFAULT NULL,
  `product_item_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Pending| 1 - Purchased | 2 - canceled',
  `deleted` int(11) NOT NULL DEFAULT 0 COMMENT '0 - not deleted | 1 - deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_product_items`
--

INSERT INTO `inv_product_items` (`item_id`, `product_id`, `used_product_id`, `unit_of_measure`, `quantity_used`, `product_item_status`, `deleted`) VALUES
(1, 4, 2, 'packets', 1, 0, 0),
(2, 4, 3, 'g', 6, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_purchase_orders`
--

CREATE TABLE `inv_purchase_orders` (
  `purchase_order_id` int(11) NOT NULL,
  `purchase_order_number` varchar(50) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `purchase_order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `adjustment` varchar(10) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `amount_in_words` text DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `purchase_order_status` int(11) DEFAULT 0 COMMENT '0 - Pending | 1 - Purchased | 2 - Canceled',
  `deleted` tinyint(1) DEFAULT 0,
  `purchased_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_purchase_orders`
--

INSERT INTO `inv_purchase_orders` (`purchase_order_id`, `purchase_order_number`, `vendor_id`, `purchase_order_date`, `subtotal`, `discount`, `adjustment`, `discount_amount`, `grand_total`, `amount_in_words`, `created_on`, `updated_on`, `created_by`, `purchase_order_status`, `deleted`, `purchased_date`) VALUES
(1, 'PO-001', 2, '2024-09-13 07:03:15', 2426.75, 1.54, '0.00', 37.37, 2389.38, 'Two Thousand And Three Hundred And Eighty Nine And Thirty Eight Paise Only', '2024-09-13 07:03:15', '2024-09-13 07:03:54', 4, 1, 0, '2024-09-13'),
(2, 'PO-002', 2, '2024-09-13 07:07:16', 14292.50, 0.00, '0.00', 0.00, 14292.50, 'fourteen thousand and two hundred and ninety two and fifty paise', '2024-09-13 07:07:16', '2024-09-13 07:07:50', 4, 1, 0, '2024-09-13'),
(3, 'PO-003', 2, '2024-09-13 07:17:50', 14292.50, 0.00, '0.00', 0.00, 14292.50, 'fourteen thousand and two hundred and ninety two and fifty paise', '2024-09-13 07:17:50', '2024-09-13 07:18:05', 4, 1, 0, '2024-09-14'),
(10, 'PO-004', 2, '2024-09-27 16:40:49', 300000.00, 0.00, '0.00', 0.00, 300000.00, 'three lakh', '2024-09-27 16:40:49', '2024-09-27 16:40:49', 4, 0, 0, NULL),
(11, 'PO-005', 1, '2024-09-27 16:41:35', 37.50, 0.00, '0.00', 0.00, 37.50, 'thirty seven and fifty paise', '2024-09-27 16:41:35', '2024-09-27 16:41:35', 4, 0, 0, NULL),
(12, 'PO-006', 1, '2024-09-28 13:20:58', 300000.00, 0.00, '0.00', 0.00, 300000.00, 'three lakh', '2024-09-28 13:20:58', '2024-09-28 13:21:41', 4, 1, 0, '2024-09-28'),
(13, 'PO-007', 1, '2024-09-28 13:22:39', 99999999.99, 0.00, '0.00', 0.00, 99999999.99, 'fifteen crore', '2024-09-28 13:22:39', '2024-09-28 13:23:04', 4, 1, 0, '2024-09-28'),
(14, 'PO-008', 1, '2024-10-03 07:01:03', 75000.00, 0.00, '0.00', 0.00, 75000.00, 'Seventy Five Thousand Only', '2024-10-03 07:01:03', '2024-10-03 07:01:03', 4, 0, 0, NULL),
(15, 'PO-009', 1, '2024-10-03 07:25:06', 90000.00, 0.00, '0.00', 0.00, 90000.00, 'ninety thousand', '2024-10-03 07:25:06', '2024-10-03 07:25:06', 4, 0, 0, NULL),
(16, 'PO-01', 1, '2024-10-03 07:25:06', 90000.00, 0.00, '0.00', 0.00, 90000.00, 'ninety thousand', '2024-10-03 07:25:06', '2024-10-04 12:45:09', 4, 0, 0, NULL),
(31, 'PO-010', 1, '2024-10-04 12:43:05', 69000.00, 0.00, '0.00', 0.00, 69000.00, 'sixty nine thousand', '2024-10-04 12:43:05', '2024-10-04 12:43:05', 4, 0, 0, NULL),
(32, 'PO-011', 1, '2024-10-04 12:45:16', 69000.00, 0.00, '0.00', 0.00, 69000.00, 'sixty nine thousand', '2024-10-04 12:45:16', '2024-10-04 12:45:16', 4, 0, 0, NULL),
(33, 'PO-012', 1, '2024-10-04 12:46:07', 69000.00, 0.00, '0.00', 0.00, 69000.00, 'sixty nine thousand', '2024-10-04 12:46:07', '2024-10-04 12:46:07', 4, 0, 0, NULL),
(34, 'PO-013', 1, '2024-10-04 12:46:14', 90000.00, 0.00, '0.00', 0.00, 90000.00, 'ninety thousand', '2024-10-04 12:46:14', '2024-10-04 12:46:14', 4, 0, 0, NULL),
(35, 'PO-014', 1, '2024-10-04 12:46:51', 69000.00, 0.00, '0.00', 0.00, 69000.00, 'sixty nine thousand', '2024-10-04 12:46:51', '2024-10-04 12:46:51', 4, 0, 0, NULL),
(36, 'PO-015', 1, '2024-10-04 12:47:25', 90000.00, 0.00, '0.00', 0.00, 90000.00, 'ninety thousand', '2024-10-04 12:47:25', '2024-10-04 12:47:25', 4, 0, 0, NULL),
(37, 'PO-016', 1, '2024-10-04 13:12:34', 125.00, 0.00, '0.00', 0.00, 125.00, 'one hundred and twenty five', '2024-10-04 13:12:34', '2024-10-04 13:12:59', 4, 1, 0, '2024-10-05'),
(38, 'PO-017', 1, '2024-10-04 13:16:25', 1250.00, 0.00, '0.00', 0.00, 1250.00, 'one thousand and two hundred and fifty', '2024-10-04 13:16:25', '2024-10-04 13:16:49', 4, 1, 0, '2024-10-04'),
(39, 'PO-018', 1, '2024-10-04 13:17:28', 25000.00, 0.00, '0.00', 0.00, 25000.00, 'twenty five thousand', '2024-10-04 13:17:28', '2024-10-04 13:17:41', 4, 1, 0, '2024-10-04'),
(40, 'PO-019', 1, '2024-10-07 12:58:18', 213000.00, 0.00, '0.00', 0.00, 213000.00, 'two lakh and thirteen thousand', '2024-10-07 12:58:18', '2024-10-07 12:58:49', 4, 1, 0, '2024-10-07');

-- --------------------------------------------------------

--
-- Table structure for table `inv_purchase_order_items`
--

CREATE TABLE `inv_purchase_order_items` (
  `purchase_order_item_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `purchase_order_item_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 - pending | 1 - purchased | 2 - canceled',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - not deleted | 1 - deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_purchase_order_items`
--

INSERT INTO `inv_purchase_order_items` (`purchase_order_item_id`, `purchase_order_id`, `vendor_id`, `product_id`, `unit_of_measure`, `quantity`, `unit_price`, `amount`, `date`, `purchase_order_item_status`, `deleted`) VALUES
(1, 1, 2, 2, 'packets', 25, 57.17, 1429.25, '2024-09-13 07:03:54', 0, 0),
(2, 1, 2, 3, 'kg', 42, 23.75, 997.50, '2024-09-13 07:03:54', 0, 0),
(3, 2, 2, 2, 'packets', 250, 57.17, 14292.50, '2024-09-13 07:07:50', 0, 0),
(4, 3, 2, 2, 'packets', 250, 57.17, 14292.50, '2024-09-13 07:18:05', 1, 0),
(7, 10, 2, 1, 'piece', 3, 100000.00, 300000.00, '2024-09-27 16:40:49', 0, 0),
(8, 11, 1, 3, 'kg', 2, 25.00, 37.50, '2024-09-27 16:41:35', 0, 0),
(9, 12, 1, 1, 'piece', 3, 100000.00, 300000.00, '2024-09-28 13:21:41', 1, 0),
(10, 13, 1, 1, 'piece', 1500, 100000.00, 99999999.99, '2024-09-28 13:23:04', 1, 0),
(11, 14, 1, 7, 'piece', 300, 250.00, 75000.00, '2024-10-03 07:01:03', 0, 0),
(12, 15, 1, 7, 'piece', 300, 300.00, 90000.00, '2024-10-03 07:25:06', 0, 0),
(13, 16, 1, 7, 'piece', 300, 300.00, 90000.00, '2024-10-03 07:25:06', 0, 0),
(14, 31, 1, 8, 'piece', 230, 300.00, 69000.00, '2024-10-04 12:43:05', 0, 0),
(15, 32, 1, 8, 'piece', 230, 300.00, 69000.00, '2024-10-04 12:45:16', 0, 0),
(16, 33, 1, 8, 'piece', 230, 300.00, 69000.00, '2024-10-04 12:46:07', 0, 0),
(17, 34, 1, 7, 'piece', 300, 300.00, 90000.00, '2024-10-04 12:46:14', 0, 0),
(18, 35, 1, 8, 'piece', 230, 300.00, 69000.00, '2024-10-04 12:46:51', 0, 0),
(19, 36, 1, 7, 'piece', 300, 300.00, 90000.00, '2024-10-04 12:47:25', 0, 0),
(20, 37, 1, 3, 'kg', 5, 25.00, 125.00, '2024-10-04 13:12:59', 1, 0),
(21, 38, 1, 3, 'kg', 50, 25.00, 1250.00, '2024-10-04 13:16:49', 1, 0),
(22, 39, 1, 3, 'kg', 1000, 25.00, 25000.00, '2024-10-04 13:17:41', 1, 0),
(23, 40, 1, 5, 'piece', 1065, 200.00, 213000.00, '2024-10-07 12:58:49', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_quotations`
--

CREATE TABLE `inv_quotations` (
  `quotation_id` int(11) NOT NULL,
  `quotation_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `quotation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `adjustment` varchar(10) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `amount_in_words` text DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `quotation_status` int(11) DEFAULT 0 COMMENT '0 - Pending | 1 - Finalized | 2 - Canceled',
  `deleted` tinyint(1) DEFAULT 0,
  `finalized_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_quotations`
--

INSERT INTO `inv_quotations` (`quotation_id`, `quotation_number`, `customer_id`, `quotation_date`, `subtotal`, `discount`, `adjustment`, `discount_amount`, `grand_total`, `amount_in_words`, `created_on`, `updated_on`, `created_by`, `quotation_status`, `deleted`, `finalized_date`) VALUES
(4, 'QTN-001', 1, '2024-09-25 18:30:00', 98025.00, 0.00, '0.00', 0.00, 98025.00, 'Ninety Thousand And Eighty One And Forty Three Paise Only', '2024-09-26 16:55:30', '2024-09-28 13:24:07', 4, 1, 0, ''),
(5, 'QTN-002', 2, '2024-09-27 18:30:00', 0.00, 0.00, '0', 0.00, 0.00, '', '2024-09-28 13:27:34', '2024-09-28 13:27:57', 4, 2, 0, ''),
(6, 'QTN-003', 1, '2024-09-27 18:30:00', 50.48, 0.00, '0.00', 0.00, 50.48, 'Fifty And Forty Eight Paise Only', '2024-09-28 13:38:46', '2024-09-28 13:39:01', 4, 1, 0, ''),
(7, 'QTN-004', 3, '2024-10-02 18:30:00', 67850.00, 0.00, '0', 0.00, 67850.00, 'Sixty Seven Thousand And Eight Hundred And Fifty Only', '2024-10-03 07:20:28', '2024-10-03 07:20:28', 4, 0, 0, ''),
(8, 'QTN-005', 3, '2024-10-02 18:30:00', 85713.00, 1.00, '0', 857.13, 84855.87, '', '2024-10-03 07:24:42', '2024-10-03 07:24:42', 4, 0, 0, ''),
(9, 'QTN-006', 1, '2024-10-03 18:30:00', 540000.00, 0.00, '0', 0.00, 540000.00, 'Five Lakh And Forty Thousand Only', '2024-10-04 12:57:51', '2024-10-04 12:57:51', 4, 0, 0, ''),
(10, 'QTN-007', 1, '2024-10-03 18:30:00', 90000.00, 0.00, '0', 0.00, 90000.00, 'Ninety Thousand Only', '2024-10-04 12:59:49', '2024-10-04 12:59:49', 4, 0, 0, ''),
(12, 'QTN-008', 3, '2024-10-03 18:30:00', 50.48, 0.00, '0', 0.00, 50.48, 'Fifty And Forty Eight Paise Only', '2024-10-04 13:00:55', '2024-10-04 13:00:55', 4, 0, 0, ''),
(13, 'QTN-009', 1, '2024-10-03 18:30:00', 50.48, 0.00, '0', 0.00, 50.48, 'Fifty And Forty Eight Paise Only', '2024-10-04 13:03:46', '2024-10-04 13:03:46', 4, 0, 0, ''),
(14, 'QTN-010', 1, '2024-10-03 18:30:00', 90000.00, 0.00, '0', 0.00, 90000.00, 'Ninety Thousand Only', '2024-10-04 13:05:10', '2024-10-04 13:05:10', 4, 0, 0, ''),
(15, 'QTN-011', 2, '2024-10-03 18:30:00', 90000.00, 0.00, '0', 0.00, 90000.00, 'Ninety Thousand Only', '2024-10-04 13:10:32', '2024-10-04 13:10:32', 4, 0, 0, ''),
(16, 'QTN-012', 1, '2024-10-03 18:30:00', 285.71, 0.00, '0', 0.00, 285.71, 'Two Hundred And Eighty Five And Seventy One Paise Only', '2024-10-04 13:11:51', '2024-10-04 13:11:51', 4, 0, 0, ''),
(17, 'QTN-013', 3, '2024-10-03 18:30:00', 5.35, 0.00, '0', 0.00, 5.35, 'Five And Thirty Five Paise Only', '2024-10-04 13:12:14', '2024-10-04 13:12:14', 4, 0, 0, ''),
(18, 'QTN-014', 1, '2024-10-03 18:30:00', 90000.00, 0.00, '0.00', 0.00, 90000.00, 'Ninety Thousand Only', '2024-10-04 13:14:47', '2024-10-04 13:18:45', 4, 1, 0, ''),
(19, 'QTN-015', 1, '2024-10-03 18:30:00', 32.10, 0.00, '0.00', 0.00, 32.10, 'Thirty Two And Ten Paise Only', '2024-10-04 13:16:08', '2024-10-04 13:18:32', 4, 1, 0, ''),
(20, 'QTN-016', 1, '2024-10-06 18:30:00', 72112250.64, 5.00, '-0.74', 3605612.53, 68506637.37, 'Six Crore And Eighty Five Lakh And Six Thousand And Six Hundred And Thirty Eight Only', '2024-10-07 12:57:45', '2024-10-07 13:00:46', 4, 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `inv_quotation_items`
--

CREATE TABLE `inv_quotation_items` (
  `quotation_item_id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `quotation_item_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 - pending | 1 - finalized | 2 - canceled',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - not deleted | 1 - deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_quotation_items`
--

INSERT INTO `inv_quotation_items` (`quotation_item_id`, `quotation_id`, `customer_id`, `product_id`, `unit_of_measure`, `quantity`, `unit_price`, `amount`, `date`, `quotation_item_status`, `deleted`) VALUES
(5, 4, 1, 1, 'piece', 1, 90000.00, 90000.00, '2024-09-28 13:24:07', 0, 0),
(6, 4, 1, 3, 'g', 1500, 5.35, 8025.00, '2024-09-28 13:24:07', 0, 0),
(7, 5, 2, 2, 'packets', 1, 50.48, 50.48, '2024-09-28 13:27:34', 2, 0),
(8, 6, 1, 2, 'packets', 1, 50.48, 50.48, '2024-09-28 13:39:01', 0, 0),
(9, 7, 3, 8, 'piece', 230, 295.00, 67850.00, '2024-10-03 07:20:28', 0, 0),
(10, 8, 3, 7, 'piece', 300, 285.71, 85713.00, '2024-10-03 07:24:42', 0, 0),
(11, 9, 1, 1, 'piece', 6, 90000.00, 540000.00, '2024-10-04 12:57:51', 0, 0),
(12, 10, 1, 1, 'piece', 1, 90000.00, 90000.00, '2024-10-04 12:59:49', 0, 0),
(13, 12, 3, 2, 'packets', 1, 50.48, 50.48, '2024-10-04 13:00:55', 0, 0),
(14, 13, 1, 2, 'packets', 1, 50.48, 50.48, '2024-10-04 13:03:46', 0, 0),
(15, 14, 1, 1, 'piece', 1, 90000.00, 90000.00, '2024-10-04 13:05:10', 0, 0),
(16, 15, 2, 1, 'piece', 1, 90000.00, 90000.00, '2024-10-04 13:10:32', 0, 0),
(17, 16, 1, 7, 'piece', 1, 285.71, 285.71, '2024-10-04 13:11:51', 0, 0),
(18, 17, 3, 3, 'g', 1, 5.35, 5.35, '2024-10-04 13:12:14', 0, 0),
(19, 18, 1, 1, 'piece', 1, 90000.00, 90000.00, '2024-10-04 13:18:45', 0, 0),
(20, 19, 1, 3, 'g', 6, 5.35, 32.10, '2024-10-04 13:18:32', 0, 0),
(21, 20, 1, 7, 'piece', 56, 285.71, 15999.76, '2024-10-07 13:00:46', 0, 0),
(22, 20, 1, 1, 'piece', 789, 90000.00, 71010000.00, '2024-10-07 13:00:46', 0, 0),
(23, 20, 1, 5, 'piece', 166, 6543.68, 1086250.88, '2024-10-07 13:00:46', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_role_pages`
--

CREATE TABLE `inv_role_pages` (
  `role_page_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_role_pages`
--

INSERT INTO `inv_role_pages` (`role_page_id`, `role_id`, `page_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 1, 19),
(20, 1, 20),
(21, 1, 21),
(22, 1, 22),
(23, 1, 23),
(24, 1, 24),
(25, 1, 25),
(26, 1, 26),
(27, 1, 27),
(28, 1, 28),
(29, 1, 29),
(30, 1, 30),
(31, 1, 31),
(32, 1, 32),
(33, 1, 33),
(34, 1, 34),
(35, 1, 35),
(36, 1, 36),
(37, 1, 37),
(38, 1, 38);

-- --------------------------------------------------------

--
-- Table structure for table `inv_role_permissions`
--

CREATE TABLE `inv_role_permissions` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_code` varchar(20) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role_permission_status` int(11) DEFAULT 1 COMMENT '0 - Inactive | 1- Active',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_role_permissions`
--

INSERT INTO `inv_role_permissions` (`role_id`, `role_name`, `role_code`, `created_on`, `updated_on`, `role_permission_status`, `deleted`) VALUES
(1, 'Super Admin', 'R-001', '2024-09-12 16:49:44', '2024-09-12 16:49:57', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_sales_history`
--

CREATE TABLE `inv_sales_history` (
  `sales_history_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `sales_history_status` int(11) DEFAULT NULL COMMENT '0 - low | 1 - high',
  `created_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_vendors`
--

CREATE TABLE `inv_vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_code` varchar(50) NOT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `vendor_company_name` varchar(100) NOT NULL,
  `vendor_contact_name` varchar(50) DEFAULT NULL,
  `vendor_phone_number` varchar(20) DEFAULT NULL,
  `vendor_email_id` varchar(100) DEFAULT NULL,
  `billing_address_street` varchar(255) DEFAULT NULL,
  `billing_address_locality` varchar(50) DEFAULT NULL,
  `billing_address_city` varchar(50) DEFAULT NULL,
  `billing_address_state` varchar(50) DEFAULT NULL,
  `billing_address_pincode` varchar(10) DEFAULT NULL,
  `shipping_address_street` varchar(255) DEFAULT NULL,
  `shipping_address_locality` varchar(50) DEFAULT NULL,
  `shipping_address_city` varchar(50) DEFAULT NULL,
  `shipping_address_state` varchar(50) DEFAULT NULL,
  `shipping_address_district` varchar(50) NOT NULL,
  `shipping_address_country` varchar(50) NOT NULL,
  `shipping_address_pincode` varchar(10) NOT NULL,
  `billing_address_district` varchar(50) NOT NULL,
  `billing_address_country` varchar(50) NOT NULL,
  `vendor_gstin` varchar(20) DEFAULT NULL,
  `vendor_pan_number` varchar(20) DEFAULT NULL,
  `vendor_bank_name` varchar(100) DEFAULT NULL,
  `vendor_account_number` varchar(50) DEFAULT NULL,
  `vendor_ifsc_code` varchar(20) DEFAULT NULL,
  `vendor_branch_name` varchar(100) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `vendor_status` int(11) DEFAULT 1 COMMENT '0 - Inactive | 1- Active',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_vendors`
--

INSERT INTO `inv_vendors` (`vendor_id`, `vendor_code`, `salutation`, `vendor_company_name`, `vendor_contact_name`, `vendor_phone_number`, `vendor_email_id`, `billing_address_street`, `billing_address_locality`, `billing_address_city`, `billing_address_state`, `billing_address_pincode`, `shipping_address_street`, `shipping_address_locality`, `shipping_address_city`, `shipping_address_state`, `shipping_address_district`, `shipping_address_country`, `shipping_address_pincode`, `billing_address_district`, `billing_address_country`, `vendor_gstin`, `vendor_pan_number`, `vendor_bank_name`, `vendor_account_number`, `vendor_ifsc_code`, `vendor_branch_name`, `created_on`, `updated_on`, `created_by`, `vendor_status`, `deleted`) VALUES
(1, 'V-001', 'Messrs.', 'Golden Globe Engineering', 'Thiruvarasan M', '7896541230', 'thiru@gmail.com', 'no.16,Mariamman Koil Street', 'Melparikalpatu', 'Bahoor', 'Pondicherry', '607402', 'no.16,Mariamman Koil Street', 'Melparikalpatu', 'Bahoor', 'Pondicherry', 'Pondicherry', 'India', '607402', 'Pondicherry', 'India', '9876QW87RT455', 'PAN102358', 'Mariamman Indian Bank', '546878923158', 'MIB007', 'Bahoor Bank', '2024-09-12 16:55:54', '2024-09-13 06:06:30', 4, 1, 0),
(2, 'V-002', 'Messrs.', 'Google', 'Bill Gates', '5647893210', 'gates@gmail.com', 'No.18, Rose Street', 'Ashok Nagar', 'Lawspet', 'Pondicherry', 'India', 'No.18, Rose Street', 'Ashok Nagar', 'Lawspet', 'Pondicherry', 'Pondicherry', '605008', 'India', 'Pondicherry', '605008', '7856HJK8825', 'PAN47922', 'ICICI Bank', '688765143554', 'ICI3466', 'ECR Branch', '2024-09-13 00:33:21', '2024-09-13 00:33:27', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_vendor_invoices`
--

CREATE TABLE `inv_vendor_invoices` (
  `vendor_invoice_id` int(11) NOT NULL,
  `vendor_invoice_number` varchar(50) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `invoice_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `invoice_due_date` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `gst` int(11) NOT NULL COMMENT '0 - Without GST | 1 - With GST',
  `sgst` decimal(10,2) DEFAULT 0.00,
  `cgst` decimal(10,2) DEFAULT 0.00,
  `igst` decimal(10,2) DEFAULT 0.00,
  `total_gst_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_charges` decimal(10,2) DEFAULT 0.00,
  `handling_fees` decimal(10,2) DEFAULT 0.00,
  `storage_fees` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `adjustments` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) DEFAULT 0.00,
  `amount_in_words` text DEFAULT NULL,
  `payment_mode` varchar(20) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `invoice_status` int(11) DEFAULT 1 COMMENT '0 - Pending | 1 - Confirmed | 2 - Canceled',
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_vendor_invoices`
--

INSERT INTO `inv_vendor_invoices` (`vendor_invoice_id`, `vendor_invoice_number`, `vendor_id`, `invoice_date`, `invoice_due_date`, `subtotal`, `gst`, `sgst`, `cgst`, `igst`, `total_gst_amount`, `shipping_charges`, `handling_fees`, `storage_fees`, `total_amount`, `adjustments`, `grand_total`, `amount_in_words`, `payment_mode`, `created_on`, `updated_on`, `created_by`, `invoice_status`, `deleted`) VALUES
(1, 'INV-001', 1, '2024-10-05 09:13:19', '2024-10-04 18:30:00', 84796.24, 1, 7628.38, 7628.38, 0.00, 15256.76, 525.00, 32.00, 465.00, 101075.00, 0.00, 101075.00, 'One Lakh And One Thousand And Seventy Five Only', 'cash', '2024-10-05 09:09:07', '2024-10-05 09:09:07', 4, 1, 0),
(2, 'INV-002', 1, '2024-10-06 18:30:00', '2024-10-06 18:30:00', 180508.47, 1, 16245.76, 16245.76, 0.00, 32491.52, 5620.00, 78.26, 956.20, 219654.46, 0.00, 219654.46, 'Two Lakh And Nineteen Thousand And Six Hundred And Fifty Four And Forty Six Paise Only', 'cash', '2024-10-07 13:00:12', '2024-10-07 13:00:12', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inv_vendor_invoice_items`
--

CREATE TABLE `inv_vendor_invoice_items` (
  `vendor_invoice_item_id` int(11) NOT NULL,
  `vendor_invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT 0.00,
  `invoice_items_status` int(11) DEFAULT 1 COMMENT '0 - Pending | 1 - Delivered | 2 - Returned',
  `deleted` tinyint(1) DEFAULT 0,
  `discount_rate` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `gst_amount` decimal(10,2) DEFAULT 0.00,
  `tax_inclusive_enable` int(11) DEFAULT NULL COMMENT '0 - inclusive | 1 - exclusive',
  `discount_enable` int(11) DEFAULT NULL COMMENT '0 - discountable | 1 - non discountable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inv_vendor_invoice_items`
--

INSERT INTO `inv_vendor_invoice_items` (`vendor_invoice_item_id`, `vendor_invoice_id`, `product_id`, `unit_of_measure`, `quantity`, `unit_price`, `amount`, `invoice_items_status`, `deleted`, `discount_rate`, `discount_amount`, `gst_amount`, `tax_inclusive_enable`, `discount_enable`) VALUES
(1, 1, 2, 'packets', 1, 50.48, 50.48, 1, 0, 0.00, 0.00, 2.52, 1, 0),
(2, 1, 1, 'piece', 1, 84745.76, 84745.76, 1, 0, 0.00, 0.00, 15254.24, 1, 0),
(3, 2, 5, 'piece', 1065, 169.49, 180508.47, 1, 0, 0.00, 0.00, 32491.53, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inv_customer`
--
ALTER TABLE `inv_customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`),
  ADD UNIQUE KEY `customer_gstin` (`customer_gstin`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_delivery_challan`
--
ALTER TABLE `inv_delivery_challan`
  ADD PRIMARY KEY (`delivery_challan_id`);

--
-- Indexes for table `inv_delivery_challan_items`
--
ALTER TABLE `inv_delivery_challan_items`
  ADD PRIMARY KEY (`delivery_challan_item_id`);

--
-- Indexes for table `inv_employee_accounts`
--
ALTER TABLE `inv_employee_accounts`
  ADD PRIMARY KEY (`employee_account_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `employee_role_id` (`employee_role_id`),
  ADD UNIQUE KEY `employee_username` (`employee_username`),
  ADD UNIQUE KEY `unique_employee_username` (`employee_username`);

--
-- Indexes for table `inv_firm_profile`
--
ALTER TABLE `inv_firm_profile`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `firm_name` (`firm_name`);

--
-- Indexes for table `inv_inventory`
--
ALTER TABLE `inv_inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inv_inventory_history`
--
ALTER TABLE `inv_inventory_history`
  ADD PRIMARY KEY (`inventory_history_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_inventory_history_inventory` (`inventory_id`);

--
-- Indexes for table `inv_invoices`
--
ALTER TABLE `inv_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_invoice_items`
--
ALTER TABLE `inv_invoice_items`
  ADD PRIMARY KEY (`invoice_item_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inv_login_logs`
--
ALTER TABLE `inv_login_logs`
  ADD PRIMARY KEY (`login_log_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `inv_out_of_stock`
--
ALTER TABLE `inv_out_of_stock`
  ADD PRIMARY KEY (`out_of_stock_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `inv_pages`
--
ALTER TABLE `inv_pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `inv_price_history`
--
ALTER TABLE `inv_price_history`
  ADD PRIMARY KEY (`price_history_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `inv_products`
--
ALTER TABLE `inv_products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_product_items`
--
ALTER TABLE `inv_product_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inv_purchase_orders`
--
ALTER TABLE `inv_purchase_orders`
  ADD PRIMARY KEY (`purchase_order_id`),
  ADD UNIQUE KEY `purchase_order_number` (`purchase_order_number`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_purchase_order_items`
--
ALTER TABLE `inv_purchase_order_items`
  ADD PRIMARY KEY (`purchase_order_item_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `inv_purchase_order_items_ibfk_3` (`vendor_id`);

--
-- Indexes for table `inv_quotations`
--
ALTER TABLE `inv_quotations`
  ADD PRIMARY KEY (`quotation_id`),
  ADD UNIQUE KEY `quotation_number` (`quotation_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_quotation_items`
--
ALTER TABLE `inv_quotation_items`
  ADD PRIMARY KEY (`quotation_item_id`),
  ADD KEY `quotation_id` (`quotation_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `inv_quotation_items_ibfk_3` (`customer_id`);

--
-- Indexes for table `inv_role_pages`
--
ALTER TABLE `inv_role_pages`
  ADD PRIMARY KEY (`role_page_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fk_page_id` (`page_id`);

--
-- Indexes for table `inv_role_permissions`
--
ALTER TABLE `inv_role_permissions`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_code` (`role_code`),
  ADD UNIQUE KEY `unique_role_name` (`role_name`),
  ADD UNIQUE KEY `unique_role_code` (`role_code`);

--
-- Indexes for table `inv_sales_history`
--
ALTER TABLE `inv_sales_history`
  ADD PRIMARY KEY (`sales_history_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `inv_vendors`
--
ALTER TABLE `inv_vendors`
  ADD PRIMARY KEY (`vendor_id`),
  ADD UNIQUE KEY `vendor_code` (`vendor_code`),
  ADD UNIQUE KEY `vendor_gstin` (`vendor_gstin`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `inv_vendor_invoices`
--
ALTER TABLE `inv_vendor_invoices`
  ADD PRIMARY KEY (`vendor_invoice_id`),
  ADD KEY `inv_vendor_invoices_ibfk_1` (`vendor_id`),
  ADD KEY `inv_vendor_invoices_ibfk_2` (`created_by`);

--
-- Indexes for table `inv_vendor_invoice_items`
--
ALTER TABLE `inv_vendor_invoice_items`
  ADD PRIMARY KEY (`vendor_invoice_item_id`),
  ADD KEY `inv_vendor_invoice_items_ibfk_1` (`vendor_invoice_id`),
  ADD KEY `inv_vendor_invoice_items_ibfk_2` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inv_customer`
--
ALTER TABLE `inv_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inv_delivery_challan`
--
ALTER TABLE `inv_delivery_challan`
  MODIFY `delivery_challan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inv_delivery_challan_items`
--
ALTER TABLE `inv_delivery_challan_items`
  MODIFY `delivery_challan_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inv_employee_accounts`
--
ALTER TABLE `inv_employee_accounts`
  MODIFY `employee_account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inv_firm_profile`
--
ALTER TABLE `inv_firm_profile`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inv_inventory`
--
ALTER TABLE `inv_inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inv_inventory_history`
--
ALTER TABLE `inv_inventory_history`
  MODIFY `inventory_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `inv_invoices`
--
ALTER TABLE `inv_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `inv_invoice_items`
--
ALTER TABLE `inv_invoice_items`
  MODIFY `invoice_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `inv_login_logs`
--
ALTER TABLE `inv_login_logs`
  MODIFY `login_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `inv_out_of_stock`
--
ALTER TABLE `inv_out_of_stock`
  MODIFY `out_of_stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inv_pages`
--
ALTER TABLE `inv_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `inv_price_history`
--
ALTER TABLE `inv_price_history`
  MODIFY `price_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inv_products`
--
ALTER TABLE `inv_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inv_product_items`
--
ALTER TABLE `inv_product_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inv_purchase_orders`
--
ALTER TABLE `inv_purchase_orders`
  MODIFY `purchase_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `inv_purchase_order_items`
--
ALTER TABLE `inv_purchase_order_items`
  MODIFY `purchase_order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inv_quotations`
--
ALTER TABLE `inv_quotations`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inv_quotation_items`
--
ALTER TABLE `inv_quotation_items`
  MODIFY `quotation_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inv_role_pages`
--
ALTER TABLE `inv_role_pages`
  MODIFY `role_page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `inv_role_permissions`
--
ALTER TABLE `inv_role_permissions`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inv_sales_history`
--
ALTER TABLE `inv_sales_history`
  MODIFY `sales_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inv_vendors`
--
ALTER TABLE `inv_vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inv_vendor_invoices`
--
ALTER TABLE `inv_vendor_invoices`
  MODIFY `vendor_invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inv_vendor_invoice_items`
--
ALTER TABLE `inv_vendor_invoice_items`
  MODIFY `vendor_invoice_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inv_customer`
--
ALTER TABLE `inv_customer`
  ADD CONSTRAINT `inv_customer_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_employee_accounts`
--
ALTER TABLE `inv_employee_accounts`
  ADD CONSTRAINT `inv_employee_accounts_ibfk_1` FOREIGN KEY (`employee_role_id`) REFERENCES `inv_role_permissions` (`role_id`);

--
-- Constraints for table `inv_inventory`
--
ALTER TABLE `inv_inventory`
  ADD CONSTRAINT `inv_inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`);

--
-- Constraints for table `inv_inventory_history`
--
ALTER TABLE `inv_inventory_history`
  ADD CONSTRAINT `fk_inventory_history_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inv_inventory` (`inventory_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inv_inventory_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`),
  ADD CONSTRAINT `inv_inventory_history_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_invoices`
--
ALTER TABLE `inv_invoices`
  ADD CONSTRAINT `inv_invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `inv_customer` (`customer_id`),
  ADD CONSTRAINT `inv_invoices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_invoice_items`
--
ALTER TABLE `inv_invoice_items`
  ADD CONSTRAINT `inv_invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `inv_invoices` (`invoice_id`),
  ADD CONSTRAINT `inv_invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`);

--
-- Constraints for table `inv_login_logs`
--
ALTER TABLE `inv_login_logs`
  ADD CONSTRAINT `inv_login_logs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_out_of_stock`
--
ALTER TABLE `inv_out_of_stock`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inv_price_history`
--
ALTER TABLE `inv_price_history`
  ADD CONSTRAINT `inv_price_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`),
  ADD CONSTRAINT `inv_price_history_ibfk_2` FOREIGN KEY (`purchase_order_id`) REFERENCES `inv_purchase_orders` (`purchase_order_id`);

--
-- Constraints for table `inv_products`
--
ALTER TABLE `inv_products`
  ADD CONSTRAINT `inv_products_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_product_items`
--
ALTER TABLE `inv_product_items`
  ADD CONSTRAINT `inv_product_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`);

--
-- Constraints for table `inv_purchase_orders`
--
ALTER TABLE `inv_purchase_orders`
  ADD CONSTRAINT `inv_purchase_orders_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `inv_vendors` (`vendor_id`),
  ADD CONSTRAINT `inv_purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_purchase_order_items`
--
ALTER TABLE `inv_purchase_order_items`
  ADD CONSTRAINT `inv_purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `inv_purchase_orders` (`purchase_order_id`),
  ADD CONSTRAINT `inv_purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`),
  ADD CONSTRAINT `inv_purchase_order_items_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `inv_vendors` (`vendor_id`);

--
-- Constraints for table `inv_role_pages`
--
ALTER TABLE `inv_role_pages`
  ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `inv_pages` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inv_role_pages_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `inv_role_permissions` (`role_id`);

--
-- Constraints for table `inv_sales_history`
--
ALTER TABLE `inv_sales_history`
  ADD CONSTRAINT `inv_sales_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`),
  ADD CONSTRAINT `inv_sales_history_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `inv_invoices` (`invoice_id`);

--
-- Constraints for table `inv_vendors`
--
ALTER TABLE `inv_vendors`
  ADD CONSTRAINT `inv_vendors_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_vendor_invoices`
--
ALTER TABLE `inv_vendor_invoices`
  ADD CONSTRAINT `inv_vendor_invoices_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `inv_vendors` (`vendor_id`),
  ADD CONSTRAINT `inv_vendor_invoices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `inv_employee_accounts` (`employee_account_id`);

--
-- Constraints for table `inv_vendor_invoice_items`
--
ALTER TABLE `inv_vendor_invoice_items`
  ADD CONSTRAINT `inv_vendor_invoice_items_ibfk_1` FOREIGN KEY (`vendor_invoice_id`) REFERENCES `inv_vendor_invoices` (`vendor_invoice_id`),
  ADD CONSTRAINT `inv_vendor_invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inv_products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
