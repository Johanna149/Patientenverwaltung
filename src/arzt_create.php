<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        'INSERT INTO arzt (titel,vorname,nachname,fachrichtung)
         VALUES (:titel, :vorname, :nachname, :fachrichtung)'
    );

    $stmt->execute([
        'titel'        => $_POST['titel'],
        'vorname'      => $_POST['vorname'],
        'nachname'     => $_POST['nachname'],
        'fachrichtung' => $_POST['fachrichtung'],
    ]);


    header('Location: index.php');
    exit;
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

    <form method="post">

    
        <p>
            <input
                type="text"
                name="titel"
                placeholder="Titel"
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
            <input
                type="text"
                name="fachrichtung"
                placeholder="Fachrichtung"
                required
            >
        </p>


        <button type="submit">Speichern</button>

        <a href="index.php">Abbrechen</a>

    </form>

</body>

</html>