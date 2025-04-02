<?php

function checkAccess($role_id, $page_id)
{
    $check_access_procedure_params = [
        ['value' => $role_id, 'type' => 'i'],
        ['value' => $page_id, 'type' => 'i']
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('check_user_access', $check_access_procedure_params);
        // Handle the result
        if ($result['status'] == 'success') {
            return $result['data'][0]['result'];
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

//checklogin function
function checkLogin($user_id, $login_id)
{
    $check_login_procedure_params = [
        ['value' => $login_id, 'type' => 'i'],
        ['value' => $user_id, 'type' => 'i'],
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('check_user_login_status', $check_login_procedure_params);
        // Handle the result
        if ($result['data'][0]['result'] != 1) {
            // User login status is valid
            echo "<script>window.location.href='" . BASEPATH . "';</script>";
            //echo json_encode(['login_status' => 'invalid', 'redirect' => BASEPATH]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function getUserIP()
{
    $ip = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipList[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


// Function to get the current location
function getCurrentLocation(): string // Specify return type (optional)
{
    // Extract the location (file name) from the URL
    $location = explode('/', $_SERVER['PHP_SELF']);
    $location = end($location); // Get the last element (file name)

    // Remove the ".php" extension
    $location = pathinfo($location, PATHINFO_FILENAME);

    // Check if the location is "index" and treat it as "Login"
    if ($location === "index") {
        $location = "Login";
    }


    // Retrieve the "type" parameter from the URL if it exists
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    $location = ucwords($location); // Capitalize the first letter of each word
    $type = ucwords($type); // Capitalize the first letter of each word
    // Concatenate "Sparrow | " with the type and current location
    if (!empty($type)) {
        return "$type $location | Rex";
    } else {
        return "$location | Rex";
    }
}

// Function to get the current URL path
function getRewrittenUrl(): string
{
    // Retrieve the URL path from the request URI
    $uri = $_SERVER['REQUEST_URI'];

    // Remove query string if present
    $uri = strtok($uri, '?');

    // Trim any leading/trailing slashes and explode by '/'
    $parts = explode('/', trim($uri, '/'));

    // Get the last segment 
    $lastSegment = end($parts);

    $lastSegment = str_replace('-', ' ', $lastSegment);
    // Remove the ".php" extension
    $lastSegment = pathinfo($lastSegment, PATHINFO_FILENAME);
    return ucfirst($lastSegment);
}

function uploadFile($fileInput, $destinationDir, $prefix = '')
{
    // Array to hold the status and errors
    $result = ['status' => 'success', 'files' => [], 'message' => ''];

    // Ensure the destination directory exists
    if (!file_exists($destinationDir)) {
        if (!mkdir($destinationDir, 0777, true)) {
            return ['status' => 'error', 'message' => 'Failed to create destination directory.'];
        }
    }

    // Ensure the temporary directory exists
    $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('upload_', true);
    if (!mkdir($tempDir)) {
        return ['status' => 'error', 'message' => 'Failed to create temporary directory.'];
    }

    // Check if the input is an array of files or a single file
    $isMultiple = is_array($fileInput['name']);

    // Process files
    $filesCount = $isMultiple ? count($fileInput['name']) : 1;
    for ($i = 0; $i < $filesCount; $i++) {
        $fileName = $isMultiple ? $fileInput['name'][$i] : $fileInput['name'];
        $fileTmpName = $isMultiple ? $fileInput['tmp_name'][$i] : $fileInput['tmp_name'];
        $fileError = $isMultiple ? $fileInput['error'][$i] : $fileInput['error'];

        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            $result['status'] = 'error';
            $result['message'] = "Error uploading file: $fileName. Error code: $fileError.";
            continue;
        }

        // Move the uploaded file to the temporary directory
        $tempFilePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($fileTmpName, $tempFilePath)) {
            $result['status'] = 'error';
            $result['message'] = "Failed to move file to temp directory: $fileName.";
            continue;
        }

        // Generate new file name with prefix
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $prefix . $fileName;
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $newFileName;

        // Check if the file already exists in the destination directory
        if (file_exists($destinationPath)) {
            // If the file exists, delete it
            if (!unlink($destinationPath)) {
                $result['status'] = 'error';
                $result['message'] = "Failed to delete existing file: $newFileName.";
                continue;
            }
        }

        // Move the file to the destination directory
        if (rename($tempFilePath, $destinationPath)) {
            $result['files'][] = $newFileName;
        } else {
            $result['status'] = 'error';
            $result['message'] = "Failed to move file to destination directory: $fileName.";
        }
    }

    // Clean up temporary directory
    foreach (glob($tempDir . '/*') as $file) {
        unlink($file);
    }
    rmdir($tempDir);

    return $result;
}


//check empty fields
function checkEmptyField($field, $fieldName)
{
    if ($field == null || $field == "" || $field == " ") {
        return "Error: $fieldName is required.";
    }
    return "";
}

//1. Validate Length with value and length
function validateLength($value, $length)
{
    if (strlen($value) <= $length) {
        return false;
    } else {
        return true;
    }
}

//2.validate Email
function isEmail($email)
{
    return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
}

//3.Passwword Hash
function hashPassword(string $userPassword, int $cost = 12): string
{
    // Use Argon2id as the default algorithm for password hashing
    $options = [
        'cost' => $cost, // Adjust cost based on server resources and security needs
    ];

    // Hash the password with the specified options
    $hashedPassword = password_hash($userPassword, PASSWORD_ARGON2ID, $options);

    // Check if hashing was successful
    if ($hashedPassword === false) {
        throw new Exception("Password hashing failed.");
    }

    // Return the hashed password
    return $hashedPassword;
}

function validatePassword($password)
{
    $errors = [];

    // Check if the password has at least 8 characters
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check if the password contains at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }

    // Check if the password contains at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check if the password contains at least one symbol
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must contain at least one symbol (e.g., !@#$%^&*).";
    }

    // Return errors if any, otherwise return true
    return empty($errors) ? true : $errors;
}


//4.Verify Password
function verifyPassword(string $userPassword, string $hashedPassword): bool // Specify parameter and return types
{
    // Attempt to verify the password using password_verify
    $isValid = password_verify($userPassword, $hashedPassword);

    // Return true if password matches, false otherwise
    return $isValid;
}

//5. Validate Phone Number 
function isPhoneNumber($phone)
{
    return preg_match('/^[0-9]{10}+$/', $phone) ? TRUE : FALSE;
}

// 6. Is Not Null
function isNotNull($value)
{
    if ($value == null) {
        return false;
    } else {
        return true;
    }
}

// 7. sanitize input
function sanitizeInput(mixed $value, string $type = 'string'): mixed
{
    if (is_array($value)) {
        return array_map(fn($item) => sanitizeInput($item, $type), $value);
    }

    // Attempt to convert the value to the specified type
    switch ($type) {
        case 'int':
            $value = (int)$value;
            break;
        case 'float':
            $value = (float)$value;
            break;
        case 'email':
        case 'url':
        case 'string':
            $value = (string)$value;
            break;
        default:
            // Optionally, you can throw an exception if an unexpected type is provided
            throw new InvalidArgumentException("Invalid type specified: $type");
    }

    // Trim leading/trailing whitespace
    $value = trim($value);

    // Sanitize based on input type
    switch ($type) {
        case 'string':
            // Remove HTML entities and escape special characters
            $value = htmlspecialchars($value, ENT_QUOTES);
            break;
        case 'int':
            // Sanitize integer (although it should already be an integer)
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            break;
        case 'float':
            // Sanitize float format (although it should already be a float)
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            break;
        case 'email':
            // Validate and sanitize email format
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
            break;
        case 'url':
            // Validate and sanitize URL format
            $value = filter_var($value, FILTER_SANITIZE_URL);
            break;
    }

    return $value;
}



// 8.capitalize first letter after all whitespaces
function capitalizeFirstLetter($value)
{
    return ucwords(strtolower($value));
}

//9.remove whitespaces at start and end
function removeWhitespaces($value)
{
    return trim($value);
}

//10. get current date 
function getCurrentDate()
{
    return date(DATE_FORMAT);
}

//11. get current time in 12 hrs format
function getCurrentTime()
{
    return date(TIME_FORMAT);
}

//12. get current date and time
function getCurrentDateTime()
{
    return date(DATETIME_FORMAT);
}

//13.Show Time stamp.  Eg.: few seconds ago
function showTimeStamp(int|string $timeStamp): string
{
    $timeStamp = strtotime($timeStamp);
    $timeElapsed = time() - $timeStamp;  // Time elapsed since the timestamp

    $timeUnits = [
        // Units in descending order of magnitude
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    ];

    foreach ($timeUnits as $unit => $secondsInUnit) {
        if ($timeElapsed >= $secondsInUnit) {
            $numberOfUnits = floor($timeElapsed / $secondsInUnit);
            return "$numberOfUnits {$unit}" . ($numberOfUnits > 1 ? 's' : '') . ' ago';
        }
    }

    // If no matching unit found, fall back to seconds
    return "a few seconds ago";
}

function convertNumberToWords($number)
{
    $dictionary = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        100000 => 'lakh',
        10000000 => 'crore'
    ];

    if ($number < 0) {
        return 'minus ' . convertNumberToWords(-$number);
    }

    if ($number === 0) {
        return $dictionary[0];
    }

    // Split the number into integer and decimal parts
    $numberParts = explode('.', number_format($number, 2, '.', ''));
    $integerPart = (int) $numberParts[0];
    $fractionPart = isset($numberParts[1]) ? (int) $numberParts[1] : 0;

    $words = [];

    // Convert integer part to words
    $words[] = convertIntegerToWords($integerPart, $dictionary);

    // Add "and" for decimal part
    if ($fractionPart > 0) {
        $words[] = 'and';
        $words[] = convertIntegerToWords($fractionPart, $dictionary) . ' paise';
    }

    return implode(' ', $words);
}

function convertIntegerToWords($number, $dictionary)
{
    if ($number === 0) {
        return $dictionary[0];
    }

    $words = [];

    // Handle crores
    if ($number >= 10000000) {
        $crore = intdiv($number, 10000000);
        $words[] = convertIntegerToWords($crore, $dictionary) . ' crore';
        $number %= 10000000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle lakhs
    if ($number >= 100000) {
        $lakh = intdiv($number, 100000);
        $words[] = convertIntegerToWords($lakh, $dictionary) . ' lakh';
        $number %= 100000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle thousands
    if ($number >= 1000) {
        $thousand = intdiv($number, 1000);
        $words[] = convertIntegerToWords($thousand, $dictionary) . ' thousand';
        $number %= 1000;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle hundreds
    if ($number >= 100) {
        $hundred = intdiv($number, 100);
        $words[] = convertIntegerToWords($hundred, $dictionary) . ' hundred';
        $number %= 100;
        if ($number > 0) {
            $words[] = 'and'; // Add 'and' if there is remaining amount
        }
    }

    // Handle tens and units
    if ($number > 0) {
        if ($number < 20) {
            $words[] = $dictionary[$number];
        } else {
            $tens = intdiv($number, 10) * 10;
            $units = $number % 10;
            $words[] = $dictionary[$tens];
            if ($units > 0) {
                $words[] = $dictionary[$units];
            }
        }
    }

    return implode(' ', $words);
}




// Function to format numbers in Indian style
function formatNumberIndian($number)
{
    // Convert number to string with two decimal places
    $number = number_format($number, 2, '.', '');
    $parts = explode('.', $number);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '00';

    // Apply Indian formatting
    $integerPartLength = strlen($integerPart);

    // Handle cases where the integer part is less than 1000
    if ($integerPartLength <= 3) {
        $formattedIntegerPart = $integerPart;
    } else {
        // Separate out the last three digits
        $lastThreeDigits = substr($integerPart, -3);
        $remainingDigits = substr($integerPart, 0, -3);

        // Format the remaining digits
        $remainingDigits = strrev($remainingDigits);
        $formattedRemainingDigits = preg_replace('/(\d{2})(?=\d)/', '$1,', $remainingDigits);
        $formattedRemainingDigits = strrev($formattedRemainingDigits);

        // Combine parts
        $formattedIntegerPart = $formattedRemainingDigits . ',' . $lastThreeDigits;
    }

    return $formattedIntegerPart . '.' . $decimalPart;
}

function convertUnitAmount($amount, $fromUnit, $toUnit)
{
    $conversionRates = [
        'piece' => 1,
        'tonne' => 1000000, // grams
        'packets' => 1,
        'kg' => 1000, // grams
        'g' => 1,
        'lb' => 453.592, // grams
        'oz' => 28.3495, // grams
        'l' => 1000, // milliliters
        'ml' => 1,
        'm' => 100, // centimeters
        'cm' => 1,
        'mm' => 0.1, // centimeters
        'ft' => 30.48, // centimeters
        'in' => 2.54 // centimeters
    ];

    if (!isset($conversionRates[$fromUnit]) || !isset($conversionRates[$toUnit])) {
        throw new Exception("Invalid unit provided");
    }

    $amountInBaseUnit = $amount / $conversionRates[$fromUnit];
    $convertedAmount = $amountInBaseUnit * $conversionRates[$toUnit];

    return $convertedAmount;
}

function convertUnitQuantity($quantity, $fromUnit, $toUnit)
{
    $conversionRates = [
        'piece' => 1,
        'tonne' => 1000000, // grams
        'packets' => 1,
        'kg' => 1000, // grams
        'g' => 1, // grams
        'lb' => 453.592, // grams
        'oz' => 28.3495, // grams
        'l' => 1000, // milliliters
        'ml' => 1, // milliliters
        'm' => 100, // centimeters
        'cm' => 1, // centimeters
        'mm' => 0.1, // centimeters
        'ft' => 30.48, // centimeters
        'in' => 2.54 // centimeters
    ];

    // Validate units
    if (!isset($conversionRates[$fromUnit]) || !isset($conversionRates[$toUnit])) {
        throw new Exception("Invalid unit provided");
    }

    // Convert the quantity to the base unit
    $quantityInBaseUnit = $quantity * $conversionRates[$fromUnit];

    // Convert from the base unit to the target unit
    $convertedQuantity = $quantityInBaseUnit / $conversionRates[$toUnit];

    return $convertedQuantity;
}


//15. get rounded value
function getRoundedValue($value)
{
    return round($value, 2);
}
