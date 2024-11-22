<?php
session_start();
// Initialize the game board if it hasn't been set
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, ''); // Empty 3x3 grid
    $_SESSION['turn'] = 'X'; // X starts (Human plays as X)
}
// Function to handle a move
if (isset($_POST['cell']) && $_SESSION['turn'] == 'X') {
    $cell = $_POST['cell'];
    if ($_SESSION['board'][$cell] == '') {
        $_SESSION['board'][$cell] = 'X'; // Human's move
        $_SESSION['turn'] = 'O'; // Switch turn to Machine
    }
}
// Simple AI to make machine moves
function machine_move()
{
    // AI will take the first available empty space
    for ($i = 0; $i < 9; $i++) {
        if ($_SESSION['board'][$i] == '') {
            $_SESSION['board'][$i] = 'O'; // Machine's move
            $_SESSION['turn'] = 'X'; // Switch turn back to Human
            return;
        }
    }
}
// If it's machine's turn, make a move
if ($_SESSION['turn'] == 'O') {
    machine_move();
}
// Check for winner or draw
function check_winner($board)
{
    $winning_combinations = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8], // Rows
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8], // Columns
        [0, 4, 8],
        [2, 4, 6] // Diagonals
    ];
    foreach ($winning_combinations as $combination) {
        list($a, $b, $c) = $combination;
        if ($board[$a] != '' && $board[$a] == $board[$b] && $board[$b] == $board[$c]) {
            return $board[$a]; // Return the winner ('X' or 'O')
        }
    }
    // Check for a draw
    if (!in_array('', $board)) {
        return 'Draw';
    }
    return null; // No winner yet
}
$winner = check_winner($_SESSION['board']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe: Human vs Machine</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
        }
        .board {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            grid-gap: 5px;
            justify-content: center;
            margin: 20px;
        }
        .cell {
            width: 100px;
            height: 100px;
            border: 2px solid #333;
            font-size: 3rem;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        .cell.taken {
            pointer-events: none;
        }
        .cell:hover {
            background-color: #f0f0f0;
            transform: scale(1.05);
        }
        /* Style for "X" */
        .cell.x {
            color: red;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        /* Style for "O" */
        .cell.o {
            color: blue;
            font-style: italic;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .message {
            margin-top: 20px;
            font-size: 1.5rem;
        }
        button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tic-Tac-Toe: Human vs Machine</h1>
        <div class="board">
            <?php for ($i = 0; $i < 9; $i++): ?>
                <form method="post" style="margin: 0;">
                    <input type="hidden" name="cell" value="<?= $i ?>">
                    <button class="cell <?= $_SESSION['board'][$i] ? strtolower($_SESSION['board'][$i]) : '' ?>"
                        <?= $_SESSION['board'][$i] ? 'disabled' : '' ?>>
                        <?= $_SESSION['board'][$i] ?>
                    </button>
                </form>
            <?php endfor; ?>
        </div>
        <div class="message">
            <?php if ($winner): ?>
                <?php if ($winner == 'Draw'): ?>
                    <p>It's a Draw!</p>
                <?php else: ?>
                    <p>Player <?= $winner ?> wins!</p>
                <?php endif; ?>
            <?php else: ?>
                <p>It's Player <?= $_SESSION['turn'] ?>'s turn!</p>
            <?php endif; ?>
        </div>
        <form method="post" style="margin-top: 20px;">
            <button type="submit" name="restart">Restart Game</button>
        </form>
    </div>
    <?php
    // Restart the game if the restart button is clicked
    if (isset($_POST['restart'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    ?>
</body>
</html>