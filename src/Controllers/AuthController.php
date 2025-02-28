<?php

require_once __DIR__ . "/../../src/Models/UserModel.php";
session_start();

use Model\User;
use Model\Auth;

$method = $_SERVER['REQUEST_METHOD'];

if ($method) {
    switch ($method) {
        case 'POST':
            if (!empty($_POST)) {
                $formData = $_POST;

                if (empty($formData['confirm_password'])) {
                    $email = empty($formData['email']) ? null : htmlspecialchars($formData['email']);
                    $name = empty($formData['username']) ? null : htmlspecialchars($formData['username']);

                    $auth = new Auth(htmlspecialchars($formData['password']), $email, $name);
                    $id = $auth->requestId();

                    if ($id) {
                        $_SESSION['user_id'] = $id;
                        header('Location: ./HomeController.php');
                        exit();
                    }
                } else {
                    /**
                     * @var int $error Error code indicating the type of error.
                     *
                     * Possible values:
                     * - 1: Passwords do not match.
                     */
                    if ($formData['password'] !== $formData['confirm_password']) {
                        header('Location: ./AuthController.php?error=1');
                        exit();
                    }

                    $dateOfBirth = !empty($formData['date_of_birth'])
                        ? new DateTime($formData['date_of_birth'])
                        : new DateTime();

                    $auth = new Auth(
                        htmlspecialchars($formData['password']),
                        htmlspecialchars($formData['email']),
                        htmlspecialchars($formData['username'])
                    );

                    $user = User::signUp(
                        $auth,
                        htmlspecialchars($formData['nom']),
                        $dateOfBirth
                    );

                    if ($user) {
                        $_SESSION['user_id'] = $user->getId();
                        header('Location: ./HomeController.php');
                        exit();
                    }
                }
                /**
                 * @var int $error
                 *
                 * Represents the error code for the form.
                 *
                 * Possible values:
                 * - 2: A generic error occurred in the form.
                 */
                header('Location: ./AuthController.php?error=2');
                exit();
            }
            break;
    }
}
require_once("../Views/auth/login.php");
