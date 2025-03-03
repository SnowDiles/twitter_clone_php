<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === NULL || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}
include_once('../Views/user/profile.php');
