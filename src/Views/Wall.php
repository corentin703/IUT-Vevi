<DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<html>
<head>
	<link rel="stylesheet" href="/css/General.css"/>
<title>VÉVI - Mur</title>
	<link rel="icon" href="/images/logo.png"/>
</head>
<script type="text/javascript" src="/scripts/Wall.js"></script>
<body >
<header>
	<img src="/images/logoComplet.png" />
    <div id = "searchbar">
        <form action ="/user/search" class="formulaire">
             <form action="" class="formulaire" method="post">
               <input class="champ" type="text" placeholder="Rechercher..." name="searchString"/>
        </form>
    </div>
</header>
<?php
        if (isset($params['userNotFound']) && $params['userNotFound'] === true)
        {
            echo "<p style='color: red'>
                Aucun utilisteur correspondant trouvé
                </p>";
        }

        $username = $params['currentUser']['username']; ?>
<h2 class="utilisateur">Connecté en tant que : <a href="/user/wall/<?php echo $username . '">' . $username?></a></h2>
<div>
<?php
if ((!isset($params['user']) || ($params['user']['id'] === $params['currentUser']['id'])) && !isset($params['search'])) // Si on est pas sur le mur de quelqu'un d'autre on peut poster
{
        echo '<form action="/home/post" method="post">
	            <h2 class="NouveauPost" id="publicaction" for="publication">Nouveau post :</h2><br/><br/>
	            <textarea id="tweet" name="content" rows="5" cols="60" maxlength="140" onclick="if(this.value==\'Écrivez ^^\') { this.value=\'\'; }">Écrivez ^^</textarea><br/>
                <!-- <input type="button" value="Joindre"> -->
	            <button type="submit">Envoyer</button>';
}
?>




    </form>

    <posts class='Test'>
        <?php
        if (isset($params['posts']) && count($params['posts']) !== 0)
        {
            if (isset($params['allUser']))
                echo "<h2 class='Dernier'>Derniers tweets :</h2>";
            else if (isset($params['user']) && ($params['user']['id'] === $params['currentUser']['id']))
                echo "<h2 class='Dernier'>Vos posts :</h2>";
            else if (isset($params['user']))
                echo "<h2 class='Dernier'>Les posts de " . $params['user']['username'] . " :</h2>";

//          Affichage des posts
            foreach ($params['posts'] as $post)
            {
                $alterable = false; // Par défaut, l'utilisateur n'a pas de droit de modification

                if ($params['currentUser']['id'] === $post['userId'])
                {
                    $user = "Vous";
                    $avoir = " avez ";
                    $alterable = true; // Le tweet appartient à l'utilisateur alors on affiche les bouttons d'édition
                }
                else
                {
                    $user = $post['user']['username'];
                    $avoir = " a ";
                }

                echo "<br/>";

                if (isset($post['postId'])) // S'il s'agit d'un repost (un simple post ne contient pas l'Id d'un post de référence)
                {
                    echo $user . $avoir . ' reposté un message de ' . $post['author']['username'] .  ' le ' . date('d/m/y' ,$post['date']) . ' à ' . date('G:i' ,$post['date']) . ' : <br/>';
                    echo '<textarea id="tweet" name="content" readonly >' . $post['text'] . '</textarea> <br/>';

                    if ($alterable) // Si l'utilisateur a les droit, possibilité de supprimer le post
                        echo '<form action="/home/deleteRepost" method="post"> <button id="remove" name="repostId" value="' . $post['id'] . '">Supprimer</button> </form>';

                    else // Sinon, on affiche les boutons j'aime / je n'aime pas / reposter
                    {
                        echo '<form action="/home/likeRepost" method="post"> <button id="likeType" name="repostId" value="' . $post['id'] . '">J\'aime (' . $post['likes'] . ')</button></form>';
                        echo '<form action="/home/dislikeRepost" method="post"> <button id="likeType" name="repostId" value="' . $post['id'] . '">Je n\'aime pas (' . $post['dislikes'] . ')</button> </form>';
                    }
                }
                else // S'il s'agit d'un post à part entière
                {
                    echo $user . $avoir . ' écrit le ' . date('d/m/y' ,$post['date']) . ' à ' . date('G:i' ,$post['date']) . ' : <br/>';

                    if ($alterable) // L'auteur peut modifier le texte de son post
                        echo '<form action="/home/modifyPost" method="post">'; // On donne au propriétaire la possibilité de modifier le post

                    echo '<textarea id="tweet" name="content"';
                    if (!$alterable) // Mise en lecture seule si l'utilisateur n'a pas de droit sur le post
                        echo 'readonly';
                    echo '>' . $post['text'] . '</textarea> <br/>';

                    if ($alterable)
                    {
                        echo $post['likes'] . ' j\'aime(s) et ' . $post['dislikes'] . ' je n\'aime pas'; // Affichage du nombre de j'aime/je n'aime pas
                        echo '<button id="modify" name="postId" value="' . $post['id'] . '">Enregistrer modifications</button> </form>';
                    }

                    if ($alterable) // Si l'utilisateur a les droit, possibilité de supprimer le post
                        echo '<form action="/home/deletePost" method="post"> <button id="remove" name="postId" value="' . $post['id'] . '">Supprimer</button> </form>';

                    else // Sinon, on affiche les boutons j'aime / je n'aime pas / reposter
                    {
                        echo '<form action="/home/likePost" method="post"> <button id="likeType" name="postId" value="' . $post['id'] . '">J\'aime (' . $post['likes'] . ')</button></form>';
                        echo '<form action="/home/dislikePost" method="post"> <button id="likeType" name="postId" value="' . $post['id'] . '">Je n\'aime pas (' . $post['dislikes'] . ')</button> </form>';
                        echo '<form action="/home/repost" method="post"> <button id="repost" name="postId" value="' . $post['id'] . '">Reposter</button></form>';
                    }
                }

                // On initialise les boutons (en fonction de si l'utilisateur a aimé ou non)
                echo '<script type="text/javascript"> 
                        var $like = ' . $post['userLikeDatas'] . ';
                        initializeLikeButton($like);
                    </script>';
            }
        }
        else
        {
            if (isset($params['allUser']))
                echo '<h2>Aucun tweet disponible, veuillez suivre des utilisateurs.</h2>';
            else if (isset($params['user']) && ($params['user']['id'] === $params['currentUser']['id']))
                echo "<h2 class='Dernier'>Vous n'avez pas encore posté de contenu.</h2>";
            else if (isset($params['user']))
                echo "<h2 class='Dernier'>" . $params['user']['username'] . " n'a pas encore posté de contenu.</h2>";
        }

        ?>


    </posts>

    <user class='Test'>
        <?php
        if (isset($params['allUsers']))
        {
            if (count($params['allUsers']) !== 1)
            {
                echo '
                <h2>Des utilisateurs que vous pouvez suivre :</h2>
                <br/>';

                foreach ($params['allUsers'] as $user)
                {
                    if ($user['id'] !== $params['currentUser']['id'])
                    {
                        echo '<form action="/user/follow" method="post"> <a href="/user/wall/' . $user['username'] . '">' .
                            $user['username'] . ' : </a> 
                            <button id="followType" name="userId" value="' . $user['id'] . '">Suivre</button>
                        </form>';

                        echo '<script type="text/javascript">
                        var $follow = ' . $user['isFollowed'] . ';
                        initializeFollowButton($follow);
                    </script>';
                    }
                }
            }
            else if (!isset($params['search']))
            {
                echo '<h2>Vous êtes le premier utilisateur de Vévi, vous ne pouvez suivre personne</h2>
                       <br/>';
            }
        }
        ?>
    </user>

    <br/>
    <br/>
    <?php
    if (isset($params['user']) || isset($params['search']))
        echo '<form action="/home">
                <button id="back" type="submit">Accueil</button>
               </form>';
    if (isset($params['user']) && ($params['user']['id'] === $params['currentUser']['id']))
        echo '<form action="/user/settings">
                <button id="settings" type="submit">Paramètres du compte</button>
               </form>';
    ?>
    <form action="/">
        <button id="disconnect" type="submit">Se déconnecter</button>
    </form>

</body>
</html>

