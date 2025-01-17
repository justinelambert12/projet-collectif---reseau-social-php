<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php include 'header.php'; ?>

        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : intval($_POST['user_id']);

            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <?php 
        if (isset($_POST['message'])){
        $authorId = $_POST['user_id'];
        $postContent = $_POST['message'];

        $authorId = intval($mysqli->real_escape_string($authorId));
        $postContent = $mysqli->real_escape_string($postContent);
        
        $lInstructionSql = "INSERT INTO posts "
                . "(id, user_id, content, created, parent_id) "
                . "VALUES (NULL, "
                . $authorId . ", "
                . "'" . $postContent . "', "
                . "NOW(), "
                . "NULL);"
                ;
        // echo $lInstructionSql;
                
            $ok = $mysqli->query($lInstructionSql);
            if ( ! $ok)
            {
                echo "Impossible d'ajouter le message: " . $mysqli->error;
            } else
            {
                // echo "Message posté en tant que :" . $authorId;
            }
        } ?>

            <aside>          
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */                
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
               // echo "<pre>" . print_r($user, 1) . "</pre>";
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias']; ?>
                        (n° <?php echo $userId ?>)
                    </p>
                    <?php include "follow.php" ?>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, posts.id, users.alias as author_name, 
                    users.id as user_id,
                    COUNT(DISTINCT likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                { 
                    // echo "<pre>" . print_r($post, 1) . "</pre>";
                    include "post.php";
                } ?>
                <?php if( isset($_SESSION['connected_id']) && $userId == $_SESSION['connected_id']) {?>  
                    <form method="post" action="wall.php">
                    <textarea name="message" placeholder="Entrez votre message" cols="80" rows="5"></textarea>
                    <input type='hidden' name='user_id' value=<?php echo $userId ?>>
                    <input type="submit" value="Envoyer" />
                </form>
                <?php } ?>
            </main>
        </div>
    </body>
</html>