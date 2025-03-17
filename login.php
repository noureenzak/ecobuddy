<?php
session_start();
require_once 'Models/EcoUserDataSet.php';
use Models\EcoUserDataSet;

// Generate a math equation if it doesn't exist in the session
if (!isset($_SESSION['math_equation'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['math_equation'] = "$num1 + $num2";
    $_SESSION['math_answer'] = $num1 + $num2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // debug: check if username, password, and math answer are correctly passed
        echo "Username from form: " . ($_POST['username'] ?? 'Not set') . "<br>";
        echo "Password from form: " . ($_POST['password'] ?? 'Not set') . "<br>";
        echo "Math answer from form: " . ($_POST['math_answer'] ?? 'Not set') . "<br>";

        $username = $_POST['username'];
        $password = $_POST['password'];
        $userAnswer = (int)$_POST['math_answer']; // Get the user's answer to the math equation

        // verify the math answer
        if ($userAnswer !== $_SESSION['math_answer']) {
            $_SESSION['error'] = "Incorrect answer to the math question.";
            header('Location: login.php');
            exit;
        }

        // Authenticate the user
        $userModel = new EcoUserDataSet();
        $user = $userModel->authenticate($username, $password);  // Authenticate user

        if ($user) {
            // Debug: Print the stored hash and entered password
            echo "Stored hash: " . ($user['password'] ?? 'Not set') . "<br>";
            echo "Entered password: " . $password . "<br>";

            // Verify password
            if (password_verify($password, $user['password'])) {
                echo "Password matches!<br>";

                // Password is correct, set session and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['userType'];

                // Clear the math equation from the session after successful login
                unset($_SESSION['math_equation']);
                unset($_SESSION['math_answer']);

                header('Location: crud.php');
                exit;
            } else {
                $_SESSION['error'] = "Invalid password.";
                header('Location: login.php');
                exit;
            }
        } else {
            $_SESSION['error'] = "User not found.";
            header('Location: login.php');
            exit;
        }
    }
}

require_once 'Views/login.phtml';
?>