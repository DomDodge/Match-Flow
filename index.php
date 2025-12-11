<?php 
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    require_once __DIR__ . '/inc/functions.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="style/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    </head>
    <body>
        <main>
            <nav>
                <h2>Match Flow</h2>
                <div class="menuItem" id="activeItem" data-page="overview" onclick="changePage('overview')">
                    <i class="fa-solid fa-house"></i> <h3>Overview</h3>
                </div>

                <div class="menuItem" data-page="people" onclick="changePage('people')">
                    <i class="fa-solid fa-person"></i> <h3>People</h3>
                </div>

                <div class="menuItem" data-page="activities" onclick="changePage('activities')">
                    <i class="fa-solid fa-chart-line"></i> <h3>Activities</h3>
                </div>

                <div class="menuItem" data-page="conversations" onclick="changePage('conversations')">
                    <i class="fa-solid fa-comment"></i> <h3>Conversations</h3>
                </div>

                <div class="menuItem" data-page="connections" onclick="changePage('connections')">
                    <i class="fa-solid fa-handshake"></i> <h3>Connections</h3>
                </div>

                <div class="menuItem" data-page="friendships" onclick="changePage('friendships')">
                    <i class="fa-solid fa-user-group"></i> <h3>Friendships</h3>
                </div>

                <div class="menuItem" data-page="dates" onclick="changePage('dates')">
                    <i class="fa-solid fa-martini-glass"></i> <h3>Dates</h3>
                </div>

                <div class="menuItem" data-page="dropped" onclick="changePage('dropped')">
                    <i class="fa-solid fa-truck-fast"></i> <h3>Dropped</h3>
                </div>

                <div class="menuItem" data-page="account" onclick="changePage('account')">
                    <i class="fa-solid fa-user"></i> <h3>Account</h3>
                </div>

                <div class="menuItem" id="logout" onclick="logout()">
                    <i class="fa-solid fa-right-from-bracket"></i> <h3>Log Out</h3>
                </div>
            </nav>

            <div id="content">
                <?php include "views/" . $_SESSION['page'] . ".php"; ?>
            </div>
        </main>


        <!-- SCRIPTS -->

        <!-- Font Awesome -->
        <script src="https://kit.fontawesome.com/af82ed3469.js" crossorigin="anonymous"></script> 
        <script>
            function changePage(page) {
                displayCurrentPage(page)
                fetch("inc/change_page.php?page=" + encodeURIComponent(page))
                    .then(() => location.reload());
            }

            function displayCurrentPage(data) {
                let activeItem = document.getElementById("activeItem");
                activeItem.id = ""
                let newActive = document.querySelector('[data-page="' + data + '"]');
                newActive.id = "activeItem";
            }

            function logout() {
                window.location = "/inc/logout.php";
            }

            displayCurrentPage(<?php echo json_encode($_SESSION['page']); ?>);
        </script>
    </body>
</html>