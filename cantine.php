<?php
require 'headerv3.php';
if (isset($_SESSION['pseudo'])) {
    
    if (!empty($_SESSION['matricule'])) {
        $bdd='accesite'; 
        $DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `mat_acces` VARCHAR(50) NULL,
        `journee` VARCHAR(50) NULL,
        `date_acces` DATETIME,
        `datesaisie` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `promo` VARCHAR(50) DEFAULT '2024',
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");?>

        <div class="container-fluid">

            <div class="row"><?php 
                require 'navcantine.php';?>
                <div class="col-sm-12 col-md-10 " style="background-color: #fff4d7; height:100vh;"><?php

                    if (isset($_GET['retour'])) {
                        $_SESSION['positionpl']=array();
                        $_SESSION['positionem']=array();
                        unset($_SESSION['mange']);

                    }else{

                    }?>

                    <div class="container-fluid">

                        <div class="row my-4">
                            <div class="card m-auto" style="width: 8rem;">
                            <img src="css/img/restaurant/logoresto.jpg" class="card-img-top m-auto" alt="..." style="width: 8rem; height: 8rem">
                            </div>
                        </div>

                        <div class="row"><?php

                            if (date('H')>16) {?>

                                <div class="text-success fs-2 fw-bold text-center">BONSOIR</div><?php

                            }else{?>

                                <div class=" text-success fs-2 fw-bold text-center">BONJOUR</div><?php
                            }?>

                            <div class="fs-3 fw-bold text-danger text-center my-4">OÙ VOULEZ-VOUS MANGER?</div>

                        </div>
                        
                        <div class="row bg-danger py-4 m-auto">

                            <div class="col">
                                <a class="btn btn-light" href="accueil.php?surplace">
                                    <div class="card m-auto" style="width: 8rem;">
                                        <img src="css/img/restaurant/surplace.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                                        <div class="card-bod m-auto">
                                        <h5 class="card-title">SURPLACE</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col">
                                <a class="btn btn-light" href="accueil.php?emporter">
                                    <div class="card m-auto" style="width: 8rem;">
                                    <img src="css/img/restaurant/emporter.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                                    <div class="card-bod m-auto">
                                        <h5 class="card-title">À EMPORTER</h5>
                                    </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col">
                                <a class="btn btn-light" href="accueil.php?livrer">
                                    <div class="card m-auto" style="width: 8rem;">
                                    <img src="css/img/restaurant/livraison.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                                    <div class="card-bod m-auto">
                                        <h5 class="card-title">À LIVRER</h5>
                                    </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col">
                                <a class="btn btn-light" href="tablecommande.php?surplace">
                                    <div class="card m-auto" style="width: 8rem;">
                                    <img src="css/img/restaurant/commandes.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                                    <div class="card-bod m-auto">
                                        <h5 class="card-title">COMMANDES</h5>
                                    </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div> <?php
    }
	
}?>

<?php require 'footer.php';?>

<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function focus(){
        document.getElementById('cursor').focus();
    }

</script>



<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'searcheleve.php?elevesearch',
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
