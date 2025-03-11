<!doctype html>
<html class="dark">

<head>
    <title>Y - login</title>
    <?php include_once("../_partials/_head.php") ?>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body data-theme="my-custom-theme">


    <div class="flex flex-col items-center sm:justify-between justify-center mx-auto h-screen sm:flex-row">
        <div class="w-1/2 h-1/4 sm:h-1/2 flex justify-center">
            <img src="../../assets/icons/logo.png" alt="Logo" class="sm:w-70 ">
        </div>
        <div class="w-1/2 space-y-4 flex-col h-1/2 min-w-60 flex justify-center" style="align-items:center">
            <h2 class="sm:text-lg  text-sm font-semibold bg-red ">Inscrivez-vous.</h2>
            <button class="btn variant-filled-primary  w-2/6 min-w-60  text-red rounded-md" style="color:white;"
                id="register-open-modal">Créer un compte</button>
            <p class="sm:text-lg  text-sm font-semibold">Vous avez déjà un compte ?</p>
            <button class="btn variant-outline-secondary w-2/6 min-w-60 text-blue-600 rounded-md"
                id="login-open-modal">Se connecter</button>
        </div>
    </div>

    <div class="fixed inset-0 flex items-center justify-center bg-slate-100  bg-opacity-60" id="modal-container"
        style="display:none">
        <div class="bg-black p-8 rounded-lg max-w-md relative w-4/6 ">
            <button class="absolute top-4 right-4 text-white">
                <svg id="close-button" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-x">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
            <div class="flex justify-center mb-6">
                <img src="../../assets/icons/logo.png" alt="Logo" class="sm:w-10 w-10">
            </div>
            <h2 class="text-white text-2xl font-medium text-center mb-8" id="modal-title">
                Inscrivez-vous !
            </h2>
            <form method="POST" class="space-y-4">
                <input type="text" name="nom" placeholder="Nom" id="name-input" maxlength="10"
                    class="w-full bg-black border border-gray-700 rounded px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
                    required />
                <input type="email" name="email" placeholder="Email"
                    class="w-full bg-black border border-gray-700 rounded px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
                    required />
                <input type="text" name="username" placeholder="@pseudo" id="username-input" maxlength="10"
                    class="w-full bg-black border border-gray-700 rounded px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
                    required />
                <input type="password" name="password" placeholder="Mot de passe "
                    class="w-full bg-black border border-gray-700 rounded px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
                    required />
                <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" id="confirm-password"
                    class="w-full bg-black border border-gray-700 rounded px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
                    required />
                <button type="submit"
                    class="w-full bg-blue-500 text-white rounded py-2 font-medium hover:bg-blue-600 transition-colors"
                    id="login-button">
                    Inscription
                </button>
                <a
                    class="block w-full border border-gray-800 text-white rounded py-2 hover:bg-gray-900 transition-colors text-center"
                    id="existing-account-button">
                    Déjà un compte ?
                </a>
            </form>
        </div>
    </div>



    <script src="../../public/js/login.js"></script>
</body>

</html>