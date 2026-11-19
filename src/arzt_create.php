<?php

require 'db.php';

$fehler = [];
$titel = $vorname = $nachname = $fachrichtung = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titel        = trim($_POST['titel'] ?? '');
    $vorname      = trim($_POST['vorname'] ?? '');
    $nachname     = trim($_POST['nachname'] ?? '');
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
            'INSERT INTO arzt (titel,vorname,nachname,fachrichtung)
             VALUES (:titel, :vorname, :nachname, :fachrichtung)'
        );

        $stmt->execute([
            'titel'        => $titel,
            'vorname'      => $vorname,
            'nachname'     => $nachname,
            'fachrichtung' => $fachrichtung,
        ]);

        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Arzt anlegen</title>
</head>

<body>

    <h1>Neuer Arzt</h1>

    <?php if ($fehler): ?>
        <ul style="color:red;">
            <?php foreach ($fehler as $f): ?>
                <li><?= htmlspecialchars($f) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">


        <p>
            <input
                type="text"
                name="titel"
                placeholder="Titel"
                maxlength="30"
                value="<?= htmlspecialchars($titel) ?>"
            >
        </p>

        <p>
            <input
                type="text"
                name="vorname"
                placeholder="Vorname"
                maxlength="50"
                value="<?= htmlspecialchars($vorname) ?>"
                required
            >
        </p>


        <p>
            <input
                type="text"
                name="nachname"
                placeholder="Nachname"
                maxlength="50"
                value="<?= htmlspecialchars($nachname) ?>"
                required
            >
        </p>

        <p>
            <input
                type="text"
                name="fachrichtung"
                placeholder="Fachrichtung"
                maxlength="100"
                value="<?= htmlspecialchars($fachrichtung) ?>"
                required
            >
        </p>


        <button type="submit">Speichern</button>

        <a href="index.php">Abbrechen</a>

    </form>

</body>

</html>