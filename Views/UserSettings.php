<DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="/css/General.css" />
		<title>VÉVI - Paramètres du compte</title>
		<link rel="icon" href="/images/logo.png"/>
	</head>

	<body>
		<header><img src="/images/logoComplet.png" /></header>

        <?php
        if (isset($params['updateError']))
        {
            echo "<p style='color: red'>
                Une erreur est survenue, merci de réessayer.
                </p>";
        }

        if (isset($params['passwordsMismatch']))
        {
            echo "<p style='color: red'>
                Les mots de passes ne correspondent pas.
                </p>";
        }

        if (isset($params['success']))
        {
            echo "<p style='color: greenyellow'>
                Informations mises à jour avec succès.
                </p>";
        }

        if (isset($params['unConfirmed']))
        {
            echo "<p style='color: greenyellow'>
                Suppression du compte non confirmée.
                </p>";
        }

        ?>

        <br/>
        <h2>Identité : <?php echo $params['user']['username']; ?> </h2>


        <form action="/user/settings/update" method="post">
				
                <input type="text" id="username" name="username" minlength="4" maxlength="40" size="15" placeholder="Nom d'utilisateur" align="center" autofocus required
                       value="<?php echo $params['user']['username']; ?>">
                <br/>
                <input type="text" id="fistName" name="firstName" minlength="4" maxlength="20" size="15" placeholder="Prénom" align="center" required
                       value="<?php echo $params['user']['firstName']; ?>">
                <br/>
                <input type="text" id="lastName" name="lastName" minlength="4" maxlength="20" size="15" placeholder="Nom" align="center" required
                       value="<?php echo $params['user']['lastName']; ?>">
                <br/>
                <input type="email" id="email" name="email" minlength="4" maxlength="50" size="15" placeholder="Couriel" align="center" required
                       value="<?php echo $params['user']['email']; ?>">
                <br/>
				<input type="password" id="password" name="password" minlength="6" maxlength="80" placeholder="Mot de passe"
                       value="">
				<br/>
				<input type="password" id="passwordConfirm" name="passwordConfirm" minlength="6" maxlength="80" placeholder="Confirmation du mot de passe"
                       value="">
				<br/>
				<button type="submit">Appliquer les modifications</button>
        </form>
        <form action="/user/delete" method="post">
            <button type="submit" id="deleteUser", name="confirm", value="true">Supprimer le compte utilisateur</button>
        </form>

        <form action="/home">
            <button type="back">Retour</button>
        </form>

	</body>