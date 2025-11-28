<?php
session_start();
require_once __DIR__ . '/functions.php';

if (
    isset($_GET['conversations']) &&
    isset($_GET['connections']) &&
    isset($_GET['friendships']) &&
    isset($_GET['dates'])
) {
    updateGoals($_SESSION['username'], date('W'), date('o'), 'conversations', $_GET['conversations']);
    updateGoals($_SESSION['username'], date('W'), date('o'), 'connections', $_GET['connections']);
    updateGoals($_SESSION['username'], date('W'), date('o'), 'friendships', $_GET['friendships']);
    updateGoals($_SESSION['username'], date('W'), date('o'), 'dates', $_GET['dates']);
    echo "ok";
} else {
    echo "no page provided";
}
