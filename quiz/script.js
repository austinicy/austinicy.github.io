document.addEventListener('DOMContentLoaded', () => {
    const mainApp = document.getElementById('main-app');
    const passwordScreen = document.getElementById('password-screen');
    const setupScreen = document.getElementById('setup-screen');
    const quizScreen = document.getElementById('quiz-screen');
    const resultsScreen = document.getElementById('results-screen');

    const passwordInput = document.getElementById('password-input');
    const passwordSubmitBtn = document.getElementById('password-submit-btn');

    const startBtn = document.getElementById('start-btn');
    const homeBtn = document.getElementById('home-btn');
    const nextBtn = document.getElementById('next-btn');

    const numQuestionsInput = document.getElementById('num-questions');
    const modeSelect = document.getElementById('mode');

    const questionContainer = document.getElementById('question-container');
    const feedback = document.getElementById('feedback');
    const scoreSpan = document.getElementById('score');
    const resultsDetails = document.getElementById('results-details');
    const historyContainer = document.getElementById('history-container');

    let allQuestions = [];
    let currentTest = {};

    function handlePasswordSubmit() {
        const correctPassword = 'ue5kz8cg';
        if (passwordInput.value === correctPassword) {
            passwordScreen.classList.add('hidden');
            mainApp.classList.remove('hidden');
            setupScreen.classList.remove('hidden');
            // Fetch questions from JSON file
            fetch('questions.json')
                .then(response => response.json())
                .then(data => {
                    allQuestions = data;
                    numQuestionsInput.max = allQuestions.length;
                    loadHistory();
                });
        } else {
            alert('Incorrect Password');
        }
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    function startTest() {
        const num = parseInt(numQuestionsInput.value);
        const mode = modeSelect.value;

        if (num > allQuestions.length) {
            alert(`Maximum number of questions is ${allQuestions.length}`);
            return;
        }

        const selectedQuestions = shuffleArray([...allQuestions]).slice(0, num);

        currentTest = {
            questions: selectedQuestions,
            mode: mode,
            answers: [],
            score: 0,
            currentQuestionIndex: 0,
            startTime: new Date()
        };

        setupScreen.classList.add('hidden');
        quizScreen.classList.remove('hidden');
        resultsScreen.classList.add('hidden');

        displayQuestion();
    }

    function displayQuestion() {
        const questionData = currentTest.questions[currentTest.currentQuestionIndex];
        questionContainer.innerHTML = `
            <p class="question-text">${questionData.question}</p>
            <div class="options-grid">
                ${questionData.options.map((opt, i) => `
                    <div class="option" data-option-index="${i}">${String.fromCharCode(65 + i)}. ${opt}</div>
                `).join('')}
            </div>
             <div class="pdf-link">
                <a href="Professional Machine Learning Engineer 339题.pdf#page=${questionData.page}" target="_blank">View in PDF (page ${questionData.page})</a>
            </div>
        `;

        document.querySelectorAll('.option').forEach(opt => {
            opt.addEventListener('click', handleOptionSelect);
        });
        nextBtn.classList.add('hidden');
    }

    function handleOptionSelect(e) {
        const selectedOption = e.target;
        const selectedIndex = parseInt(selectedOption.dataset.optionIndex);
        const correctAnswerLetter = currentTest.questions[currentTest.currentQuestionIndex].answer;
        const correctAnswerIndex = correctAnswerLetter.charCodeAt(0) - 65;

        const isCorrect = selectedIndex === correctAnswerIndex;

        currentTest.answers.push({
            questionIndex: currentTest.currentQuestionIndex,
            selected: selectedIndex,
            correct: correctAnswerIndex
        });

        if (isCorrect) {
            currentTest.score++;
        }

        if (currentTest.mode === 'immediate') {
            showImmediateFeedback(isCorrect, selectedOption, correctAnswerIndex);
        } else {
            selectedOption.classList.add('selected');
            // Disable further clicks
            document.querySelectorAll('.option').forEach(opt => opt.removeEventListener('click', handleOptionSelect));
            nextBtn.classList.remove('hidden');
        }
    }

    function showImmediateFeedback(isCorrect, selectedOption, correctAnswerIndex) {
        document.querySelectorAll('.option').forEach((opt, i) => {
            opt.removeEventListener('click', handleOptionSelect); // Disable options
            if (i === correctAnswerIndex) {
                opt.classList.add('correct');
            }
        });

        if (!isCorrect) {
            selectedOption.classList.add('incorrect');
            feedback.textContent = "Incorrect!";
        } else {
            feedback.textContent = "Correct!";
        }
        feedback.classList.remove('hidden');
        nextBtn.classList.remove('hidden');
    }

    function nextQuestion() {
        currentTest.currentQuestionIndex++;
        feedback.classList.add('hidden');

        if (currentTest.currentQuestionIndex < currentTest.questions.length) {
            displayQuestion();
        } else {
            showResults();
        }
    }

    function showResults() {
        quizScreen.classList.add('hidden');
        resultsScreen.classList.remove('hidden');

        const percentage = (currentTest.score / currentTest.questions.length) * 100;
        scoreSpan.textContent = `${currentTest.score} / ${currentTest.questions.length} (${percentage.toFixed(2)}%)`;

        resultsDetails.innerHTML = currentTest.questions.map((q, i) => {
            const userAnswerData = currentTest.answers.find(a => a.questionIndex === i);
            const userAnswerLetter = String.fromCharCode(65 + userAnswerData.selected);
            const correctAnswerLetter = q.answer;
            const isCorrect = userAnswerLetter === correctAnswerLetter;

            return `
                <div class="result-item">
                    <p class="result-question">${i + 1}. ${q.question}</p>
                    <p class="user-answer ${isCorrect ? 'correct' : 'incorrect'}">Your answer: ${userAnswerLetter}. ${q.options[userAnswerData.selected]}</p>
                    ${!isCorrect ? `<p class="correct-answer">Correct answer: ${correctAnswerLetter}. ${q.options[userAnswerData.correct]}</p>` : ''}
                     <div class="pdf-link">
                        <a href="Professional Machine Learning Engineer 339题.pdf#page=${q.page}" target="_blank">View in PDF (page ${q.page})</a>
                    </div>
                </div>
            `;
        }).join('');

        saveTestToHistory();
    }

    function saveTestToHistory() {
        const history = JSON.parse(localStorage.getItem('mlQuizHistory')) || [];
        const testResult = {
            date: currentTest.startTime.toLocaleString(),
            score: currentTest.score,
            total: currentTest.questions.length,
            mode: currentTest.mode
        };
        history.push(testResult);
        localStorage.setItem('mlQuizHistory', JSON.stringify(history));
        loadHistory();
    }

    function loadHistory() {
        const history = JSON.parse(localStorage.getItem('mlQuizHistory')) || [];
        if (history.length === 0) {
            historyContainer.innerHTML = "<p>No tests taken yet.</p>";
            return;
        }
        historyContainer.innerHTML = history.reverse().map(item => `
            <div class="history-item">
                <span>${item.date}</span>
                <span>${item.score} / ${item.total}</span>
                <span>(${item.mode} mode)</span>
            </div>
        `).join('');
    }

    function goHome() {
        resultsScreen.classList.add('hidden');
        setupScreen.classList.remove('hidden');
    }

    startBtn.addEventListener('click', startTest);
    nextBtn.addEventListener('click', nextQuestion);
    homeBtn.addEventListener('click', goHome);
    passwordSubmitBtn.addEventListener('click', handlePasswordSubmit);
});