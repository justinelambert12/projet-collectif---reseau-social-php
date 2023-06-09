<?php
session_start();
if (isset($_SESSION['connected_id'])) {
    $connectedId = $_SESSION['connected_id'];
}
    $queryURL = ($_SERVER['QUERY_STRING']!=="") ? ("?".$_SERVER['QUERY_STRING']) : "";
    $pageURL = $_SERVER['PHP_SELF'] . $queryURL;

    //Réception du formulaire et gestion de l'ajout ou suppression de like
    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
    if (isset($_POST['like'])) {
        $postId = $_POST['postId'];
        if ($_POST['like']==="OK") { //Ajout d'un like
            $likeSQL = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $connectedId, $postId);";
        } else { //Suppression d'un like ($_POST['like']="NOK")
            $likeSQL = "DELETE FROM likes WHERE user_id=$connectedId AND post_id=$postId;";
        }
        //Exécution de la requête
        $ok = $mysqli->query($likeSQL);
        if(!$ok) {
            echo "Impossible de liker ou unliker : " . $mysqli->error;
        }
    }
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Connexion</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
<header>
            <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
            <nav id="menu">
                <a href="news.php">Actualités</a>
            <?php if(isset($connectedId) && $connectedId!==""): ?>
                <a href="wall.php?user_id=<?php echo $connectedId; ?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $connectedId; ?>">Flux</a>
            <?php endif; ?>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
            <?php if(isset($connectedId) && $connectedId!=="") {?> 
                <a href="#">▾ Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $connectedId; ?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $connectedId; ?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $connectedId; ?>">Mes abonnements</a></li>
                    <li><a href="logout.php">Se déconnecter</a></li>
                </ul>
                <?php }
                else {?> 
                    <a href='login.php'> Login </a>
                <?php }?>
            </nav>              
</header>
