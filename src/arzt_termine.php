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

// Wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['arzt_id'], $_GET['datum'])) {

    $arzt_id = $_GET['arzt_id'];
    $datum = $_GET['datum'];

    // Termine des ausgewählten Arztes an diesem Tag laden
    $stmt = $pdo->prepare("
        SELECT termin.uhrzeit, termin.grund,
               patient.vorname, patient.nachname
        FROM termin
        JOIN patient ON termin.patient_sv = patient.sv_nummer
        WHERE termin.arzt_id = :arzt_id
        AND termin.datum = :datum
        ORDER BY termin.uhrzeit
    ");

    $stmt->execute([
        ':arzt_id' => $arzt_id,
        ':datum' => $datum
    ]);

    $termine = $stmt->fetchAll();
}
?>

<h1>Tagesansicht der Arzttermine</h1>

<!-- Formular zur Auswahl eines Arztes und Datums -->
<form method="GET">

    <label>Arzt auswählen:</label>
    <select name="arzt_id" required>
        <option value="">Arzt auswählen</option>

        <?php foreach ($aerzte as $arzt): ?>
            <option value="<?= htmlspecialchars($arzt['id']) ?>"
                <?= (isset($_GET['arzt_id']) && $_GET['arzt_id'] == $arzt['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($arzt['titel'] . ' ' . $arzt['vorname'] . ' ' . $arzt['nachname']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <label>Datum auswählen:</label>
    <input type="date" name="datum"
           value="<?= htmlspecialchars($_GET['datum'] ?? '') ?>" required>

    <br><br>

    <button type="submit">Termine anzeigen</button>

</form>

<hr>

<h2>Termine des ausgewählten Tages</h2>

<?php if (isset($_GET['arzt_id'], $_GET['datum'])): ?>

    <?php if (count($termine) > 0): ?>

        <table border="1">
            <tr>
                <th>Uhrzeit</th>
                <th>Patient</th>
                <th>Grund</th>
            </tr>

            <?php foreach ($termine as $termin): ?>
                <tr>
                    <td><?= htmlspecialchars($termin['uhrzeit']) ?></td>
                    <td><?= htmlspecialchars($termin['vorname'] . ' ' . $termin['nachname']) ?></td>
                    <td><?= htmlspecialchars($termin['grund']) ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php else: ?>

        <p>Für diesen Arzt gibt es an diesem Tag keine Termine.</p>

    <?php endif; ?>

<?php endif; ?>

<br>
<a href="index.php">Zurück zur Startseite</a>
