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

    <h1>Patient:innen</h1>

    <p>
        <a href="create.php">+ Neue:r Patient:in</a>
    </p>

    <form method="get">
        <input
            type="text"
            name="q"
            placeholder="Name oder SV-Nummer"
            value="<?= htmlspecialchars($suche) ?>"
        >
        <button type="submit">Suchen</button>
        <?php if ($suche !== ''): ?>
            <a href="index.php">Zurücksetzen</a>
        <?php endif; ?>
    </form>

    <br>

    <table border="1" cellpadding="6">

        <tr>
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
                        Bearbeiten
                    </a>

                    <form
                        method="post"
                        action="delete.php"
                        onsubmit="return confirm('Wirklich löschen?')"
                        style="display:inline;"
                    >
                        <input
                            type="hidden"
                            name="sv_nummer"
                            value="<?= htmlspecialchars($p['sv_nummer']) ?>"
                        >

                        <button type="submit">
                            Löschen
                        </button>
                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        <?php if (!$patienten): ?>
            <tr>
                <td colspan="5">Keine Patient:innen gefunden.</td>
            </tr>
        <?php endif; ?>

    </table>

</body>

</html>