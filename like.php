<?php 
if (isset($_SESSION['connected_id'])) {
    //Définition des variables utiles
    $connectedId = $_SESSION['connected_id'];
    $postId = $post['id'];
    $queryURL = ($_SERVER['QUERY_STRING']!=="") ? ("?".$_SERVER['QUERY_STRING']) : "";
    $pageURL = $_SERVER['PHP_SELF'] . $queryURL;

    //Gestion de l'ajout ou suppression de like en fonction du formulaire envoyé
    if (isset($_POST['like'])) {
        if ($_POST['like']==="OK") {
            $likeSQL = "INSERT INTO likes (id, user_id, post_id) VALUES (NULL, $connectedId, $postId);";
        } else { // $_POST['like']="NOK"
            $likeSQL = "DELETE FROM likes WHERE user_id=$connectedId AND post_id=$postId;";
        }
        //Exécution de la requête
        $ok = $mysqli->query($likeSQL);
        if(!$ok) {
            echo "Impossible d'effectuer l'abonnement/désabonnement: " . $mysqli->error;
        } else {//si la requête est validée, je recharge la page pour que le nombre de likes se mette à jour
            header("Location: $pageURL");
        }
    }

    //Formulaire pour ajouter un like avec l'utilisateur connecté sous un post qui n'est pas le sien
    if ($post['user_id']!=$_SESSION['connected_id']) {

        $alreadyLikedSQL = "SELECT * FROM likes WHERE user_id=$connectedId AND post_id=$postId";
        $alreadyLikedInfo = $mysqli -> query($alreadyLikedSQL);
        if (!$alreadyLikedInfo) {
            echo("Echec de la requete : " . $mysqli->error);
        } else {
    ?>
        <form action="<?php echo $pageURL; ?>" method="post">
        <?php if ($alreadyLikedInfo->num_rows > 0) { // Déjà liké ?>
            <input type="hidden" name="like" value="NOK">
            <input type="submit" value="Unlike">
        <?php } else { // Pour liker ?>
            <input type="hidden" name="like" value="OK">
            <input type="submit" value="Like">
        </form>
    <?php   };
        };
    }; 
};?>