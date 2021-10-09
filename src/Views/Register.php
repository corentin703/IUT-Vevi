<DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="../css/General.css" />
		<title>VÉVI - S'enregistrer</title>
		<link rel="icon" href="../images/logo.png"/>
	</head>

	<body>
		<header><img src="../images/logoComplet.png" /></header>

        <?php
        if (isset($params['insertError']) && $params['insertError'] === true)
        {
            echo "<p style='color: red'>
                Une erreur est survenue, merci de réessayer.
                </p>";
        }

        if (isset($params['passwordsDMatch']) && $params['passwordsDMatch'] === true)
        {
            echo "<p style='color: red'>
                Les mots de passes ne correspondent pas.
                </p>";
        }

        if (isset($params['userAlreadyExist']) && $params['userAlreadyExist'] === true)
        {
            echo "<p style='color: red'>
                  Un utilisateur du même nom existe déjà.
                  </p>";
        }

        ?>


        <form action="/tryRegister" method="post">
				<h1>S'enregistrer</h1>
                <input type="text" id="username" name="username" minlength="4" maxlength="40" size="15" placeholder="Nom d'utilisateur" align="center" autofocus required
                       value="<?php if (isset($params['userInfos'])) echo $params['userInfos']['username']; ?>">
                <br/>
                <input type="text" id="fistName" name="firstName" minlength="4" maxlength="20" size="15" placeholder="Prénom" align="center" required
                       value="<?php if (isset($params['userInfos'])) echo $params['userInfos']['firstName']; ?>">
                <br/>
                <input type="text" id="lastName" name="lastName" minlength="4" maxlength="20" size="15" placeholder="Nom" align="center" required
                       value="<?php if (isset($params['userInfos'])) echo $params['userInfos']['lastName']; ?>">
                <br/>
                <input type="email" id="email" name="email" minlength="4" maxlength="50" size="15" placeholder="Email" align="center" required
                       value="<?php if (isset($params['userInfos'])) echo $params['userInfos']['email']; ?>">
                <br/>
				<input type="password" id="password" name="password" minlength="6" maxlength="80" placeholder="Mot de passe" required
                       value="">
				<br/>
				<input type="password" id="passwordConfirm" name="passwordConfirm" minlength="6" maxlength="80" placeholder="Confirmation du mot de passe" required
                       value="">
				<br/>
				<button type="register">Soumettre</button>
        </form>
        <form action="/">
            <button type="back">Retour</button>
        </form>

	</body>