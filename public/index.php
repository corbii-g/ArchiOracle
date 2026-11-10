<?php
/**
 * Archioracle - Palla 8 Vaga
 * Pagina principale + endpoint API
 */

// ---------- Connessione al database ----------
$host     = 'localhost';
$dbname   = 'archioracle';
$username = 'root';
$password = '';          // inserisci la password se ce l'hai

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]
    );
} catch (PDOException $e) {
    if (isset($_GET['action']) && $_GET['action'] === 'get_phrase') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Connessione al database fallita']);
        exit;
    }
    die("Connessione al database fallita: " . $e->getMessage());
}

// ---------- API: restituisce una frase casuale ----------
if (isset($_GET['action']) && $_GET['action'] === 'get_phrase') {
    header('Content-Type: application/json');

    try {
        $stmt = $pdo->query("SELECT phrase, category FROM phrases ORDER BY RAND() LIMIT 1");
        $row = $stmt->fetch();

        if ($row) {
            echo json_encode([
                'success'  => true,
                'phrase'   => $row['phrase'],
                'category' => $row['category']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'phrase'  => 'Il vuoto è vuoto... aggiungi prima qualche frase.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'phrase' => 'Qualcosa è andato storto.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="stylesheet" href="css/style.css">
    <title>Archioracle – Palla 8 Vaga</title>
    <script src="js/app.js" defer></script>
</head>
<body>

    <h1>ARCHIORACLE</h1>
    <p class="subtitle">Scuoti la palla. Ricevi una risposta.</p>

    <div class="input-group">
        <label for="question">Fai una domanda</label>
        <input type="text" id="question" placeholder="Passerò quest'anno?" maxlength="120" autocomplete="off">
    </div>

    <div class="ball-container" id="ballContainer" title="Clicca la palla per scuoterla">
        <div class="ball" id="ball">
            <div class="window">
                <div class="eight" id="eight">8</div>
                <div class="answer" id="answer"></div>
            </div>
        </div>
    </div>

    <p class="hint">Clicca la palla per scuoterla</p>
    <div class="entry-link-wrapper">
        <a href="add_entry.php" class="entry-link">Aggiungi una entry!</a>
    </div>
    
    <img src="assets/qr-code.png" alt="QR Code" class="qr-code">

    <footer>
        Archioracle &bull; Progetto Open Day
    </footer>
</body>
</html>
