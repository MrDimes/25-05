<?php

    if ( !isset($_GET['employe_id']) || empty($_GET['employe_id']) ) 
    {
        return header("Location: index.php");
    }

    $employe_id = (int) strip_tags(trim($_GET['employe_id']));

    require __DIR__ . "/db/connection.php";

    $req = $db->prepare("DELETE FROM employes WHERE id=:id");
    $req->bindValue(":id", $employe_id);
    $req->execute();
    $req->closeCursor();

    return header("Location: employes.php");