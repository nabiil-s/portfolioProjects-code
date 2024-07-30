
const quizContainer = document.getElementById('quizContainer');
const userGreeting = document.getElementById('userGreeting');
const resultForm = document.getElementById('resultForm');
const usernameInput = document.getElementById('username');
const scoreInput = document.getElementById('score');

quizContainer.style.display = 'none';

//To hold language data 
let score = 0;
const test = [
    { spanish: 'Coche', english: 'Car' },
    { spanish: 'Luna', english: 'Moon' },
    { spanish: 'Agua', english: 'Water' },
    { spanish: 'Alimento', english: 'Food' },
    { spanish: 'Hola', english: 'Hello' },
    { spanish: 'Beber', english: 'Drink' },
    { spanish: 'Cielo', english: 'Sky' }
];

let username = '';

//Create arrow function to start the quiz
const showGreeting = (event) => {
    event.preventDefault();

    const error = document.getElementById('error');

    //Function to add username and prevent quiz initiation if username not entered
    username = document.getElementById('name').value.trim().substring(0, 18);

    if (!username) {
        error.innerHTML = 'Please enter a username!';
        error.style.color = 'maroon';
    } else {
        error.innerHTML = '';
        const userData = document.getElementById('userData');
        userData.style.display = 'none';

        userGreeting.innerHTML = `Hello ${username}! Welcome to the Spanish Quiz<br>`;
        quizContainer.style.display = 'block';

        quiz();
    }
}

let currentIndex = 0;

// function to set up question format and connecting to loops
const question = () => {
    const word = test[currentIndex];
    const displayQuestion = document.getElementById('questions');
    displayQuestion.innerHTML = `Translate '${word.spanish}' to English`;
}

// Creating live score
const quiz = () => {
    document.getElementById('liveScore').innerHTML = `Score: ${score} / ${test.length}<br>`;
    question();
}

// Checking if all questions are answered correct or incorrect
const checkAnswer = () => {
    if (currentIndex >= test.length) {
        return;
    }
    const userAnswer = document.getElementById('answer').value.trim().toLowerCase();
    const word = test[currentIndex];
    const resultElement = document.getElementById('result');

    if (userAnswer === word.english.toLowerCase()) {
        resultElement.innerHTML = 'Correct!';
        resultElement.style.color = 'green';
        score++;
    } else {
        resultElement.innerHTML = `Incorrect. The correct translation is '${word.english}'.`;
        resultElement.style.color = '#ca0d0d';
    }

    currentIndex++;

    document.getElementById('liveScore').innerHTML = `Score: ${score} / ${test.length} <br>`;

    //Using loops to naviagte through questions
    if (currentIndex < test.length) {
        setTimeout(() => {
            document.getElementById('answer').value = '';
            resultElement.innerHTML = '';
            question();
        }, 900);

    //To create a fetch api which submits form results completely before displaying results
    } else {
        usernameInput.value = username;
        scoreInput.value = score;

        // Submit the form using fetch
        fetch(resultForm.action, {
            method: resultForm.method,
            body: new FormData(resultForm)
        })
        .then(response => {
            if (response.ok) {
                return response.text(); 
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            // Handle successful form submission
            // direct to another page
            displayFinalResults();
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
        });
    }
};

// Event listener for form submission
resultForm.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent default form submission
    checkAnswer(); // Call checkAnswer function to handle form submission
});

const displayFinalResults = () => {

    const waterMark = document.getElementById('visitPortfolio');

     if (waterMark) {
        waterMark.style.display = 'none'; // Hide watermark
    } else {
        console.log("Element with ID 'visitPortfolio' not found.");
    }

    //setting up final results template
    document.body.style.backgroundColor = '#FFFFE0';
    userGreeting.innerHTML = 'Quiz Results!';
    quizContainer.style.display = 'none';

    const finalResultsContainer = document.getElementById('finalResultsContainer');
    const finalResultsMessage = document.getElementById('finalResultsMessage');
    
    finalResultsContainer.style.display = 'block';

    //Switch statement to provide feedback based on results
    switch (score) {
        case 0:
        case 1:
            finalResultsMessage.innerHTML = `${username}, you scored <span style="color: #ca0d0d">${score}</span> / ${test.length}. You need more practice.`;
            break;
        case 2:
            finalResultsMessage.innerHTML = `${username}, you scored <span style="color: black">${score}</span> / ${test.length}. You can do better!`;
            break;
        case 3:
        case 4:
            finalResultsMessage.innerHTML = `${username}! You scored <span style="color: green">${score}</span> / ${test.length}. You are doing well!`;
            break;
        case 5:
        case 6:
            finalResultsMessage.innerHTML = `Congratulations ${username}! You scored <span style="color: green">${score}</span> / ${test.length}.`;
            break;
    }

    usernameInput.value = username;
    scoreInput.value = score;

    //Retrieving results from PHP file using AJAX
    finalResultsMessage.innerHTML += '<div class="scoreTitle"><strong>Top Scores</strong></div>';

    // AJAX to fetch top scores
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch.php', true);

    xhr.onreadystatechange = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {

                // Directly insert the fetched results into the finalResultsMessage element
                finalResultsMessage.innerHTML += `<br><br>${xhr.responseText}`;
                
            } else {
                console.log("Error: ", xhr.status, xhr.statusText);
            }
        }
    };

    xhr.onerror = () => {
        finalResultsMessage.textContent += '\nFailed to connect to the server. Please check your internet connection or try again later.';
    };

    xhr.send();
};



