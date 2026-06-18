$(function () {
    $('[data-autofocus]').first().trigger('focus');
});

(function () {
    'use strict';

    var state = {
        currentStep: 0,
        totalSteps: 0,
        score: 0,
        timerInterval: null,
        timeLeft: 15,
        answered: false,
    };

    var firstStep = document.querySelector('.question-step.active');
    if (!firstStep) return;

    var allSteps = document.querySelectorAll('.question-step');
    state.totalSteps = allSteps.length;

    function startTimer() {
        clearInterval(state.timerInterval);
        state.timeLeft = 15;
        updateTimerDisplay();
        state.timerInterval = setInterval(function () {
            state.timeLeft--;
            updateTimerDisplay();
            if (state.timeLeft <= 0) {
                clearInterval(state.timerInterval);
                handleTimeout();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        var el = document.getElementById('timerDisplay');
        if (!el) return;
        el.textContent = state.timeLeft;
        el.className = 'timer';
        if (state.timeLeft <= 5) {
            el.classList.add('timer--danger');
        } else if (state.timeLeft <= 10) {
            el.classList.add('timer--warning');
        }
    }

    function handleTimeout() {
        if (state.answered) return;
        state.answered = true;

        var step = allSteps[state.currentStep];
        var correctId = parseInt(step.getAttribute('data-correct-id'), 10);

        Array.prototype.forEach.call(step.querySelectorAll('.answer-btn'), function (btn) {
            btn.disabled = true;
            var aid = parseInt(btn.getAttribute('data-answer-id'), 10);
            if (aid === correctId) {
                btn.classList.add('answer-btn--correct');
            } else {
                btn.classList.add('answer-btn--dimmed');
            }
        });

        showNextButton();
    }

    function selectAnswer(btn) {
        if (state.answered) return;
        clearInterval(state.timerInterval);

        var step = allSteps[state.currentStep];
        var questionId = parseInt(step.getAttribute('data-question-id'), 10);
        var correctId = parseInt(step.getAttribute('data-correct-id'), 10);
        var answerId = parseInt(btn.getAttribute('data-answer-id'), 10);
        var isCorrect = answerId === correctId;

        // Set hidden input for server-side submission
        var hiddenInput = step.querySelector('input[type="hidden"]');
        if (hiddenInput) hiddenInput.value = answerId;

        // Visual feedback
        Array.prototype.forEach.call(step.querySelectorAll('.answer-btn'), function (el) {
            el.disabled = true;
            var aid = parseInt(el.getAttribute('data-answer-id'), 10);
            if (aid === correctId) {
                el.classList.add('answer-btn--correct');
            } else if (aid === answerId && !isCorrect) {
                el.classList.add('answer-btn--wrong');
            } else if (aid !== answerId) {
                el.classList.add('answer-btn--dimmed');
            }
        });

        if (isCorrect) {
            state.score++;
            var scoreEl = document.getElementById('scoreDisplay');
            if (scoreEl) scoreEl.textContent = 'Score: ' + state.score;
        }

        state.answered = true;
        showNextButton();
    }

    function showNextButton() {
        setTimeout(function () {
            if (state.currentStep >= state.totalSteps - 1) {
                var finishBtn = document.getElementById('finishBtn');
                if (finishBtn) finishBtn.style.display = 'inline-flex';
            } else {
                var nextBtn = document.getElementById('nextBtn');
                if (nextBtn) nextBtn.style.display = 'inline-flex';
            }
        }, 800);
    }

    function showStep(index) {
        Array.prototype.forEach.call(allSteps, function (el, i) {
            el.classList.toggle('active', i === index);
        });

        var counter = document.getElementById('questionCounter');
        if (counter) counter.textContent = 'Vraag ' + (index + 1) + ' / ' + state.totalSteps;

        var fill = document.getElementById('progressFill');
        if (fill) fill.style.width = (index / state.totalSteps * 100) + '%';

        state.answered = false;
        startTimer();
    }

    function nextQuestion() {
        if (state.currentStep < state.totalSteps - 1) {
            state.currentStep++;
            showStep(state.currentStep);
        }
    }

    // Event delegation for answer buttons
    document.addEventListener('click', function (e) {
        if (state.answered) return;
        var btn = e.target.closest('.answer-btn:not(:disabled)');
        if (btn && allSteps[state.currentStep].contains(btn)) {
            selectAnswer(btn);
        }
    });

    // Next button click
    var nextBtn = document.getElementById('nextBtn');
    if (nextBtn) nextBtn.addEventListener('click', nextQuestion);

    // Initialize first question timer
    startTimer();
})();
