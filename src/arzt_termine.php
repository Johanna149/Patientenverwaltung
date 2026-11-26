<?php

require_once 'db.php';

// Ärzte aus der Datenbank laden
$stmt = $pdo->query("
    SELECT id, titel, vorname, nachname, fachrichtung
    FROM arzt
    ORDER BY nachname, vorname
");
$aerzte = $stmt->fetchAll();

$termine = [];
$fehler = '';

$arzt_id = '';
$datum = '';

// Wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['arzt_id'], $_GET['datum'])) {

    $arzt_id = trim($_GET['arzt_id']);
    $datum = trim($_GET['datum']);

    // Arzt-ID überprüfen
    if (
        !filter_var($arzt_id, FILTER_VALIDATE_INT) ||
        (int) $arzt_id <= 0
    ) {
        $fehler = 'Bitte wähle einen gültigen Arzt aus.';
    }

    // Datum überprüfen
    elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum) ||
        !checkdate(
            (int) substr($datum, 5, 2),
            (int) substr($datum, 8, 2),
            (int) substr($datum, 0, 4)
        )
    ) {
        $fehler = 'Bitte gib ein gültiges Datum ein.';
    }

    else {
        // Überprüfen, ob der Arzt existiert
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM arzt
            WHERE id = :arzt_id
        ");
        $stmt->execute(['arzt_id' => $arzt_id]);

        if ($stmt->fetchColumn() == 0) {
            $fehler = 'Der ausgewählte Arzt existiert nicht.';
        } else {
            // Termine des ausgewählten Arztes an diesem Tag laden
            $stmt = $pdo->prepare("
                SELECT termin.uhrzeit, termin.grund,
                       patient.vorname, patient.nachname
                FROM termin
                JOIN patient
                    ON termin.patient_sv = patient.sv_nummer
                WHERE termin.arzt_id = :arzt_id
                AND termin.datum = :datum
                ORDER BY termin.uhrzeit
            ");

            $stmt->execute([
                'arzt_id' => $arzt_id,
                'datum' => $datum
            ]);

            $termine = $stmt->fetchAll();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Tagesansicht der Arzttermine</title>
</head>

<body>

    <h1>Tagesansicht der Arzttermine</h1>

    <!-- Formular zur Auswahl eines Arztes und Datums -->
    <form method="GET">

        <label for="arzt_id">Arzt auswählen:</label>

        <select name="arzt_id" id="arzt_id" required>
            <option value="">Arzt auswählen</option>

            <?php foreach ($aerzte as $arzt): ?>
                <option
                    value="<?= htmlspecialchars((string) $arzt['id']) ?>"
                    <?= ($arzt_id == $arzt['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars(
                        $arzt['titel'] . ' ' .
                        $arzt['vorname'] . ' ' .
                        $arzt['nachname']
                    ) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <br><br>

        <label for="datum">Datum auswählen:</label>

        <input
            type="date"
            id="datum"
            name="datum"
            value="<?= htmlspecialchars($datum) ?>"
            required
        >

        <br><br>

        <button type="submit">Termine anzeigen</button>

    </form>

    <hr>

    <?php if ($fehler !== ''): ?>

        <p style="color: red;">
            <?= htmlspecialchars($fehler) ?>
        </p>

    <?php elseif ($arzt_id !== '' && $datum !== ''): ?>

        <h2>Termine des ausgewählten Tages</h2>

        <?php if (count($termine) > 0): ?>

            <table border="1">
                <tr>
                    <th>Uhrzeit</th>
                    <th>Patient</th>
                    <th>Grund</th>
                </tr>

                <?php foreach ($termine as $termin): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($termin['uhrzeit']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $termin['vorname'] . ' ' . $termin['nachname']
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($termin['grund']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>Für diesen Arzt gibt es an diesem Tag keine Termine.</p>

        <?php endif; ?>

    <?php endif; ?>

    <br>

    <a href="index.php">Zurück zur Startseite</a>

</body>

</html>
