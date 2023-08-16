<?php

if (isset($_GET['delpc'])) {

	$DB->delete('DELETE FROM validcomande WHERE id = ?', array($_GET['delpc']));
}

if (isset($_GET['desig'])) {

	$prodvalidcverif = $DB->querys("SELECT quantite FROM validcomande where id_produit='{$_GET['idc']}' and pseudo='{$_SESSION['idpseudo']}' ");

	if (empty($prodvalidcverif)) {
				
		$DB->insert('INSERT INTO validcomande (id_produit, designation, quantite, pachat, pvente, previent, frais, etat, pseudo, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($_GET['idc'], $_GET['desig'], 1, $_GET['pa'], $_GET['pv'], 0, 0, 'paye', $_SESSION['idpseudo']));

	}else{

		$qtitesup=$prodvalidcverif['quantite']+1;

		$DB->insert('UPDATE validcomande SET quantite=? where id_produit=? and pseudo=?', array($qtitesup, $_GET['idc'], $_SESSION['idpseudo']));

	}
}

	

if (isset($_POST['modifcom']) or isset($_GET['modifcom'])) {
	
	$DB->insert('UPDATE validcomande SET quantite=?, pachat=?, frais=?, pvente=? where id=? and pseudo=?', array($_POST['quantity'], $_POST['pachat'], $_POST['frais'], $_POST['pvente'], $_POST['id'], $_SESSION['idpseudo']));
}

$prodvalidc = $DB->query('SELECT validcomande.id as id, id_produit, validcomande.quantite as quantite, validcomande.designation as designation, pvente, pachat, prix_achat, prix_vente, frais FROM validcomande inner join stock on stock.id=validcomande.id_produit order by(validcomande.id)desc');

if (!empty($prodvalidc)) {?>

	<div class="col" style="overflow: auto;">
	 	
		<table class="table table-hover table-bordered table-striped table-responsive">

	 		<thead>

		 		<tr>			
					<th>Désignation</th>
					<th>Qtite</th>				
					<th>P. Achat</th>
					<th>Frais</th>
					<th>P. Vente</th>
					<th>P. Total</th>	
					<th></th>			
					<th>Sup</th>
				</tr>

			</thead>

			<?php

			$ptotalht=0;
			$totfrais=0;

			foreach($prodvalidc as $key=> $product){

				$ptotal=$product->quantite*$product->pachat;
				$pfrais=$product->frais;

				$ptotalht+=$ptotal;
				$totfrais+=$product->frais*$product->quantite;?>

				<form id="modifcom" action="commande.php?modifcom" method="POST">

					<tbody>

						<td><?= ucfirst(strtolower($product->designation)); ?><input  type="hidden" name="id" value="<?=$product->id;?>"></td>

						<td><input class="form-control text-center" type="text" min="0" name="quantity" value="<?=$product->quantite;?>"></td><?php

						if ($product->pachat==0) {?>

							<td><input class="form-control" type="text" min="0" name="pachat" value="<?=$product->prix_achat;?>"></td><?php

						}else{?>

							<td><input class="form-control" type="text" min="0" name="pachat" value="<?=$product->pachat;?>"></td><?php
						}?>

						<td><input class="form-control" type="text" name="frais" required="" value="<?=$product->frais;?>"></td><?php

						if ($product->pvente==0) {?>

							<td><input class="form-control" type="text" min="0" name="pvente" value="<?=$product->prix_vente;?>"></td><?php

						}else{?>

							<td><input class="form-control" type="text" min="0" name="pvente" value="<?=$product->pvente;?>"></td><?php
						}?>

						<td style="text-align:right;"><?=number_format($ptotal,0,',',' ');?></td>

						<td><input class="btn btn-success" type="submit" name="modifcom" value="Valider" style="background-color: orange; color: white;"></td>					

						<td class="supc">
							<a onclick="return alerteV();" href="commande.php?delpc=<?= $product->id; ?>" class="del"><img src="css/img/sup.jpg" width="30" height="25"></a>
						</td>

					</tbody>
				</form><?php
			}?>

			
		</table><?php 

		$ttcgnf=$ptotalht*1;?>


		<table class="table table-hover table-bordered table-striped table-responsive text-center bg-info">

			<thead>

		      	<tr>
		      		<th height="35" >FRAIS TOTAUX</th>
		      		<th><?=number_format($totfrais,0,',',' '); ?></th>

		       		<th>TTC</th> 

		       		<th><?=number_format($ptotalht,0,',',' '); ?></th>

		       		<th height="35">TTC GNF</th> 

		       		<th><?=number_format($ttcgnf,0,',',' '); ?></th>
		      	</tr>

		    </thead>

		</table>
	</div>
	</div>




	<form action="commande.php" method="POST">
		<div class="col" style="overflow: auto">

			<table class="table table-hover table-bordered table-striped table-responsive text-center bg-info">

			    <thead>

			      <tr>
			      	<th height="25">N° Facture</th>
			      	<th>Date Fact</th>
			        <th>Montant à Payer</th>
			        <th>Montant Payé</th>
			        <th>Frais</th>                    
			        <th>Paiement</th>
			        <th>Compte Retrait</th>               
			        <th>Fournisseurs</th>
			      </tr>

			    </thead>
	                        
	    		<tbody>
	    	

			    	<td><input class="form-control" type="text" min="0"  name="numfact" required=""></td>

			    	<td><input class="form-control" type="date"  name="datefact" required=""></td> 

			      	<td><input class="form-control text-center" type="text" min="0"  name="prix_reel" value="<?=$ttcgnf; ?>"></td>

			      	<td><input class="form-control text-center" type="text" min="0"  name="montantc" required=""></td>

			      	<td><input class="form-control text-center" type="text" min="0"  name="frais" value="<?=$totfrais;?>"></td> 

			        <td><select class="form-select" name="mode_payement" required="" >
			            <option value=""></option><?php 
			            foreach ($panier->modep as $value) {?>
			                <option value="<?=$value;?>"><?=$value;?></option><?php 
			            }?></select>
			        </td>

		            <td><select  class="form-select" name="compte" required="">
		                <option></option><?php
		                    $type='Banque';

		                    foreach($panier->nomBanque() as $product){?>

		                        <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
		                    }?>
		                </select>               
		            </td> 

		            <td><select class="form-select" type="text" name="client" required="">

		              <option></option><?php

		              $type1='Fournisseur';
						$type2='Clientf';


						foreach($panier->clientF($type1, $type2) as $product){?>

		                	<option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php

		                }?></select>
		        	</td>

	        	</tbody>

			</table><?php if ($_SESSION['level']>6) {?>

			<input class="btn btn-primary" type="submit" name="payer" value="Valider" onclick="return alerteV();"><?php }?>
		</div>

	</form><?php 
}?>







