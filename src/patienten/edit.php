<?php

require_once __DIR__ . '/db.php';

// Primärschlüssel ist die SV-Nummer
$sv = trim($_GET['sv'] ?? '');

if (!preg_match('/^[0-9]{10}$/', $sv)) {
    die('Ungültige SV-Nummer.');
}

// Bestehenden Patienten laden
$stmt = $pdo->prepare('SELECT * FROM patient WHERE sv_nummer = :sv');
$stmt->execute(['sv' => $sv]);
$p = $stmt->fetch();

if (!$p) {
    die('Patient:in nicht gefunden.');
}

$fehler = [];

$vorname = $p['vorname'];
$nachname = $p['nachname'];
$geburtstag = $p['geburtsdatum'];
$telefon = $p['telefon'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $vorname = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $geburtstag = trim($_POST['geburtsdatum'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');

    // Pflichtfelder überprüfen
    if ($vorname === '') {
        $fehler[] = 'Vorname ist ein Pflichtfeld.';
    }

    if ($nachname === '') {
        $fehler[] = 'Nachname ist ein Pflichtfeld.';
    }

    if ($geburtstag === '') {
        $fehler[] = 'Geburtsdatum ist ein Pflichtfeld.';
    }

    // Maximale Länge der Eingaben überprüfen
    if (strlen($vorname) > 50) {
        $fehler[] = 'Der Vorname darf maximal 50 Zeichen enthalten.';
    }

    if (strlen($nachname) > 50) {
        $fehler[] = 'Der Nachname darf maximal 50 Zeichen enthalten.';
    }

    if (strlen($telefon) > 20) {
        $fehler[] = 'Die Telefonnummer darf maximal 20 Zeichen enthalten.';
    }

    // Geburtsdatum überprüfen
    if ($geburtstag !== '') {

        $d = DateTime::createFromFormat('!Y-m-d', $geburtstag);

        if (!$d || $d->format('Y-m-d') !== $geburtstag) {
            $fehler[] = 'Bitte ein gültiges Geburtsdatum angeben.';
        } elseif ($d > new DateTime('today')) {
            $fehler[] = 'Das Geburtsdatum darf nicht in der Zukunft liegen.';
        }
    }

    // Eingaben im Formular behalten
    $p['vorname'] = $vorname;
    $p['nachname'] = $nachname;
    $p['geburtsdatum'] = $geburtstag;
    $p['telefon'] = $telefon;

    if (!$fehler) {

        // Patientendaten aktualisieren
        $stmt = $pdo->prepare(
            'UPDATE patient
             SET vorname = :vorname,
                 nachname = :nachname,
                 geburtsdatum = :geburtsdatum,
                 telefon = :telefon
             WHERE sv_nummer = :sv'
        );

        $stmt->execute([
            'vorname' => $vorname,
            'nachname' => $nachname,
            'geburtsdatum' => $geburtstag,
            'telefon' => $telefon !== '' ? $telefon : null,
            'sv' => $sv
        ]);

        header('Location: ../index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="de" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Patient:in bearbeiten</title>
</head>

<body>

    <h1>Patient:in bearbeiten</h1>

    <?php if ($fehler): ?>
        <ul style="color:red;">
            <?php foreach ($fehler as $f): ?>
                <li><?= htmlspecialchars($f) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">

        <p>
            <label>SV-Nummer (nicht änderbar)<br>
                <input
                    type="text"
                    value="<?= htmlspecialchars($p['sv_nummer']) ?>"
                    readonly
                >
            </label>
        </p>

        <p>
            <label>Vorname<br>
                <input
                    type="text"
                    name="vorname"
                    maxlength="50"
                    value="<?= htmlspecialchars($p['vorname']) ?>"
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
                    value="<?= htmlspecialchars($p['nachname']) ?>"
                    required
                >
            </label>
        </p>

        <p>
            <label>Geburtsdatum<br>
                <input
                    type="date"
                    name="geburtsdatum"
                    value="<?= htmlspecialchars($p['geburtsdatum']) ?>"
                    max="<?= date('Y-m-d') ?>"
                    required
                >
            </label>
        </p>

        <p>
            <label>Telefon<br>
                <input
                    type="text"
                    name="telefon"
                    maxlength="20"
                    value="<?= htmlspecialchars($p['telefon'] ?? '') ?>"
                >
            </label>
        </p>

        <button type="submit">Aktualisieren</button>

        <a href="../index.php">Abbrechen</a>

    </form>

</body>

</html>