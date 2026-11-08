const ball          = document.getElementById('ball');
const eight         = document.getElementById('eight');
const answerEl      = document.getElementById('answer');
const questionInput = document.getElementById('question');
const inputGroup    = document.querySelector('.input-group');

let isShaking = false;

async function shakeBall() {

    // Evita richieste multiple
    if (isShaking) return;

    // Controlla la domanda
    if (questionInput.value.trim() === '') {
        activateDanger();
        return;
    }

    isShaking = true;

    try {
        answerEl.classList.remove('visible', 'eight');
        answerEl.textContent = '';

        eight.style.display = 'block';

        ball.classList.add('shaking');

        await sleep(1000);

        const res = await fetch('?action=get_phrase');

        if (!res.ok) {
            throw new Error('Errore HTTP: ' + res.status);
        }

        const data = await res.json();

        eight.style.display = 'none';
        answerEl.textContent = data.phrase || 'L\'oracolo è silenzioso.';
        answerEl.classList.add('visible');

        // Attendi 5 secondi
        await sleep(5000);
        answerEl.classList.remove('eight', 'eight-appear');
        answerEl.textContent = '8';

        void answerEl.offsetWidth;

        answerEl.classList.add('eight', 'visible', 'eight-appear');

    } catch (err) {

        eight.style.display = 'none';

        answerEl.textContent = 'Connessione fallita...';
        answerEl.classList.add('visible');

        console.error('Errore durante shakeBall:', err);

        await sleep(5000);


        answerEl.classList.remove('eight', 'eight-appear');
        answerEl.textContent = '8';

        void answerEl.offsetWidth;

        answerEl.classList.add('eight', 'visible', 'eight-appear');

    } finally {

        ball.classList.remove('shaking');
        isShaking = false;

    }
}

function activateDanger() {
  inputGroup.classList.add('danger');
  
  setTimeout(() => {
    inputGroup.classList.remove('danger');
  }, 450);

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Clic sulla palla
ball.addEventListener('click', shakeBall);

// Anche con il tasto Invio nel campo di testo
questionInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        shakeBall();
    }
});