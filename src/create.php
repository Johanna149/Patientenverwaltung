<?php

require 'db.php';
require_once 'validation.php';

$fehler = [];
$sv = $vorname = $nachname = $geburtsdatum = $telefon = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sv           = trim($_POST['sv_nummer'] ?? '');
    $vorname      = trim($_POST['vorname'] ?? '');
    $nachname     = trim($_POST['nachname'] ?? '');
    $geburtsdatum = trim($_POST['geburtsdatum'] ?? '');
    $telefon      = trim($_POST['telefon'] ?? '');

    if (!isValidSvNummer($sv)) {
        $fehler[] = 'Die SV-Nummer muss genau 10 Ziffern haben.';
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
    if (!isValidDate($geburtsdatum)) {
        $fehler[] = 'Bitte ein gültiges Geburtsdatum angeben.';
    } elseif ($geburtsdatum > date('Y-m-d')) {
        $fehler[] = 'Das Geburtsdatum darf nicht in der Zukunft liegen.';
    }
    if (mb_strlen($telefon) > 20) {
        $fehler[] = 'Die Telefonnummer darf höchstens 20 Zeichen haben.';
    }

    if (!$fehler) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO patient (sv_nummer, vorname, nachname, geburtsdatum, telefon)
                 VALUES (:sv_nummer, :vorname, :nachname, :geburtsdatum, :telefon)'
            );

            $stmt->execute([
                'sv_nummer'    => $sv,
                'vorname'      => $vorname,
                'nachname'     => $nachname,
                'geburtsdatum' => $geburtsdatum,
                'telefon'      => $telefon !== '' ? $telefon : null,
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            // 1062 = doppelter Eintrag (SV-Nummer existiert schon)
            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $fehler[] = 'Diese SV-Nummer existiert bereits.';
            } else {
                throw $e;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Patient anlegen</title>
</head>

<body>

    <h1>Neuer Patient</h1>

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
                name="sv_nummer"
                placeholder="SV-Nummer (10 Ziffern)"
                pattern="[0-9]{10}"
                maxlength="10"
                value="<?= htmlspecialchars($sv) ?>"
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
            <label for="geburtsdatum">Geburtsdatum:</label>
            <input
                type="date"
                id="geburtsdatum"
                name="geburtsdatum"
                value="<?= htmlspecialchars($geburtsdatum) ?>"
                required
            >
        </p>

        <p>
            <input
                type="tel"
                name="telefon"
                placeholder="Telefonnummer"
                maxlength="20"
                value="<?= htmlspecialchars($telefon) ?>"
            >
        </p>

        <button type="submit">Speichern</button>

        <a href="index.php">Abbrechen</a>

    </form>

</body>

</html>