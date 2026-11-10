<?php

// ---------- Connessione al database ----------
$host     = 'localhost';
$dbname   = 'archioracle';
$username = 'root';
$password = '';          // inserisci la password se ce l'hai

$message = '';
$messageType = ''; 

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
    die("Connessione al database fallita: " . $e->getMessage());
}

// ---------- Gestione del form ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phrase   = trim($_POST['phrase'] ?? '');
    $category = trim($_POST['category'] ?? 'neutral');

    // Validazione semplice
    if ($phrase === '') {
        $message = 'Scrivi una frase prima di inviare.';
        $messageType = 'error';
    } elseif (mb_strlen($phrase) > 255) {
        $message = 'La frase è troppo lunga (massimo 255 caratteri).';
        $messageType = 'error';
    } else {
        // Categorie ammesse
        $allowedCategories = ['neutral', 'positive', 'negative', 'funny'];
        if (!in_array($category, $allowedCategories)) {
            $category = 'neutral';
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO phrases (phrase, category) VALUES (?, ?)");
            $stmt->execute([$phrase, $category]);

            $message = 'Frase aggiunta con successo!';
            $messageType = 'success';

            // Pulisci i campi dopo il successo
            $phrase = '';
            $category = 'neutral';
        } catch (PDOException $e) {
            $message = 'Errore durante il salvataggio. Riprova.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archioracle – Aggiungi frase</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>ARCHIORACLE</h1>
    <p class="subtitle">Aggiungi una nuova risposta da aggiungere al database</p>

    <div class="card">
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="phrase">Scrivi la frase</label>
            <input type="text"
                   id="phrase"
                   name="phrase"
                   placeholder="Es. Le stelle dicono forse..."
                   maxlength="255"
                   value="<?= isset($phrase) ? htmlspecialchars($phrase) : '' ?>"
                   required
                   autocomplete="off">

            <label for="category">Categoria </label>
            <select id="category" name="category">
                <option value="neutral"  <?= (isset($category) && $category === 'neutral')  ? 'selected' : '' ?>>Neutra</option>
                <option value="positive" <?= (isset($category) && $category === 'positive') ? 'selected' : '' ?>>Positiva</option>
                <option value="negative" <?= (isset($category) && $category === 'negative') ? 'selected' : '' ?>>Negativa</option>
                <option value="funny"    <?= (isset($category) && $category === 'funny')    ? 'selected' : '' ?>>Divertente</option>
            </select>

            <button type="submit" class="btn">Aggiungi frase</button>
        </form>
    </div>
    
    <div>
        <a href="index.php" class="back-link">← Torna alla palla</a>
    </div>

    <img src="assets/qr-code.png" alt="QR Code" class="qr-code">

    <footer>
        Archioracle &bull; Progetto Open Day
    </footer>

</body>
</html>
