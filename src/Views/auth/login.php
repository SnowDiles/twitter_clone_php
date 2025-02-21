<!doctype html>
<html class="dark">

<head>
    <title>Y - login</title>
    <link rel="stylesheet" href="../../public/css/login.css">
    <?php include_once("../_partials/_head.php") ?>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body data-theme="my-custom-theme">


    <div class="flex flex-col gap-10 justify-center items-center h-screen column" id="main">
        <div>
            <img src="../../assets/icons/logo.png" alt="Logo" class=" logo" />
        </div>
        <div>
            <div>
                <p >Inscrivez-vous.</p>
                <button class="btn variant-filled-primary text-secondary-500 rounded-lg" id="registerOpenModal">Créer
                    un compte</button>
            </div>
            <div class="divider"></div>
            <div>
                <p>Vous avez deja un compte ?</p>
                <button class="btn variant-outlined btn2 rounded-lg" id="connexionOpenModal">Se connecter</button>
            </div>
        </div>
    </div>


    <div class="modal-container" id="ModalContainer">
        <div class="modal " style="display:none;" id="modal">
            <img src="../../assets/icons/close.png" alt="close" class="close-logo" id="closeButton" />
            <div>
                <img src="../../assets/icons/logo.png" alt="Logo" class="logo-modal" />
            </div>
            <div>
                <h1 class="modal-title" id="modalTitle">Inscrivez-vous ! </h1>
            </div>

            <div class="form-container">
                <form action="" method="POST">
                    <input type="text" placeholder="Nom" required name="name" id="nameInput"/>
                    <input type="email" placeholder="Email" required name="email" />
                    <input type="text" placeholder="Pseudo" required name="username" />
                    <input type="password" placeholder="Mot de passe" required  name="password" />
                    <input type="password" placeholder="Confirmez le mot de passe " required name="confirmPassword" />
                    <div class="form-container--button">
                        <button type="submit" class="btn variant-filled-primary text-secondary-500 rounded-lg btn-register">Inscription</button>
                        <button type="submit" class="btn variant-outline-primary btn3 rounded-lg">Déjà un compte ?</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="../../public/js/login.js"></script>
</body>

</html>