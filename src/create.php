<?php

require_once 'db.php';

$fehler = '';

$sv_nummer = '';
$vorname = '';
$nachname = '';
$geburtsdatum = '';
$telefon = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eingaben aus dem Formular übernehmen
    $sv_nummer = trim($_POST['sv_nummer'] ?? '');
    $vorname = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $geburtsdatum = trim($_POST['geburtsdatum'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');

    // Pflichtfelder überprüfen
    if (
        $sv_nummer === '' ||
        $vorname === '' ||
        $nachname === '' ||
        $geburtsdatum === ''
    ) {
        $fehler = 'Bitte fülle alle Pflichtfelder aus.';
    }

    // SV-Nummer muss genau 10 Ziffern enthalten
    elseif (!preg_match('/^[0-9]{10}$/', $sv_nummer)) {
        $fehler = 'Die SV-Nummer muss genau 10 Ziffern enthalten.';
    }

    // Geburtsdatum überprüfen
    elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $geburtsdatum) ||
        !checkdate(
            (int) substr($geburtsdatum, 5, 2),
            (int) substr($geburtsdatum, 8, 2),
            (int) substr($geburtsdatum, 0, 4)
        )
    ) {
        $fehler = 'Bitte gib ein gültiges Geburtsdatum ein.';
    }

    // Länge der Eingaben überprüfen
    elseif (
        strlen($vorname) > 50 ||
        strlen($nachname) > 50 ||
        strlen($telefon) > 20
    ) {
        $fehler = 'Eine Eingabe ist zu lang.';
    }

    else {
        // Überprüfen, ob die SV-Nummer bereits existiert
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM patient WHERE sv_nummer = :sv_nummer'
        );
        $stmt->execute(['sv_nummer' => $sv_nummer]);

        if ($stmt->fetchColumn() > 0) {
            $fehler = 'Diese SV-Nummer ist bereits vorhanden.';
        } else {
            // Neuen Patienten in der Datenbank speichern
            $stmt = $pdo->prepare(
                'INSERT INTO patient
                (sv_nummer, vorname, nachname, geburtsdatum, telefon)
                VALUES
                (:sv_nummer, :vorname, :nachname, :geburtsdatum, :telefon)'
            );

            $stmt->execute([
                'sv_nummer' => $sv_nummer,
                'vorname' => $vorname,
                'nachname' => $nachname,
                'geburtsdatum' => $geburtsdatum,
                'telefon' => $telefon !== '' ? $telefon : null,
            ]);

            header('Location: index.php');
            exit;
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

    <?php if ($fehler !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($fehler) ?>
        </p>
    <?php endif; ?>

    <form method="post">

        <p>
            <input
                type="text"
                name="sv_nummer"
                placeholder="SV-Nummer (10 Ziffern)"
                pattern="[0-9]{10}"
                maxlength="10"
                value="<?= htmlspecialchars($sv_nummer) ?>"
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
