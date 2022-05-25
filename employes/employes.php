<?php
    
    require __DIR__ . "/db/connection.php";

    $req = $db->prepare("SELECT * FROM employes");
    $req->execute();
    $row = $req->rowCount();

    $employes = [];
    
    if ( $row > 0 ) 
    {
        $employes = $req->fetchAll();
    }


    
    
?>

<!-- -------------------------------------View------------------------------  -->

<?php $title = "Liste des employes"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Liste des employés</h1>
    
        <div class="d-flex justify-content-end align-items-center">
            <a href="create.php" class="btn btn-primary">Ajouter</a>
        </div>
        <table>
        <th class='bg-dark'>
            <td>Id</td>
            <td>Prénom</td>
            <td>Date De Naissance</td>
            <td>Fonction</td>
            <td>Email</td>
            <td>Salaire</td>
            <td>Actions</td>
        </th>
        <?php if(count($employes) == 0) : ?>
            <p>Aucun employé ajouté à la liste.</p>
        <?php else : ?>
            
                <?php foreach($employes as $employe) : ?>
                    <tr>
                        <td><?= $employe['id'] ?></td>
                        <td><?= $employe['prenom'] ?></td>
                        <td><?= $employe['ddn'] ?></td>
                        <td><?= $employe['fonction'] ?></td>
                        <td><?= $employe['email'] ?></td>
                        <td><?= $employe['salaire'] ?></td>
                        <td><a href="edit.php?employe_id=<?= $employe['id'] ?>">Modifier</a> 
                        <a href="delete.php?employe_id=<?= $employe['id'] ?>">Supprimer</a>
                        <a href="info.php?employe_id=<?= $employe['id'] ?>">I</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            
        <?php endif ?>
        </table>
    </main>
    <!-- End the specific content for this page -->

<?php require __DIR__ . "/partials/foot.php"; ?>