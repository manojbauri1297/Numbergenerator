<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $countryCode = $_POST['country_code'] ?? '';
    $areaCode = $_POST['area_code'] ?? '';
    $digitCount = (int)($_POST['digit_count'] ?? 8);
    $quantity = (int)($_POST['quantity'] ?? 100);

    $numbers = [];
    $generatedSet = [];

    $maxPossible = pow(10, $digitCount);
    if ($quantity > $maxPossible) {
        die("❌ Cannot generate $quantity unique numbers with $digitCount digits.");
    }

    while (count($numbers) < $quantity) {
        $randomDigits = '';
        for ($j = 0; $j < $digitCount; $j++) {
            $randomDigits .= mt_rand(0, 9);
        }
        if (isset($generatedSet[$randomDigits])) {
            continue;
        }

        $generatedSet[$randomDigits] = true;
        $formattedDigits = implode(' ', str_split($randomDigits, 2));
        $numbers[] = "$countryCode $areaCode $formattedDigits";
    }

    $filename = "phone_numbers_" . date("Y-m-d_h_i") . ".csv";
    $file = fopen($filename, "w");

    fputcsv($file, ['Phone Number']);

    foreach ($numbers as $number) {
        fputcsv($file, [$number]);
    }

    fclose($file);
    header("Location: index.php?success=1&file=$filename");

    exit;
}
