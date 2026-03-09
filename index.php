<?php
session_start();

$file = 'profile.json';
$message = '';
$messageType = 'info';

if (file_exists($file)) {
    $interests = json_decode(file_get_contents($file), true) ?? [];
} else {
    $interests = [];
}


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'add';

    
    if ($action === 'add' && isset($_POST['new_interest'])) {

        $newInterest = trim($_POST['new_interest']);

        if ($newInterest === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            $lowerInterests = array_map('strtolower', $interests);

            if (in_array(strtolower($newInterest), $lowerInterests)) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $interests[] = $newInterest;
                file_put_contents($file, json_encode($interests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $_SESSION['message'] = 'Zájem byl úspěšně přidán.';
                $_SESSION['messageType'] = 'success';
            }
        }

        header("Location: index.php");
        exit;
    }

    
    if ($action === 'edit' && isset($_POST['edit_index']) && isset($_POST['edit_value'])) {

        $index = (int)$_POST['edit_index'];
        $newValue = trim($_POST['edit_value']);

        if ($newValue === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } 
        elseif (!isset($interests[$index])) {
            $_SESSION['message'] = 'Neplatný zájem.';
            $_SESSION['messageType'] = 'error';
        } 
        else {
            $lowerInterests = array_map('strtolower', $interests);
            $lowerNew = strtolower($newValue);

           
            $duplicate = false;
            foreach ($lowerInterests as $i => $val) {
                if ($i !== $index && $val === $lowerNew) {
                    $duplicate = true;
                    break;
                }
            }

            if ($duplicate) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $interests[$index] = $newValue;
                file_put_contents($file, json_encode($interests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $_SESSION['message'] = 'Zájem byl upraven.';
                $_SESSION['messageType'] = 'success';
            }
        }

        header("Location: index.php");
        exit;
    }

    
    if ($action === 'delete' && isset($_POST['delete_index'])) {

        $index = (int)$_POST['delete_index'];

        if (isset($interests[$index])) {
            unset($interests[$index]);
            $interests = array_values($interests); 

            file_put_contents($file, json_encode($interests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $_SESSION['message'] = 'Zájem byl odstraněn.';
            $_SESSION['messageType'] = 'success';
        }

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Moje zájmy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Seznam zájmů</h1>

<?php if ($message): ?>
    <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="text" name="new_interest" placeholder="Nový zájem..." required>
    <input type="hidden" name="action" value="add">
    <button type="submit">Přidat</button>
</form>

<h2>Aktuální zájmy:</h2>

<?php if (empty($interests)): ?>
    <p><em>zatím žádné zájmy</em></p>
<?php else: ?>
    <ul>
    <?php foreach ($interests as $i => $interest): ?>
        <li>
            <form method="post" class="edit-form" style="display:inline;">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_index" value="<?= $i ?>">
                <input type="text" name="edit_value" value="<?= htmlspecialchars($interest) ?>" required>
                <button type="submit">Upravit</button>
            </form>

            <form method="post" style="display:inline;" 
                  onsubmit="return confirm('Opravdu odstranit <?= htmlspecialchars($interest, ENT_QUOTES) ?>?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="delete_index" value="<?= $i ?>">
                <button type="submit">×</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>