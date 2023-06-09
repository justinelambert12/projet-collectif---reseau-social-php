<?php 
if (isset($_SESSION['connected_id'])) {
    //Définition des variables utiles
    $connectedId = $_SESSION['connected_id'];
    $queryURL = ($_SERVER['QUERY_STRING']!=="") ? ("?".$_SERVER['QUERY_STRING']) : "";
    $pageURL = $_SERVER['PHP_SELF'] . $queryURL;

    //Formulaire pour ajouter un like avec l'utilisateur connecté sous un post qui n'est pas le sien
    $postId = $post['id'];
    if ($post['user_id'] != $connectedId) {
        $alreadyLikedSQL = "SELECT * FROM likes WHERE user_id=$connectedId AND post_id=$postId";
        $alreadyLikedInfo = $mysqli -> query($alreadyLikedSQL);
        if (!$alreadyLikedInfo) {
            echo("Echec de la requete : " . $mysqli->error);
        } else {
    ?>
        <form action="<?php echo $pageURL; ?>" method="post">
            <input type="hidden" name="postId" value="<?php echo $postId ?>">
        <?php if ($alreadyLikedInfo->num_rows > 0) { // Déjà liké ?>
            <input type="hidden" name="like" value="NOK">
            <input type="submit" value="Unlike">
        <?php } else { // Pour liker ?>
            <input type="hidden" name="like" value="OK">
            <input type="submit" value="Like">
        <?php }; ?>
        </form>
    <?php };
    }; 
};?>