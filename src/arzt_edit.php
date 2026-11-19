<?php

require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? '';

if (!ctype_digit((string)$id)) {
    die('Ungültige Arzt-ID.');
}

$stmt = $pdo->prepare('SELECT * FROM arzt WHERE id = :id');
$stmt->execute(['id' => $id]);
$a = $stmt->fetch();

if (!$a) {
    die('Arzt nicht gefunden.');
}

$fehler = [];


$titel = $a['titel'] ?? '';
$vorname = $a['vorname'];
$nachname = $a['nachname'];
$fachrichtung = $a['fachrichtung'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titel = trim($_POST['titel'] ?? '');
    $vorname = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $fachrichtung = trim($_POST['fachrichtung'] ?? '');

    if (mb_strlen($titel) > 30) {
        $fehler[] = 'Der Titel darf höchstens 30 Zeichen haben.';
    }

    if ($vorname === '') {
        $fehler[] = 'Vorname ist ein Pflichtfeld.';
    } elseif (mb_strlen($vorname) > 50) {
        $fehler[] = 'Der Vorname darf höchstens 50 Zeichen haben.';
    }

    if ($nachname === '') {
        $fehler[] = 'Nachname ist ein Pflichtfeld.';
    } elseif (mb_strlen($nachname) > 50) {
        $fehler[] = 'Der Nachname darf höchstens 50 Zeichen haben.';
    }

    
    if ($fachrichtung === '') {
        $fehler[] = 'Fachrichtung ist ein Pflichtfeld.';
    } elseif (mb_strlen($fachrichtung) > 100) {
        $fehler[] = 'Die Fachrichtung darf höchstens 100 Zeichen haben.';
    }

    if (!$fehler) {
        $stmt = $pdo->prepare(
            'UPDATE arzt
             SET titel = :titel,
                 vorname = :vorname,
                 nachname = :nachname,
                 fachrichtung = :fachrichtung
             WHERE id = :id'
        );

        $stmt->execute([
            'titel'        => $titel,
            'vorname'      => $vorname,
            'nachname'     => $nachname,
            'fachrichtung' => $fachrichtung,
            'id'           => $id
        ]);

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="de" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Arzt:in bearbeiten</title>
</head>

<body>

    <h1>Arzt:in bearbeiten</h1>

    <?php if ($fehler): ?>
        <ul style="color:red;">
            <?php foreach ($fehler as $f): ?>
                <li><?= htmlspecialchars($f) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id) ?>">
        <p>
            <label>Titel<br>
                <input
                    type="text"
                    name="titel"
                    maxlength="30"
                    value="<?= htmlspecialchars($titel) ?>"
                >
            </label>
        </p>

        <p>
            <label>Vorname<br>
                <input
                    type="text"
                    name="vorname"
                    maxlength="50"
                    value="<?= htmlspecialchars($vorname) ?>"
                    required
                >
            </label>
        </p>

        <p>
            <label>Nachname<br>
                <input
                    type="text"
                    name="nachname"
                    maxlength="50"
                    value="<?= htmlspecialchars($nachname) ?>"
                    required
                >
            </label>
        </p>

        <p>
            <label>Fachrichtung<br>
                <input
                    type="text"
                    name="fachrichtung"
                    maxlength="100"
                    value="<?= htmlspecialchars($fachrichtung) ?>"
                    required
                >
            </label>
        </p>

        <button type="submit">Aktualisieren</button>
        <a href="index.php">Abbrechen</a>

    </form>
    

</body>
</html>