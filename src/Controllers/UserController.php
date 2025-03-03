<?php

require_once('../Models/UserModel.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

use Model\User;

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    if (!is_numeric($id) || $id < 0) {
        $id = USER::retrieveIdWithUsername($id);
        $otherUser = USER::fetch($id);
        include_once '../Views/user/otherProfile.php';
        exit;
    }
    if ($id == $_SESSION['user_id']) {
        $CurrentUser = USER::fetch($_SESSION['user_id']);
        include_once('../Views/user/currentProfile.php');
        exit;
    } else {
        $otherUser = USER::fetch($id);
        include_once('../Views/user/otherProfile.php');
        exit;
    }
} else {
    $CurrentUser = USER::fetch($_SESSION['user_id']);
    include_once('../Views/user/currentProfile.php');
    exit;
}
