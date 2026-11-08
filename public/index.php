<?php
// ---------- Database connection ----------
$host     = 'localhost';
$dbname   = 'archioracle';
$username = 'root';
$password = '';          

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
    // If this is an API request, return JSON error
    if (isset($_GET['action']) && $_GET['action'] === 'get_phrase') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    die("Database connection failed: " . $e->getMessage());
}

// ---------- Simple API: return a random phrase ----------
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
                'phrase'  => 'The void is empty... add some phrases first.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'phrase' => 'Something went wrong.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archioracle – Vague 8-Ball</title>
    <style>
        :root {
            --bg: #0b0e17;
            --ball: #111827;
            --accent: #22d3ee;
            --accent2: #a78bfa;
            --text: #e2e8f0;
            --muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: radial-gradient(ellipse at top, #1e1b4b 0%, var(--bg) 60%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        h1 {
            font-size: clamp(1.8rem, 5vw, 2.6rem);
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 0.3rem;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: var(--muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* ---- Question input ---- */
        .input-group {
            width: 100%;
            max-width: 420px;
            margin-bottom: 2rem;
        }

        label {
            display: block;
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #334155;
            border-radius: 12px;
            background: #1e293b;
            color: var(--text);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.2);
        }

        /* ---- The 8-Ball ---- */
        .ball-container {
            position: relative;
            width: 260px;
            height: 260px;
            margin: 0 auto 2rem;
            cursor: pointer;
            user-select: none;
        }

        .ball {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #374151, #0f172a 70%);
            box-shadow:
                inset -20px -20px 40px rgba(0,0,0,0.6),
                inset 10px 10px 20px rgba(255,255,255,0.05),
                0 15px 40px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: transform 0.1s;
        }

        .ball::before {
            content: '';
            position: absolute;
            top: 18%;
            left: 22%;
            width: 35%;
            height: 25%;
            background: radial-gradient(ellipse, rgba(255,255,255,0.15), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .window {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #020617;
            border: 4px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 12px;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.8);
            overflow: hidden;
        }

        .answer {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.3;
            color: #67e8f9;
            opacity: 0;
            transform: scale(0.6);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .answer.visible {
            opacity: 1;
            transform: scale(1);
        }

        .eight {
            font-size: 2.8rem;
            font-weight: 800;
            color: #e2e8f0;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }

        /* Shake animation */
        @keyframes shake {
            0%, 100% { transform: rotate(0deg) translateX(0); }
            10%  { transform: rotate(-8deg) translateX(-6px); }
            20%  { transform: rotate(8deg)  translateX(6px); }
            30%  { transform: rotate(-8deg) translateX(-6px); }
            40%  { transform: rotate(8deg)  translateX(6px); }
            50%  { transform: rotate(-6deg) translateX(-4px); }
            60%  { transform: rotate(6deg)  translateX(4px); }
            70%  { transform: rotate(-4deg) translateX(-2px); }
            80%  { transform: rotate(4deg)  translateX(2px); }
            90%  { transform: rotate(-2deg) translateX(-1px); }
        }

        .ball.shaking {
            animation: shake 0.9s ease-in-out;
        }

        /* ---- Button ---- */
        .shake-btn {
            display: block;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            padding: 16px 24px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.2s;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.35);
        }

        .shake-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(139, 92, 246, 0.45);
        }

        .shake-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .shake-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* User question display */
        .user-question {
            margin-top: 1.8rem;
            max-width: 420px;
            text-align: center;
            font-size: 0.95rem;
            color: var(--muted);
            min-height: 1.4em;
        }

        .user-question strong {
            color: var(--accent);
        }

        footer {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: #475569;
            text-align: center;
        }

        /* Mobile tweaks */
        @media (max-width: 480px) {
            .ball-container {
                width: 220px;
                height: 220px;
            }
            .window {
                width: 95px;
                height: 95px;
            }
            .answer {
                font-size: 0.78rem;
            }
        }
    </style>
</head>
<body>

    <h1>ARCHIORACLE</h1>
    <p class="subtitle">Shake the ball. Receive a vague answer.</p>

    <div class="input-group">
        <label for="question">Ask a vague question...</label>
        <input type="text" id="question" placeholder="Will I survive the next exam?" maxlength="120" autocomplete="off">
    </div>

    <div class="ball-container" id="ballContainer" title="Click or press the button to shake">
        <div class="ball" id="ball">
            <div class="window">
                <div class="eight" id="eight">8</div>
                <div class="answer" id="answer"></div>
            </div>
        </div>
    </div>

    <button class="shake-btn" id="shakeBtn">SHAKE THE BALL</button>

    <div class="user-question" id="userQuestion"></div>

    <footer>
        Archioracle &bull; School Open Day Project
    </footer>

    <script>
        const ball        = document.getElementById('ball');
        const eight       = document.getElementById('eight');
        const answerEl    = document.getElementById('answer');
        const shakeBtn    = document.getElementById('shakeBtn');
        const questionInput = document.getElementById('question');
        const userQuestion  = document.getElementById('userQuestion');

        let isShaking = false;

        async function shakeBall() {
            if (isShaking) return;
            isShaking = true;
            shakeBtn.disabled = true;

            // Show the user's question
            const q = questionInput.value.trim();
            userQuestion.innerHTML = q
                ? `You asked: <strong>${escapeHtml(q)}</strong>`
                : '';

            // Reset previous answer
            answerEl.classList.remove('visible');
            answerEl.textContent = '';
            eight.style.display = 'block';

            // Start shake animation
            ball.classList.add('shaking');

            // Wait for animation + a bit more drama
            await sleep(1000);

            // Fetch random phrase from PHP
            try {
                const res = await fetch('?action=get_phrase');
                const data = await res.json();

                eight.style.display = 'none';
                answerEl.textContent = data.phrase || 'The oracle is silent.';
                answerEl.classList.add('visible');
            } catch (err) {
                eight.style.display = 'none';
                answerEl.textContent = 'Connection failed...';
                answerEl.classList.add('visible');
            }

            ball.classList.remove('shaking');
            isShaking = false;
            shakeBtn.disabled = false;
        }

        // Helpers
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Events
        shakeBtn.addEventListener('click', shakeBall);
        ball.addEventListener('click', shakeBall);

        // Allow pressing Enter in the input
        questionInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                shakeBall();
            }
        });
    </script>
</body>
</html>
