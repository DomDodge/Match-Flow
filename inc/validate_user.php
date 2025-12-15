<?php

session_start();
require_once __DIR__ . "/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $pdo = getDB();

        if ($_POST['action'] === 'login') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                 
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['page'] = "overview";
                $_SESSION['username'] = $user['username']; 
                header("Location: ../index.php");
                exit();
            } else {
                
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: ../login.php");
            }

        } elseif ($_POST['action'] === 'sign up') {
            $fname = $_POST['first_name'];
            $lname = $_POST['last_name'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                
                $_SESSION['error'] = "Username is already taken";
                header("Location: ../login.php");
            } 
            else if(strlen($password) < 6) {
                
                $_SESSION['error'] = "Password is too weak";
                header("Location: ../login.php");
            }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, first_name, last_name) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hash, $fname, $lname]);

                $newId = $pdo->lastInsertId();

                
                $_SESSION['user_id'] = (int) $newId;
                $_SESSION['page'] = "overview";
                $_SESSION['username'] = $username;
                header("Location: ../index.php");
                exit();
            }
        }

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
