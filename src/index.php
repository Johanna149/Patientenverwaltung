<?php

require 'db.php';

// Suche per GET (F6): Name oder SV-Nummer
$suche = trim($_GET['q'] ?? '');

if ($suche !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM patient
         WHERE vorname LIKE :q
            OR nachname LIKE :q
            OR CONCAT(vorname, " ", nachname) LIKE :q
            OR sv_nummer LIKE :q
         ORDER BY nachname, vorname'
    );
    $stmt->execute(['q' => '%' . $suche . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT * FROM patient ORDER BY nachname, vorname'
    );
}

$patienten = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="de" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Patient:innen</title>
</head>

<body>

<<<<<<< HEAD
    <h1>Arzt:innen</h1>

    <p>
<<<<<<< Updated upstream
        <a href="arzt_create.php">Neuer Arzt:in</a>
    </p>
=======
        <a href="create.php">+ Neue:r Patient:in</a>
        <br>
        <a href="termin_anlegen.php">+ Termin anlegen</a></br>
>>>>>>> Stashed changes

    </p>
    
=======
    <h1>Patient:innen</h1>

    <p>
        <a href="create.php">+ Neue:r Patient:in</a>
    </p>

>>>>>>> origin/F1
    <form method="get">
        <input
            type="text"
            name="q"
<<<<<<< HEAD
            placeholder="Name oder Fachrichtung"
            value="<?= htmlspecialchars($suche) ?>"
        >
        <button type="submit">Suchen</button>

=======
            placeholder="Name oder SV-Nummer"
            value="<?= htmlspecialchars($suche) ?>"
        >
        <button type="submit">Suchen</button>
>>>>>>> origin/F1
        <?php if ($suche !== ''): ?>
            <a href="index.php">Zurücksetzen</a>
        <?php endif; ?>
    </form>

    <br>

    <table border="1" cellpadding="6">

        <tr>
<<<<<<< HEAD
            <th>Titel</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Fachrichtung</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($arzt as $a): ?>

            <tr>
                <td>
                    <?= htmlspecialchars($a['titel'] ?? '') ?>
                </td>

                <td>
                    <?= htmlspecialchars($a['vorname']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($a['nachname']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($a['fachrichtung']) ?>
                </td>

                <td>
                    <a href="arzt_edit.php?id=<?= urlencode((string)$a['id']) ?>">
=======
            <th>SV-Nummer</th>
            <th>Name</th>
            <th>Geburtsdatum</th>
            <th>Telefon</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($patienten as $p): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($p['sv_nummer']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['nachname'] . ' ' . $p['vorname']) ?>
                </td>

                <td>
                    <?= htmlspecialchars(date('d.m.Y', strtotime($p['geburtsdatum']))) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['telefon'] ?? '') ?>
                </td>

                <td>

                    <a href="edit.php?sv=<?= urlencode($p['sv_nummer']) ?>">
>>>>>>> origin/F1
                        Bearbeiten
                    </a>

                    <form
                        method="post"
<<<<<<< HEAD
                        action="arzt_delete.php"
=======
                        action="delete.php"
>>>>>>> origin/F1
                        onsubmit="return confirm('Wirklich löschen?')"
                        style="display:inline;"
                    >
                        <input
                            type="hidden"
<<<<<<< HEAD
                            name="id"
                            value="<?= htmlspecialchars((string)$a['id']) ?>"
=======
                            name="sv_nummer"
                            value="<?= htmlspecialchars($p['sv_nummer']) ?>"
>>>>>>> origin/F1
                        >

                        <button type="submit">
                            Löschen
                        </button>
                    </form>
<<<<<<< HEAD
                </td>
=======

                </td>

>>>>>>> origin/F1
            </tr>

        <?php endforeach; ?>

<<<<<<< HEAD
        <?php if (!$arzt): ?>
            <tr>
                <td colspan="5">Keine Arzt:innen gefunden.</td>
=======
        <?php if (!$patienten): ?>
            <tr>
                <td colspan="5">Keine Patient:innen gefunden.</td>
>>>>>>> origin/F1
            </tr>
        <?php endif; ?>

    </table>

</body>
<<<<<<< HEAD
</html>
=======

</html>
>>>>>>> origin/F1
