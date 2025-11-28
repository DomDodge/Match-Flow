<?php
session_start();

if (isset($_GET['page'])) {
    $_SESSION['page'] = $_GET['page'];
    echo "ok";
} else {
    echo "no page provided";
}
