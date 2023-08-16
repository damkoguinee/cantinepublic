<?php 
require 'header.php';

if (isset($_GET['numcmdpaye'])) {

    $bdd='venteupdate';  

    $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `num_cmd` varchar(50) NOT NULL,
      `Total` double NOT NULL,
      `montantpaye` double NOT NULL,
      `num_client` int(11) DEFAULT NULL,
      `remise` float DEFAULT NULL,
      `date_cmd` datetime NOT NULL,
      `idpersonnel` int(11) DEFAULT NULL,
      `idtable` varchar(100) DEFAULT NULL,
      `dateop` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ");

    $_SESSION['update']=$_GET['numcmdpaye'];

    $produpdate= $DB->querys('SELECT *FROM payement  where num_cmd=:num ', array('num'=>$_SESSION['update']));

    $DB->insert('INSERT INTO venteupdate (num_cmd, Total, montantpaye, remise, num_client, date_cmd, idtable, idpersonnel, dateop) VALUES(?, ?, ?, ?, ?, ?, ?, ?, now())', array($produpdate['num_cmd'], $produpdate['Total'], $produpdate['montantpaye'], $produpdate['remise'], $produpdate['num_client'], $produpdate['date_cmd'], $produpdate['idtable'], $_SESSION['idpseudo']));

    foreach ($panier->email as $valuem) {

        $destinataire=$valuem;
        $message='bonjour, la vente N°'.$produpdate['num_cmd'].' de '.number_format($produpdate['Total'],0,',',' ').' a été modifiée par '.$panier->nomPersonnel($_SESSION["idpseudo"])[1];
        ini_set( 'display_errors', 1);
        error_reporting( E_ALL );
        $from = "codebar@damkoguinee.com";
        $to =$destinataire;
        $subject = "vente modifiée";
        $message = $message;
        $headers = "From:" . $from;
        mail($to,$subject,$message, $headers);
    }  

    
    
}?>

