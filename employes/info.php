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
    
?>

<!-- -------------------------------------View------------------------------  -->

<?php $title = "Informations employé"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <main class="container-fluid">

        <h1 class="text-center my-5">Information de l'employé</h1>
        <div id='info'>
                        <p><?= $employe['prenom'] ?></p>
                        <p><?= $employe['fonction'] ?>   de l'entreprise</p>
                        <p>Contact  <?= $employe['email'] ?></p>
        </div>
    </main>

<?php require __DIR__ . "/partials/foot.php"; ?>