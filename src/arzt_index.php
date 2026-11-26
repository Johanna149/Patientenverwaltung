<?php

require_once 'db.php';

// Ärzte laden
$arztSuche = trim($_GET['arzt_q'] ?? '');

if ($arztSuche !== '') {

    $stmt = $pdo->prepare(
        'SELECT * FROM arzt
         WHERE vorname LIKE :q
            OR nachname LIKE :q
            OR CONCAT(vorname, " ", nachname) LIKE :q
            OR fachrichtung LIKE :q
         ORDER BY nachname, vorname'
    );

    $stmt->execute(['q' => '%' . $arztSuche . '%']);

} else {

    $stmt = $pdo->query(
        'SELECT * FROM arzt ORDER BY nachname, vorname'
    );
}

$aerzte = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Arztverwaltung</title>
</head>

<body>

    <h1>Arztverwaltung</h1>

    <hr>

    <h2>Ärzt:innen</h2>

    <p>
        <a href="arzt_create.php">+ Neue:r Arzt:in</a>
    </p>

    <form method="get">

        <input
            type="text"
            name="arzt_q"
            placeholder="Name oder Fachrichtung"
            value="<?= htmlspecialchars($arztSuche) ?>"
        >

        <button type="submit">Ärzte suchen</button>

        <a href="arzt_index.php">Suche zurücksetzen</a>

    </form>

    <br>

    <table border="1" cellpadding="6">

        <tr>
            <th>Titel</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Fachrichtung</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($aerzte as $a): ?>

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

                    <a href="arzt_edit.php?id=<?= urlencode((string) $a['id']) ?>">
                        Bearbeiten
                    </a>

                    <form
                        method="post"
                        action="arzt_delete.php"
                        onsubmit="return confirm('Wirklich löschen?')"
                        style="display:inline;"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars((string) $a['id']) ?>"
                        >

                        <button type="submit">Löschen</button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        <?php if (!$aerzte): ?>

            <tr>
                <td colspan="5">Keine Ärzt:innen gefunden.</td>
            </tr>

        <?php endif; ?>

    </table>

    <hr>

    <a href="arzt_termine.php">Tagesansicht der Arzttermine</a>

    <br><br>

    <a href="index.php">Zurück zur Startseite</a>

</body>

</html>
