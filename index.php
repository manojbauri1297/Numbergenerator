<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phone Number Generator</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #d9e4f5);
            height: 100vh;
        }
        .card {
            border-radius: 1rem;
        }
        h2 {
            font-weight: bold;
            color: #2c3e50;
        }
        .error {
            color: red;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5">
            <h2 class="mb-4 text-center">📱 Phone Number Generator</h2>
            <div class="card p-4 shadow">
                <form method="POST" action="generate.php" id="phoneForm">
                    <div class="mb-3">
                        <label class="form-label">🌍 Country Code</label>
                        <input type="text" class="form-control" name="country_code" id="country_code" placeholder="+33">
                        <span class="error" id="error_country_code"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">📞 Area/Mobile Code</label>
                        <input type="text" class="form-control" name="area_code" id="area_code" placeholder="6 or 621">
                        <span class="error" id="error_area_code"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔢 Number of Random Digits</label>
                        <input type="number" class="form-control" name="digit_count" id="digit_count" min="1" max="12">
                        <span class="error" id="error_digit_count"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🧾 Quantity to Generate</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" min="1" max="100000">
                        <span class="error" id="error_quantity"></span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100" id="generateBtn">⚙️ Generate</button>
                        <a href="index.php" class="btn btn-outline-secondary w-100">🧹 Clear</a>
                    </div>
                </form>

                <?php if (isset($_GET['success']) && isset($_GET['file'])): ?>
                <div class="alert alert-success mt-4 text-center">
                ✅ Phone numbers generated successfully.<br>
                <a href="<?= htmlspecialchars($_GET['file']) ?>" class="btn btn-success btn-sm mt-2" download>⬇️ Download CSV</a>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
        document.getElementById("phoneForm").addEventListener("submit", function (e) {
            let isValid = true;

            document.querySelectorAll('.error').forEach(span => span.textContent = '');
            const countryCode = document.getElementById("country_code").value.trim();
            const areaCode = document.getElementById("area_code").value.trim();
            const digitCount = document.getElementById("digit_count").value.trim();
            const quantity = document.getElementById("quantity").value.trim();
            if (!/^\+?\d{1,4}$/.test(countryCode)) 
            {
                document.getElementById("error_country_code").textContent = "Enter valid country code (e.g., +91)";
                isValid = false;
            }

            if (!/^\d{1,5}$/.test(areaCode)) 
            {
                document.getElementById("error_area_code").textContent = "Enter a valid area/mobile code";
                isValid = false;
            }
            if (!/^\d+$/.test(digitCount) || digitCount < 1 || digitCount > 12) {
                document.getElementById("error_digit_count").textContent = "Enter a number between 1 and 12";
                isValid = false;
            }
            if (!/^\d+$/.test(quantity) || quantity < 1 || quantity > 100000) {
                document.getElementById("error_quantity").textContent = "Enter a number between 1 and 100000";
                isValid = false;
            }
            if (isValid) {
                const generateBtn = document.getElementById("generateBtn");
                generateBtn.textContent = "⚙️ Generating...";
                generateBtn.disabled = true;
            } else {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
