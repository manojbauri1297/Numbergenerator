<?php
// Enable error reporting
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $countryCode = $_POST['country_code'] ?? '';
    $areaCode = $_POST['area_code'] ?? '';
    $digitCount = (int)($_POST['digit_count'] ?? 8);
    $targetQuantity = (int)($_POST['quantity'] ?? 100);
    $validate = isset($_POST['validate']);

    $validNumbers = [];
    $generatedSet = [];
    $batchMultiplier = 2;
    $maxAttempts = 15;
    $attempt = 0;

    $maxPossible = pow(10, $digitCount);
    if ($targetQuantity > $maxPossible) {
        die("❌ Cannot generate $targetQuantity unique numbers with $digitCount digits.");
    }

    while (count($validNumbers) < $targetQuantity && $attempt < $maxAttempts) {
        $attempt++;
        $remaining = $targetQuantity - count($validNumbers);
        $generateCount = min($remaining * $batchMultiplier, $maxPossible - count($generatedSet));

        file_put_contents('debug.log', "Attempt $attempt: Need $remaining more, generating $generateCount\n", FILE_APPEND);

        if ($generateCount <= 0) break;

        $batch = [];

        while (count($batch) < $generateCount) {
            $randomDigits = '';
            for ($j = 0; $j < $digitCount; $j++) {
                $randomDigits .= mt_rand(0, 9);
            }

            if (isset($generatedSet[$randomDigits])) continue;
            $generatedSet[$randomDigits] = true;

            $formattedDigits = implode(' ', str_split($randomDigits, 2));
            $fullNumber = preg_replace('/\s+/', '', "$countryCode$areaCode$randomDigits");

            if ($validate) {
                file_put_contents('debug.log', "Validating: $fullNumber\n", FILE_APPEND);

                if (!isValidPhoneNumber($fullNumber)) {
                    file_put_contents('debug.log', "❌ Invalid: $fullNumber\n", FILE_APPEND);
                    continue;
                }

                file_put_contents('debug.log', "✅ Valid: $fullNumber\n", FILE_APPEND);
            }

            $batch[] = "$countryCode $areaCode $formattedDigits";

            if (count($validNumbers) + count($batch) >= $targetQuantity) break;
        }

        $validNumbers = array_merge($validNumbers, $batch);
    }

    if (empty($validNumbers)) {
        file_put_contents('debug.log', "❌ No valid numbers generated after $attempt attempts.\n", FILE_APPEND);
        die("❌ Failed to generate valid phone numbers.");
    }

    $filename = "phone_numbers_" . date("Y-m-d_H-i") . ".csv";
    $file = fopen($filename, "w");
    fputcsv($file, ['Phone Number']);

    foreach (array_slice($validNumbers, 0, $targetQuantity) as $number) {
        fputcsv($file, [$number]);
    }

    fclose($file);
    file_put_contents('debug.log', "✅ File generated: $filename with " . count($validNumbers) . " numbers.\n", FILE_APPEND);
    header("Location: index.php?success=1&file=$filename");
    exit;
}

function isValidPhoneNumber($number) {
    $username = 'softmuneeb';
    $password = 'Validate123@';
    $cleanNumber = preg_replace('/\D/', '', $number);

    $url = "https://api.numberportabilitylookup.com/npl?user=" . urlencode($username)
         . "&pass=" . urlencode($password)
         . "&msisdn=" . urlencode($cleanNumber)
         . "&format=json";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        file_put_contents('debug.log', "⚠️ cURL error: " . curl_error($ch) . "\n", FILE_APPEND);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    if ($statusCode !== 200 || !$response) {
        file_put_contents('debug.log', "⚠️ HTTP status $statusCode or empty response.\n", FILE_APPEND);
        return false;
    }

    $result = json_decode($response, true);


    if (isset($result[0]['validnumber']) && ($result[0]['validnumber'] === true || $result[0]['validnumber'] === "true")) {
        return true;
    }

    file_put_contents('debug.log', "⚠️ Invalid API response for $number: $response\n", FILE_APPEND);
    return false;
}
