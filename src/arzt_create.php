<?php

require_once 'db.php';

$fehler = '';

$titel = '';
$vorname = '';
$nachname = '';
$fachrichtung = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eingaben aus dem Formular übernehmen
    $titel = trim($_POST['titel'] ?? '');
    $vorname = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $fachrichtung = trim($_POST['fachrichtung'] ?? '');

    // Pflichtfelder überprüfen
    if (
        $titel === '' ||
        $vorname === '' ||
        $nachname === '' ||
        $fachrichtung === ''
    ) {
        $fehler = 'Bitte fülle alle Pflichtfelder aus.';
    }

    // Maximale Länge der Eingaben überprüfen
    elseif (
        strlen($titel) > 30 ||
        strlen($vorname) > 50 ||
        strlen($nachname) > 50 ||
        strlen($fachrichtung) > 100
    ) {
        $fehler = 'Eine Eingabe ist zu lang.';
    }

    else {
        // Neuen Arzt in der Datenbank speichern
        $stmt = $pdo->prepare(
            'INSERT INTO arzt
            (titel, vorname, nachname, fachrichtung)
            VALUES
            (:titel, :vorname, :nachname, :fachrichtung)'
        );

        $stmt->execute([
            'titel' => $titel,
            'vorname' => $vorname,
            'nachname' => $nachname,
            'fachrichtung' => $fachrichtung,
        ]);

        header('Location: arzt_index.php');
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

    <?php if ($fehler !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($fehler) ?>
        </p>
    <?php endif; ?>

    <form method="post">

        <p>
            <input
                type="text"
                name="titel"
                placeholder="Titel"
                maxlength="30"
                value="<?= htmlspecialchars($titel) ?>"
                required
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

        <a href="arzt_index.php">Abbrechen</a>

    </form>

</body>

</html>
