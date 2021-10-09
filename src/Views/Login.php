<!DOCTYPE html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	 	<link rel="stylesheet" href="../css/General.css" />
		<title>VÉVI - Connexion</title>
		<link rel="icon" href="../images/logo.png"/>
	</head>
	<body class="class-color">
		<header><img src="../images/logoComplet.png" /></header>

        <?php
        if (isset($params['registered']) && $params['registered'] === true)
        {
            echo "<p style='color: greenyellow'>
                Vous avez été enregistré avec succès.
                </p>";
        }

        if (isset($params['errorLI']) && $params['errorLI'] === true)
        {
            echo "<p style='color: red'>
                Une erreur est survenue, merci de réessayer.
                </p>";
        }
        ?>
        <br/>
        <br/>

            <!-- Demande de saisie du nom et du mot de passe -->
		<form action="/login" method="post">
            <div>
			    <input type="text" id="name" name="username" minlength="4" maxlength="50" size="15" placeholder="Nom d'utilisateur" autofocus align="center" required
                value="<?php if (isset($params['username'])) echo $params['username']; ?>">
		    </div>
		    <div>
			    <p><input type="password" id="Mot de passe" name="password" minlength="6" maxlength="80" placeholder="Mot de passe" required
                    value="<?php if (isset($params['password'])) echo $params['password']; ?>"></p><!-- Le mot de pass est caché -->
  		    </div>
  		    <br/>
                <button type="login">Confirmer</button>
            <br/>
        </form>

        <form action="/register">
            <button type="register">S'inscrire</button>
        </form>

	</body>
	</html>