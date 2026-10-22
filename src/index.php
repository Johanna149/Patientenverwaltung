
<?php

require 'db.php';

$suche = trim($_GET['q'] ?? '');

if ($suche !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM arzt
         WHERE vorname LIKE :q
            OR nachname LIKE :q
            OR CONCAT(vorname, " ", nachname) LIKE :q
            OR fachrichtung LIKE :q
         ORDER BY nachname, vorname'
    );

    $stmt->execute(['q' => '%' . $suche . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT * FROM arzt ORDER BY nachname, vorname'
    );
}

$arzt = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Arzt:innen</title>
</head>

<body>

    <h1>Arzt:innen</h1>

    <p>
        <a href="create.php">Neuer Arzt:in</a>
    </p>

    <form method="get">
        <input
            type="text"
            name="q"
            placeholder="Name oder Fachrichtung"
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
                    <a href="edit.php?id=<?= urlencode((string)$a['id']) ?>">
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
                            name="id"
                            value="<?= htmlspecialchars((string)$a['id']) ?>"
                        >

                        <button type="submit">
                            Löschen
                        </button>
                    </form>
                </td>
            </tr>

        <?php endforeach; ?>

        <?php if (!$arzt): ?>
            <tr>
                <td colspan="5">Keine Arzt:innen gefunden.</td>
            </tr>
        <?php endif; ?>

    </table>

</body>
</html>
