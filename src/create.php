
<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        'INSERT INTO patient (sv_nummer, vorname, nachname, geburtsdatum, telefon)
         VALUES (:sv_nummer, :vorname, :nachname, :geburtsdatum, :telefon)'
    );

    $stmt->execute([
        'sv_nummer'    => $_POST['sv_nummer'],
        'vorname'      => $_POST['vorname'],
        'nachname'     => $_POST['nachname'],
        'geburtsdatum' => $_POST['geburtsdatum'],
        'telefon'      => $_POST['telefon'],
    ]);

    header('Location: index.php');
    exit;
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

    <form method="post">

        <p>
            <input
                type="text"
                name="sv_nummer"
                placeholder="SV-Nummer (10 Ziffern)"
                pattern="[0-9]{10}"
                maxlength="10"
                required
            >
        </p>

        <p>
            <input
                type="text"
                name="vorname"
                placeholder="Vorname"
                required
            >
        </p>

        <p>
            <input
                type="text"
                name="nachname"
                placeholder="Nachname"
                required
            >
        </p>

        <p>
            <label for="geburtsdatum">Geburtsdatum:</label>
            <input
                type="date"
                id="geburtsdatum"
                name="geburtsdatum"
                required
            >
        </p>

        <p>
            <input
                type="tel"
                name="telefon"
                placeholder="Telefonnummer"
            >
        </p>

        <button type="submit">Speichern</button>

        <a href="index.php">Abbrechen</a>

    </form>

</body>

</html>
