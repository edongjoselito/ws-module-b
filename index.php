<?php
include("db.php"); // ikonekta ang database

// default na language ay English
$lang = "en";

// kung may ?lang=fr sa URL, gawing French ang description
if (isset($_GET['lang']) && $_GET['lang'] == "fr") {
    $lang = "fr";
}

// kunin ang lahat ng products na hindi hidden
// at galing sa company na hindi deactivated
$stmt = $pdo->query("
    SELECT p.*, c.company_name
    FROM products p
    JOIN companies c ON p.company_id = c.id
    WHERE p.is_hidden = 0
    AND c.is_deactivated = 0
    ORDER BY p.id DESC
");

// gawing array ang mga nakuha na products
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Public Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- tawagin ang external CSS file -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- itaas na menu -->
    <div class="topbar">
        <b>Public Products</b>
        <span>
            <a href="gtin-bulk-verify.php">Bulk GTIN</a>
            <a href="login">Admin</a>
        </span>
    </div>

    <div class="container">
        <h2>All Products</h2>

        <?php if (!empty($products)) { ?>
            <div class="grid">

                <?php foreach ($products as $p) { ?>
                    <?php
                    // default image kung walang naka-upload na product image
                    $image = "images/no-image.avif";

                    // tingnan kung may image_path at kung existing talaga ang file
                    if (!empty($p['image_path'])) {
                        $filePath = __DIR__ . '/' . ltrim($p['image_path'], '/');

                        if (file_exists($filePath)) {
                            $image = ltrim($p['image_path'], '/');
                        }
                    }
                    ?>

                    <div class="card">

                        <!-- pagpili ng language -->
                        <div class="lang">
                            <a href="?lang=en">EN</a> /
                            <a href="?lang=fr">FR</a>
                        </div>

                        <!-- pangalan ng company -->
                        <div class="company">
                            <?= htmlspecialchars($p['company_name']) ?>
                        </div>

                        <!-- larawan ng product -->
                        <img src="<?= htmlspecialchars($image) ?>"
                            alt="product"
                            onerror="this.src='images/no-image.avif';">

                        <!-- GTIN ng product -->
                        <div class="gtin">
                            GTIN: <?= htmlspecialchars($p['gtin']) ?>
                        </div>

                        <!-- pangalan ng product -->
                        <div class="name_en">
                            Name: <?= htmlspecialchars($p['name_en']) ?>
                        </div>

                        <!-- description depende sa napiling language -->
                        <div class="desc">
                            <?php
                            if ($lang == "fr") {
                                echo htmlspecialchars($p['description_fr'] ?? '');
                            } else {
                                echo htmlspecialchars($p['description_en'] ?? '');
                            }
                            ?>
                        </div>

                        <!-- timbang ng product -->
                        <div class="weight">
                            Weight: <?= htmlspecialchars($p['gross_weight']) . ' ' . htmlspecialchars($p['weight_unit']) ?><br>
                            Net: <?= htmlspecialchars($p['net_weight']) . ' ' . htmlspecialchars($p['weight_unit']) ?>
                        </div>

                    </div>
                <?php } ?>

            </div>
        <?php } else { ?>

            <!-- lalabas ito kung walang product na nahanap -->
            <div class="empty">No products found.</div>

        <?php } ?>

    </div>

</body>

</html>