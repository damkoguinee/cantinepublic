<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
<head>
	<title>Restaurant</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="css/comptabilite.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="css/client.css" type="text/css" media="screen" charset="utf-8">
</head>
<body>
	<div class="principal"><?php 
		if (!empty($_SESSION['numcmdmodif'])) {
			require 'navmodif.php';
			$positionmodif="modifventeprod.php";
		}else{
			require 'nav.php';
			$positionmodif="accueil.php";
		}?>
		<div class="choix">
			
			<?php 
			if (!isset($_GET['id'])) {?>
				<div class="bloc">
					<div>
						<div class="proposition">QUE VOULEZ-VOUS COMME DESSERT ?</div>
						<div class="affiche"><?php
							$products = $DB->query('SELECT * FROM stock WHERE type="desserts" ');
							foreach ($products as $plat){?>

								<div class="affiche_menu"><a href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&adddessert&suplement">
									<div class="designation"><?= ucwords(strtolower($plat->nom)); ?></div>

									<div class="picture"><img src="css/img/plat/<?= $plat->id; ?>.jpg" alt=" "></div>

									<div class="pricebox"><?= number_format($plat->prix_vente,0,',',' '); ?></div></a>

								</div><?php
							}?>
						</div>
					</div>


					<div style="display: flex; width: 100%;">	

						<div style="width: 50%;">
							<a href="cafe.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Cafés</div></a>
						</div>

						<div style="width: 50%;">
							<a href="supplement.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Supplements</div></a>
						</div>
					</div>
				</div><?php
			}else{
				require 'plat.php';
				
			}?>
		</div>
		<div class="panier">
			<div>
				<?php 
				if (!empty($_SESSION['numcmdmodif'])) {
					require 'modifventepanier.php';
				}else{
					require 'panier.php';
				}?>		
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
