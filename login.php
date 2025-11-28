<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <h1>Match Flow</h1>
        <?php
            session_start();

            if(isset($_SESSION['error'])) {
                echo "<h4>" . $_SESSION['error'] . "</h4>";
            }
        ?>
        <form id="loginForm" method="POST" action="inc/validate_user.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button id="login" name="action" value="login">LOG IN</button>
        </form>


        <form id="signupForm" method="POST" action="inc/validate_user.php" style="display:none;">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button id="login" name="action" value="sign up">SIGN UP</button>
        </form>
        <button id="signUp" onclick="toggleForm()">SIGN UP</button>
    </body>

    <script>
        let signUpForm = document.getElementById("signupForm");
        let loginForm = document.getElementById("loginForm");
        let showingSignup = false;
        let toggleButton = document.getElementById("signUp");

        function toggleForm() {
            showingSignup = !showingSignup;

            if (showingSignup) {
                signUpForm.style.display = "block";
                loginForm.style.display = "none";
                toggleButton.innerHTML = "LOGIN";
            } else {
                signUpForm.style.display = "none";
                loginForm.style.display = "block";
                toggleButton.innerHTML = "SIGN UP";
            }
        }
    </script>
</html>