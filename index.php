<?php
$file = 'profile.json';
$message = '';
$messageType = '';

if (file_exists($file)) {
    $interests = json_decode(file_get_contents($file), true);
} else {
    $interests = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['new_interest'])) {

        $newInterest = trim($_POST['new_interest']);

        if ($newInterest === '') {
            $message = "Pole nesmí být prázdné.";
            $messageType = "error";
        } 
        else {

            $lowerInterests = array_map('strtolower', $interests);

            if (in_array(strtolower($newInterest), $lowerInterests)) {
                $message = "Tento zájem už existuje.";
                $messageType = "error";
            } 
            else {

                $interests[] = $newInterest;

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
<?php echo '<link rel= "stylesheet" href= "style.css">'; ?>
</head>
<body>

<h1>Seznam zájmů</h1>

<?php if ($message): ?>
    <p style="color: <?= $messageType === 'error' ? 'red' : 'green' ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

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