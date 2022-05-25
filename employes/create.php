<?php
session_start(); // Autorisation pour utiliser les sessions

    // Si la méthode d'envoi des données est "POST"
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        // var_dump($_POST); die();

        $post_clean = [];
        $errors     = [];

        // Protégeons-nous contre les failles de type XSS
        foreach ($_POST as $key => $value) 
        {
            $post_clean[$key] = strip_tags(trim($value));
        }

        
        // Procédons à la validation des données de chaque input

        // 1) prenom
        if ( isset($post_clean['prenom']) ) 
        {
            if (empty($post_clean['prenom'])) 
            {
                $errors['prenom'] = "Le prenom  est obligatoire.";
            }
            else if( mb_strlen($post_clean['prenom']) > 50 )
            {
                $errors['prenom'] = "Le prenom doit contenir au maximum 50 carcatères.";
            }
        }

        // 2) date de naissance
        if ( isset($post_clean['ddn']) ) 
        {
            if (empty($post_clean['ddn'])) 
            {
                $errors['ddn'] = "La date de naissance est obligatoire.";
            }
        }

        // 3- email
        if ( isset($post_clean['email']) ) 
        {
            if (empty($post_clean['email'])) 
            {
                $errors['email'] = "Veuillez saisir un email";
            }
        }

        // 4- fonction
        if ( isset($post_clean['fonction']) ) 
        {
            if (empty($post_clean['fonction'])) 
            {
                $errors['fonction'] = "Veuillez saisir une fonction";
            }
            else if( mb_strlen($post_clean['fonction']) > 25 )
            {
                $errors['fonction'] = "La fonction doit contenir au maximum 25 carcatères.";
            }
        }

        // 5- salaire
        if ( isset($post_clean['salaire']) ) 
        {
            if (empty($post_clean['salaire'])) 
            {
                $errors['salaire'] = "Veuillez saisir un salaire";
            }
            else if( ! is_numeric($post_clean['salaire']) )
            {
                $errors['salaire'] = "Veuillez entrer un nombre.";
            }
        }


        /*
         * Si il y a des erreurs, 
         * on redirige l'utilisateur vers la page de laquelle proviennent les informations avec 
         * et on arrete l'exécution du script 
         * les messages d'erreurs qui vont avec
        */
        if ( count($errors) > 0 ) 
        {
            $_SESSION['old']    = $post_clean;
            $_SESSION['errors'] = $errors;
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }



        /*
         * Dans le cas où il n'y a pas d'erreurs, on insère les données dans la table 'employes' de la base de données  
         */

        // connection à la base de données
        require __DIR__ . "/db/connection.php";

        // Requête d'insertion des données dans la table 'employes'
        $req = $db->prepare("INSERT INTO employes (prenom, ddn, email, fonction, salaire) VALUES (:prenom, :ddn, :email, :fonction, :salaire ) ");

        // On envoie les vraies données
        $req->bindValue(":prenom",   $post_clean['prenom']);
        $req->bindValue(":ddn", $post_clean['ddn']);
        $req->bindValue(":email", $post_clean['email']);
        $req->bindValue(":fonction", $post_clean['fonction']);
        $req->bindValue(":salaire", $post_clean['salaire']);

        // On exécute la requête
        $req->execute();

        // On ferme la connexion avec la base de données (optionnel)
        $req->closeCursor();

        // Redirection vers la page employes
        return header("Location: employes.php");

    }
?>

<!-- ------------------------------------View------------------------------- -->

<?php $title = "Nouvel employé"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Création employé</h1>

        <div class="container">

            <?php if( isset($_SESSION['errors']) && !empty($_SESSION['errors']) ) : ?>
                <div>
                    <ol>
                        <?php foreach( $_SESSION['errors'] as $error ) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </ol>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="prenom">Prénom de l'employé</label>
                    <input type="text" name="prenom" class="form-control" id="prenom" value="<?php echo $_SESSION['old']['prenom'] ?? $employe['prenom']; unset($_SESSION['old']['prenom']); ?>" >
                </div>
                <div class="mb-3">
                    <label for="ddn">Date de naissance</label>
                    <input type="date" name="ddn" class="form-control" id="ddn" value="<?php echo $_SESSION['old']['ddn'] ?? $employe['ddn']; unset($_SESSION['old']['ddn']); ?>">
                </div>
                <div class="mb-3">
                    <label for="email">email</label>
                    <input type="text" name="email" class="form-control" id="email" value="<?php echo $_SESSION['old']['email'] ?? $employe['email']; unset($_SESSION['old']['email']); ?>">
                </div>
                <div class="mb-3">
                    <label for="fonction">Fonction de l'employé</label>
                    <input type="text" name="fonction" class="form-control" id="fonction" value="<?php echo $_SESSION['old']['fonction'] ?? $employe['fonction']; unset($_SESSION['old']['fonction']); ?>" >
                </div>
                <div class="mb-3">
                    <label for="salaire">Salaire de l'employé</label>
                    <input type="text" name="salaire" class="form-control" id="salaire" value="<?php echo $_SESSION['old']['salaire'] ?? $employe['salaire']; unset($_SESSION['old']['salaire']); ?>" >
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary" />
                </div>
            </form>
        </div>

    </main>
    <!-- End the specific content for this page -->

<?php require __DIR__ . "/partials/foot.php"; ?>