<?php

require 'db.php';

// Patienten laden
$patientSuche = trim($_GET['patient_q'] ?? '');

if ($patientSuche !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM patient
         WHERE vorname LIKE :q
            OR nachname LIKE :q
            OR CONCAT(vorname, " ", nachname) LIKE :q
            OR sv_nummer LIKE :q
         ORDER BY nachname, vorname'
    );
    $stmt->execute(['q' => '%' . $patientSuche . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT * FROM patient ORDER BY nachname, vorname'
    );
}

$patienten = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Patienten- und Arztverwaltung</title>
</head>

<body>

    <h1>Patientenverwaltung</h1>

    <h2>Patient:innen</h2>

    <p>
        <a href="create.php">+ Neue:r Patient:in</a>
    </p>

    <form method="get">
        <input
            type="text"
            name="patient_q"
            placeholder="Name oder SV-Nummer"
            value="<?= htmlspecialchars($patientSuche) ?>"
        >
        <button type="submit">Patienten suchen</button>
    </form>

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
                <td><?= htmlspecialchars($p['sv_nummer']) ?></td>
                <td><?= htmlspecialchars($p['nachname'] . ' ' . $p['vorname']) ?></td>
                <td><?= htmlspecialchars(date('d.m.Y', strtotime($p['geburtsdatum']))) ?></td>
                <td><?= htmlspecialchars($p['telefon'] ?? '') ?></td>
                <td>
                    <a href="edit.php?sv=<?= urlencode($p['sv_nummer']) ?>">
                        Bearbeiten
                    </a>

                    <form method="post" action="delete.php"
                          onsubmit="return confirm('Wirklich löschen?')"
                          style="display:inline;">
                        <input type="hidden" name="sv_nummer"
                               value="<?= htmlspecialchars($p['sv_nummer']) ?>">
                        <button type="submit">Löschen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (!$patienten): ?>
            <tr><td colspan="5">Keine Patient:innen gefunden.</td></tr>
        <?php endif; ?>
    </table>

    <a href="termine.php">Terminverwaltung</a><br>
    <a href="arzt_index.php">Ärzteverwaltung</a>

</body>
</html>
