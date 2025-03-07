<?php

require_once('../Models/UserModel.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

use Model\User;

// Routing preparation and for the future back
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page === "following") {
        $CurrentUser = User::fetch($_GET['userId'] ?? $_SESSION['user_id']);
        include_once("../Views/user/following.php");
        exit;
    } elseif ($page === "follower") {
        $CurrentUser = User::fetch($_GET['userId'] ?? $_SESSION['user_id']);
        include_once("../Views/user/follower.php");
        exit;
    }
}

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    if ($id == $_SESSION['user_id']) {
        $CurrentUser = User::fetch($_SESSION['user_id']);
        include_once('../Views/user/currentProfile.php');
        exit;
    } else {
        $otherUser = User::fetch($id);
        include_once('../Views/user/otherProfile.php');
        exit;
    }
} else {
    $CurrentUser = User::fetch($_SESSION['user_id']);
    include_once('../Views/user/currentProfile.php');
    exit;
}