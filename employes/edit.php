<?php
session_start();

    // Si l'identifiant envoyé est vide,
    // On redirige l'utilisateur sur la page index.php
    if ( !isset($_GET['employe_id']) || empty($_GET['employe_id']) ) 
    {
        return header("Location: index.php");
    }

    
    /*
    * On récupère l'id
    * On se protège contre les failles de type XSS
    * On convertit l'identifin-ant en nombre entier
    */
    $employe_id = (int) strip_tags(trim($_GET['employe_id']));

    // Si l'identifiant est vide, on redirige l'utilisateur vers index.php
    if ( empty($employe_id) ) 
    {
        return header("Location: index.php");
    }

    // On établit une nouvelle connexion avec la base de données
    require __DIR__ . "/db/connection.php";

    // On fait une requête pour sélectionner toutes les colonnes 
    // d'un seul enregistrement de la table "film" 
    $req = $db->prepare("SELECT * FROM employes WHERE id=:id");
    $req->bindValue(":id", $employe_id);
    $req->execute();

    // On récupère le nombre d'enregistrement
    $row = $req->rowCount();

    // Si ce nombre n'est pas égale à 1,
    // On redirige l'utilisateur vers la page de laquelle proviennent
    // les informations (index.php)
    if ( empty($row) || $row != 1 ) 
    {
        return header("Location: employes.php");
    }

    // Dans le cas contraire
    $employe = $req->fetch();

    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        $post_clean = [];
        $errors     = [];

        foreach ($_POST as $key => $value) 
        {
            $post_clean[$key] = strip_tags(trim($value));
        }

        // Validation des données

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

        
        if ( count($errors) > 0 ) 
        {
            // var_dump($errors); die();
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $post_clean;
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        require __DIR__ . "/db/connection.php";

        $req = $db->prepare("UPDATE employes SET prenom=:prenom, ddn=:ddn, email=:email, fonction=:fonction, salaire=:salaire WHERE id=:id ");

        $req->bindValue(":prenom",   $post_clean['prenom']);
        $req->bindValue(":ddn", $post_clean['ddn']);
        $req->bindValue(":email", $post_clean['email']);
        $req->bindValue(":fonction", $post_clean['fonction']);
        $req->bindValue(":salaire", $post_clean['salaire']);
        $req->bindValue(":id",     $employe['id']);

        $req->execute();
        $req->closeCursor();

        return header("Location: employes.php");
    }



    

?>

<!-- -------------------------------View-------------------------------  -->

<?php $title = "Modification d'un employé"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Modification de : <em><?= $employe['prenom'] ?><em></h1>

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