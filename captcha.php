<?php
session_start(); // Always start the session at the top of your script

// Log file path
$logFilePath = 'failed_attempts.log';

// Function to log failed attempts
function logFailedAttempt($ip) {
    global $logFilePath;
    $date = date("Y-m-d H:i:s");
    $logMessage = "$date - Failed attempt from IP: $ip\n";
    file_put_contents($logFilePath, $logMessage, FILE_APPEND);
}

// Array of color block questions
$colorQuestions = [
    ["question" => "Select the red block:", "correct_color" => "red"],
    ["question" => "Select the blue block:", "correct_color" => "blue"],
    ["question" => "Select the green block:", "correct_color" => "green"],
    ["question" => "Select the yellow block:", "correct_color" => "yellow"],
];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the correct answer from the session
    $correctAnswer = $_SESSION['correct_color'];
    
    // Check if 'answer' key exists in the POST request
    if (isset($_POST['answer'])) {
        $userAnswer = $_POST['answer']; // Collect user's selected color

        // Validate answer
        if ($userAnswer === $correctAnswer) {
            echo "<div class='message success'>Form submitted successfully!</div>";
        } else {
            logFailedAttempt($_SERVER['REMOTE_ADDR']); // Log the failed attempt
            echo "<div class='message error'>Incorrect color block, please try again.</div>";
        }
    } else {
        echo "<div class='message error'>Please select a color block.</div>";
    }
} else {
    // Select a random question and store it in the session for validation later
    $selectedQuestion = $colorQuestions[array_rand($colorQuestions)];
    $_SESSION['question'] = $selectedQuestion['question'];
    $_SESSION['correct_color'] = $selectedQuestion['correct_color'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Block CAPTCHA</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            max-width: 350px; /* Reduced form width */
            width: 100%;
            background: #fff;
            padding: 20px; /* Adjusted padding */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .color-blocks {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }
        .color-blocks div {
            width: 70px; /* Reduced width */
            height: 70px; /* Reduced height */
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid #ddd;
            transition: transform 0.3s ease;
        }
        .color-blocks div:hover {
            transform: scale(1.1);
        }
        .color-blocks input {
            display: none; /* Hide the radio button */
        }
        .color-blocks input:checked + label div {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .red { background-color: red; }
        .blue { background-color: blue; }
        .green { background-color: green; }
        .yellow { background-color: yellow; }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <h2>Verify You're Human</h2>
        <div>
            <label><?php echo $_SESSION['question']; ?></label>
            <div class="color-blocks">
                <?php
                // Create an array of color options
                $colors = ['red', 'blue', 'green', 'yellow'];

                // Shuffle the color blocks for random positioning
                shuffle($colors);

                // Display the shuffled color blocks
                foreach ($colors as $color) {
                    echo "
                        <input type='radio' name='answer' value='$color' id='$color'>
                        <label for='$color'>
                            <div class='$color'></div>
                        </label>
                    ";
                }
                ?>
            </div>
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>

<!--          Include CAPTCHA using an iframe 
<iframe src="https://your-server-domain.com/captcha.php" width="400" height="300" frameborder="0"></iframe>

<button type="submit">Submit</button> -->
