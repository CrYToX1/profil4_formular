<?php

$file = 'profile.json';
$message = '';
$messageType = '';

// Načtení existujících zájmů
if (file_exists($file)) {
    $interests = json_decode(file_get_contents($file), true);
} else {
    $interests = [];
}

// Zpracování formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['new_interest'])) {

        $newInterest = trim($_POST['new_interest']);

        // 1️⃣ prázdné pole
        if ($newInterest === '') {
            $message = "Pole nesmí být prázdné.";
            $messageType = "error";
        } 
        else {

            // 2️⃣ kontrola duplicity (bez ohledu na velikost písmen)
            $lowerInterests = array_map('strtolower', $interests);

            if (in_array(strtolower($newInterest), $lowerInterests)) {
                $message = "Tento zájem už existuje.";
                $messageType = "error";
            } 
            else {
                // 3️⃣ přidání zájmu
                $interests[] = $newInterest;

                // 4️⃣ uložení do JSON
                file_put_contents($file, json_encode($interests, JSON_PRETTY_PRINT));

                $message = "Zájem byl úspěšně přidán.";
                $messageType = "success";
            }
        }
    }
}
?>

<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<title>Moje zájmy</title>
</head>
<body>

<h1>Seznam zájmů</h1>

<!-- Zobrazení hlášky -->
<?php if ($message): ?>
    <p style="color: <?= $messageType === 'error' ? 'red' : 'green' ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<!-- Formulář -->
<form method="POST">
    <input type="text" name="new_interest" required>
    <button type="submit">Přidat zájem</button>
</form>

<h2>Aktuální zájmy:</h2>
<ul>
    <?php foreach ($interests as $interest): ?>
        <li><?= htmlspecialchars($interest) ?></li>
    <?php endforeach; ?>
</ul>

</body>
</html>