const puzzles = [
  { q: "The king ruled for 25 years. He started in 1420. He ended in 🍌.", a: 1445 },
  { q: "An inventor was born in 1809 and lived 56 years. He died in 🍌.", a: 1865 },
  { q: "World War II ended in 1945. It lasted 6 years. It started in 🍌.", a: 1939 },
  { q: "A clock tower was built in 1700. 320 years later it was restored in 🍌.", a: 2020 },
  { q: "A queen began her reign in 1558 and ruled 45 years. She died in 🍌.", a: 1603 },
  { q: "The internet became public in 1991. In 29 years, it reached 2020. Confirm 🍌.", a: 2020 }
];

let score = 0;
let current = 0;
let player = "";


const bananaAPI = "https://www.sanfoh.com/uob/banana/data/t1342e6bcc217c658dbae77b820n66.png"; 

function startGame() {
  player = document.getElementById("playerName").value;
  if (player.trim() === "") {
    alert("Please enter your name!");
    return;
  }

  // Save player identity
  localStorage.setItem("chronoPlayer", player);

  document.getElementById("setup").style.display = "none";
  document.getElementById("game").style.display = "block";
  document.getElementById("welcome").innerText = "Welcome, " + player + "! Let's fix the timeline.";
  loadPuzzle();
}

function loadPuzzle() {
  if (current < puzzles.length) {
    document.getElementById("question").innerText = puzzles[current].q;
    // document.getElementById("bananaImg").src = bananaAPI; 
    document.getElementById("answer").value = "";
    document.getElementById("level").innerText = current + 1;
  } else {
    document.getElementById("question").innerText = "🎉 Timeline restored! Great job, " + player + "!";
    document.getElementById("bananaImg").style.display = "none";
    document.getElementById("answer").style.display = "none";
  }
}

function checkAnswer() {
  let userAns = parseInt(document.getElementById("answer").value);
  let feedback = document.getElementById("feedback");

  if (userAns === puzzles[current].a) {
    score++;
    feedback.innerText = "✅ Correct! Timeline fixed.";
  } else {
    feedback.innerText = "❌ Wrong! Try again.";
    return; // Let them retry
  }

  document.getElementById("score").innerText = score;

  // Save score
  localStorage.setItem("chronoScore", score);

  // Next puzzle
  current++;
  loadPuzzle();
}
