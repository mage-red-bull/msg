<?php

require './include/Constants.php';

$errorMsg   = '';
$infoMsg    = '';
$successMsg = '';
$warningMsg = '';

// Si des données sont transmises
if (isset($_POST['message'])) {
    // 'false' est le message reçu si une erreur à eu lieu pendant le chiffrement
    if ($_POST['message'] == 'false') {
        $errorMsg = 'Non transmis. Erreur lors de la sécuriation ou l\'envoi du message !! :-(';
    } else if ($_POST['message'] == '') {
        $errorMsg = 'Non transmis. Message vide !! :-(';
    } else {
        // Envoi par email
        $res = mail(
                'georget.maxime@gmail.com',
                'Message',
                $_POST['message']
        );
        if ($res === FALSE) { // Erreur lors de l'envoi
            $errorMsg = 'Non transmis. Erreur lors de l\'envoi du message !! :-(';
        }
    }
    // Préparation des messages d'informations
    if (empty($errorMsg)) {
        $successMsg = 'Message envoyé !! ;-)';
        $warningMsg = "Message envoyé sous la forme :\n" . wordwrap($_POST['message'], 20, "\r\n", true);
    }
}

// On transmet la clé publique
$publicKey = file_get_contents(Constants::PUBLIC_KEY_PATH);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Message :-)</title>
    <style>
        body {
            padding: 50px;
            text-align: center;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        h1 {
            margin-bottom: 30px;
        }
        textarea {
            resize: vertical;
            padding: 10px 15px;
            border: 2px solid black;
            border-radius: 5px;
        }
        a {
            color: inherit;
            cursor: pointer;
        }
        input[type="submit"] {
            width: 150px;
            margin-top: 30px;
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
            border-radius: 5px;
            cursor: pointer;
        }
        .bg {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        .bg-danger {
            color: #a94442;
            background-color: #f2dede;
            border: 2px solid #ebccd1;
        }
        .bg-info {
            color: #31708f;
            background-color: #d9edf7;
            border: 2px solid #bce8f1;
        }
        .bg-success {
            color: #3c763d;
            background-color: #dff0d8;
            border: 2px solid #d6e9c6;
        }
        .bg-warning {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border: 2px solid #faebcc;
        }
        .bg br {
            display: none;
        }
    </style>
    <script type="text/javascript" src="./include/jquery.min.js"></script>
    <script type="text/javascript" src="./include/jsencrypt.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#formMessage").on('submit', function () {
                var inputMessage = $('#message');

                // Si message vide
                if (inputMessage.val() == '') {
                    $("#formMessage").after('<p class="bg bg-danger">Le message ne doit pas être vide.</p>');
                    return false;
                }

                // Le message doit être différent de 'false'
                if (inputMessage.val() == 'false') {
                    $("#formMessage").after('<p class="bg bg-danger">Le message doit être différent de "false".</p>');
                    return false;
                }

                // Si la taille du message est supérieure à la limite fixée
                if (inputMessage.val().length > <?php echo Constants::MESSAGE_SIZE_MAX ?>) {
                    $("#formMessage").after('<p class="bg bg-danger">Le message doit avoir une taille inférieure à <?php echo Constants::MESSAGE_SIZE_MAX ?> caractères.</p>');
                    return false;
                }

                var inputPublicKey = $('#publicKey');
                var encrypt = new JSEncrypt();
                encrypt.setPublicKey(inputPublicKey.val());

                var encrypted = encrypt.encrypt(inputMessage.val());

                if (encrypted == false) {
                    $("#formMessage").after('<p class="bg bg-danger">Une erreur s\'est produite pendant la sécurisation du message.</p>');
                    return false;
                }

                inputMessage.val(encrypted);
            });

            $("#message").on('keyup', function () {
                $("#nbRemainingChar").text(<?php echo Constants::MESSAGE_SIZE_MAX ?> - $("#message").val().length);
            });
        });
    </script>
</head>
<body>
    <div id="divBody">
        <img src="img/secure.png" alt="Secure">
        <h1>Message :-)</h1>
        <form id="formMessage" method="POST" action="./">
            <p id="pRemainingChar">Caractères restants : <span id="nbRemainingChar"><?php echo Constants::MESSAGE_SIZE_MAX ?></span></p>
            <textarea id="message" name="message" required cols="70" rows="7"
                      maxlength="<?php echo Constants::MESSAGE_SIZE_MAX ?>"
                      placeholder="/!\ N'oubliez pas de dire qui vous êtes ;-)  (surnom/initiales/prénom)"></textarea><br>
            <input type="submit" value="Go !!" class="btn btn-success" />
        </form>
        <input type="hidden" id="publicKey" value="<?php echo $publicKey; ?>">
        <?php if (!empty($errorMsg)) { ?>
            <p class="bg bg-danger">
                <?php echo nl2br($errorMsg); ?>
            </p>
        <?php } ?>
        <?php if (!empty($successMsg)) { ?>
            <p class="bg bg-success">
                <?php echo nl2br($successMsg); ?>
            </p>
        <?php } ?>
        <?php if (!empty($infoMsg)) { ?>
            <p class="bg bg-info">
                <?php echo nl2br($infoMsg); ?>
            </p>
        <?php } ?>
        <?php if (!empty($warningMsg)) { ?>
            <p class="bg bg-warning">
                <?php echo nl2br($warningMsg); ?>
            </p>
        <?php } ?>
        <br>
        <br>
        <br>
        <p class="bg bg-warning">
            <a href="../oldMsg/">Si cette page ne fonctionne pas =( clique !</a>
        </p>
    </div>
</body>
</html>
