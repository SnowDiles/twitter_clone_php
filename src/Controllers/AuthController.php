<?php

require_once __DIR__ . "/../../src/Models/UserModel.php";
session_start();
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./HomeController.php');
    exit();
}

use Model\User;
use Model\Auth;

if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    $_SERVER['CONTENT_TYPE'] === 'application/json'
) {
    $jsonData = json_decode(file_get_contents('php://input'), true);

    if (isset($jsonData['data']) && !empty($jsonData['data']) && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $user = User::fetch($userId);
        $passwordhash = User::getPasswordHash($userId);

        if ($user) {
            $auth = new Auth($jsonData['data'], $user->getEmail(), null);
            $isValid = ($auth->getPasswordHash() === $passwordhash);

            header('Content-Type: application/json');
            echo json_encode([
                'status' => $isValid ? 'success' : 'error',
                'message' => $isValid ? 'Mot de passe correct' : 'Votre ancien mot de passe est incorrect'
            ]);
            exit;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

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

                    if (htmlspecialchars($formData['theme']) === 'dark') {
                        $theme = 1;
                    } else {
                        $theme = 2;
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
                        $dateOfBirth,
                        $theme
                    );

                    if ($user) {
                        $_SESSION['user_id'] = $user->getId();
                        $theme = $user->getTheme() === 1 ? 'dark' : 'light';
                        $_SESSION['theme'] = $theme;
                        
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