<div class="col">

    <div class="container-fluid p-0 m-0">
        <div class="row">

            <div class="col-sm-12 col-md-6 border border-5 border-primary m-0 p-0"><?php

                if (isset($_GET['numcmdpaye'])) {
                    $_SESSION['numcmdmodif']=$_GET['numcmdpaye'];
                }

                if (isset($_GET['addplat']) and $_GET['type']=='nos tacos' ) {
                    $_SESSION['type']=$_GET['type'];
                    $_SESSION['description']=$_GET['nom'];
                    header('Location: composition.php');
                }elseif (isset($_GET['addplat']) and $_GET['type']=='Nos Chichas' ) {
                    $_SESSION['type']=$_GET['type'];
                    $_SESSION['description']=$_GET['nom'];
                    header('Location: arome.php');
                }elseif (isset($_GET['addplat'])) {
                    $_SESSION['type']=$_GET['type'];
                    header('Location: supplement.php');
                }elseif (isset($_GET['addaccompagnement'])) {
                    header('Location: accompagnement.php');
                }elseif (isset($_GET['addsupplement'])) {
                    header('Location: supplement.php');
                }elseif (isset($_GET['addboisson'])) {
                    header('Location: boisson.php');
                }elseif (isset($_GET['addcafe'])) {
                    header('Location: cafe.php');
                }elseif (isset($_GET['adddessert'])) {
                    header('Location: dessert.php');
                }elseif (isset($_GET['addmenu'])) {
                    $_SESSION['payant']='gratuit';
                    header('Location: boissonm.php');
                }elseif (isset($_GET['addmenub']) and $_GET['type']=='frite et boisson') {
                    $_SESSION['payant']='gratuit';
                    header('Location: accompagnement.php');
                }else{
                    $_SESSION['payant']='payant';?>

                    <div class="container">

                        <div class="row"><?php

                            if (!isset($_GET['type']) AND !isset($_GET['menu'])) {
                                $_SESSION['typecmd']='simple';?>

                                <div  class="col fw-bold fs-5 p-1 mb-1 text-white text-center m-auto rounded" style="background-color: #2C1A6C">QUE VOULEZ-VOUS SVP ?</div>
                                
                                <div class="container-fluid">

                                    <div class="row"><?php                

                                        $products = $DB->query('SELECT * FROM stock WHERE genre="plat" and type!="accompagnements" ORDER BY(id) ');

                                        foreach ($products as $plat){?>

                                            <div class="col pb-2">
                                                <a style="text-decoration: none" href="modifventeprod.php?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&addplat&type">
                                                    <div class="card m-auto border-10 border-primary" style="width: 9rem; height: 180px;">

                                                        <div class="card-bod m-auto text-center fw-bold fs-7"><?= ucwords(strtolower($plat->nom)); ?>
                                                        </div>

                                                        <img src="css/img/plat/<?= $plat->id; ?>.jpg" class="card-img-top m-auto" alt=" " style="width: 6rem; height: 6rem">

                                                        <div class="card-bod m-auto">
                                                            <h5 class="card-title text-center text-danger"><?= number_format($plat->prix_vente,0,',',' '); ?></h5>
                                                        </div>

                                                    </div>
                                                </a>
                                            </div><?php
                                        } ?>

                                    </div>

                                    <div class="row bg-light p-1">
                                        <div class="col">
                                            <a class="btn btn-primary" href="accompagnement.php?menu=<?='';?>">Accompagnements</a>
                                        </div>


                                        <div class="col">
                                            <a class="btn btn-primary" href="supplement.php?menu=<?='';?>">Supplements</a>
                                        </div>

                                        <div class="col">
                                            <a class="btn btn-primary" href="boisson.php?menu=<?='';?>">Boissons</a>
                                        </div>
                                    
                                    </div>
                            

                                </div><?php

                            }elseif (isset($_GET['menu'])) {
                                $_SESSION['typecmd']='simple';?>

                                <div  class="col fw-bold fs-5 p-1 mb-1 text-white text-center m-auto rounded" style="background-color: #2C1A6C">QUE VOULEZ-VOUS COMME MENU SVP ?</div>

                                <div class="container">

                                    <div class="row"><?php

                                        $productsm = $DB->query('SELECT * FROM menucompose ');
                                        foreach ($productsm as $plat):?>

                                            <div class="col pb-2">
                                                <a style="text-decoration: none" href="modifventeprod.php?type=<?= $menu->type; ?>&aa">
                                                    <div class="card m-auto border-10 border-primary" style="width: 9rem; height: 180px;">

                                                        <div class="card-bod m-auto text-center fw-bold fs-7"><?= ucwords(strtolower($plat->nom)); ?>
                                                        </div>

                                                        <img src="css/img/plat/<?= $plat->id; ?>.jpg" class="card-img-top m-auto" alt=" " style="width: 6rem; height: 6rem">

                                                        <div class="card-bod m-auto">
                                                            <h5 class="card-title text-center text-danger"><?= number_format($plat->prix_vente,0,',',' '); ?></h5>
                                                        </div>

                                                    </div>
                                                </a>
                                            </div>                                                

                                        <?php endforeach; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-primary" href="accompagnement.php?menu=<?='';?>">Accompagnements</a>
                                        </div>


                                        <div class="col">
                                            <a class="btn btn-primary" href="supplement.php?menu=<?='';?>">Supplements</a>
                                        </div>

                                        <div class="col">
                                            <a class="btn btn-primary" href="boisson.php?menu=<?='';?>">Boissons</a>
                                        </div>
                                    
                                    </div>

                                </div><?php

                            }else{
                                $_SESSION['typecmd']='simple';
                                $_SESSION['typemenu']=$_GET['type'];?>

                                <div  class="col fw-bold fs-5 p-1 mb-1 text-white text-center m-auto rounded" style="background-color: #2C1A6C">QUE VOULEZ-VOUS COMME PLAT SVP ?</div>

                                <div class="container">

                                    <div class="row">

                                        <?php require 'plat.php';?>

                                    </div>

                                    <div class="row bg-info">
                                        <div class="col">
                                            <a class="btn btn-primary" href="accompagnement.php?menu=<?='';?>">Accompagnements</a>
                                        </div>


                                        <div class="col">
                                            <a class="btn btn-primary" href="supplement.php?menu=<?='';?>">Supplements</a>
                                        </div>

                                        <div class="col">
                                            <a class="btn btn-primary" href="boisson.php?menu=<?='';?>">Boissons</a>
                                        </div>
                                    
                                    </div>
                                </div><?php
                            }

                            if (isset($_GET['adddessert'])) {
                                $_SESSION['typecmd']='simple';
                            }?>
                        </div>

                    </div><?php
                }?>
            </div>
        

            <div class="col">

                <?php require 'modifventepanier.php'; ?>

            </div>
        </div>

    </div>

</div>

</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'recherche_utilisateur.php',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#result-search').append(data);
                        }else{
                          document.getElementById('result-search').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                        }
                    }
                })
            }
      
        });
    });
</script>

<script type="text/javascript">
  function alerteS(){
    return(confirm('Valider la suppression'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>
