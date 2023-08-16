<?php 

if (isset($_GET['surplace'])) {

	$_SESSION['mange']='SURPLACE';

}elseif (isset($_GET['emporter'])) {

	$_SESSION['mange']='EMPORTER';

}elseif (isset($_GET['livrer'])) {

	$_SESSION['mange']='LIVRAISON';

}

if (empty($_SESSION['mange'])) {
	header("Location: choix.php?retour=<?='retour';?>");
}else{

	if (isset($_GET['clientvip'])) {
		$_SESSION['clientvip']=$_GET['clientvip'];
	}

	if (isset($_POST['clientvip'])) {
		$_SESSION['clientvip']=$_POST['clientvip'];
	}

	if (isset($_POST['banque'])) {
		$_SESSION['banque']=$_POST['banque'];
		unset($_SESSION['alertesvirement']);
	}

	if (isset($_GET['delPanier'])) {

		$DB->delete('DELETE FROM validpaie WHERE id = ? and pseudov=?', array($_GET['delPanier'], $_SESSION['idpseudo']));

		$DB->delete('DELETE FROM validvente where pseudop=?', array($_SESSION['idpseudo']));
	}


	if (isset($_SESSION['error']) AND $_SESSION['error']!= array()) {?>
		<div class="alertes"><?php echo $_SESSION['error']; ?></div>
		<?php
	}else{

	}

	if (isset($_GET['nom'])) {

		//$prodvalidcverif = $DB->querys('SELECT quantite FROM validpaie where id_produit=? and pseudov=?', array($_GET['idc'], $_SESSION['idpseudo']));

		

		if ($_SESSION['typecmd']=='menu' and isset($_GET['suplement'])) {
			$DB->insert('INSERT INTO validpaie (id_produit, codebvc, quantite, pvente, type, liaison, pseudov, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($_GET['idc'], 1, 1, 0, $_SESSION['typecmd'], 0, $_SESSION['idpseudo']));
		}else{
				
			$DB->insert('INSERT INTO validpaie (id_produit, codebvc, quantite, pvente, type, liaison, pseudov, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, now())', array($_GET['idc'], 1, 1, $_GET['pv'], $_SESSION['typecmd'], 0, $_SESSION['idpseudo']));
		}

		
	}

	if (isset($_POST['pvente']) or isset($_POST['quantity'])) {
		$pvente=$panier->espace($_POST['pvente']);
		
		$DB->insert('UPDATE validpaie SET quantite=?, pvente=? where id_produit=? and pseudov=?', array($_POST['quantity'], $pvente, $_POST['id'], $_SESSION['idpseudo']));
	}

	if (isset($_POST['off'])) {
		$pvente=$panier->espace($_POST['pvente']);
		
		$DB->insert('UPDATE validpaie SET pvente=? where id_produit=? and pseudov=?', array(0, $_POST['id'], $_SESSION['idpseudo']));
	}

	if (isset($_GET['nom']) or isset($_POST['quantity']) or isset($_GET['delPanier'])) {		

		$proddirect = $DB->query('SELECT *FROM validpaie where pseudov=?', array($_SESSION['idpseudo']));
		$totcomd=0;
		foreach ($proddirect as $valued) {
			$totcomd+=$valued->quantite*$valued->pvente;
		}

		$prodverifv=$DB->querys('SELECT id from validvente where pseudop=?', array($_SESSION['idpseudo']));

		if (empty($prodverifv)) {

			$DB->insert('INSERT INTO validvente (remise, montantpgnf, Fraisup, pseudop) VALUES(?, ?, ?, ?)', array(0, $totcomd, 0, $_SESSION['idpseudo']));
		}else{
		
			$DB->insert('UPDATE validvente SET montantpgnf=? where pseudop=?', array($totcomd, $_SESSION['idpseudo']));
		}

		$DB->insert('UPDATE validvente SET remise=? where pseudop=?', array(0, $_SESSION['idpseudo']));
	}

	

	if (isset($_POST['remise']) or isset($_POST['remisep']) or isset($_POST['gnfpaye'])) {

		if (!empty($_POST['remisep'])) {
			$remise=$panier->h($panier->espace(($panier->totalcom()*($_POST['remisep']/100))));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye'])*(1-($_POST['remisep']/100)));
		}elseif(!empty($_POST['remise'])) {
			$remise=$panier->h($panier->espace($_POST['remise']));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye']));
		}else{

			$remise=$panier->h($panier->espace($_POST['remisep']));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye']));

		}
		
		$fraisup=$panier->h($panier->espace($_POST['fraisup']));		

		$prodverifv=$DB->querys('SELECT id from validvente where pseudop=?', array($_SESSION['idpseudo']));

		if (empty($prodverifv)) {

			$DB->insert('INSERT INTO validvente (remise, montantpgnf, fraisup, pseudop) VALUES(?, ?, ?, ?)', array($remise, $montantgnf, $fraisup, $_SESSION['idpseudo']));
		}else{
		
			$DB->insert('UPDATE validvente SET remise=?, montantpgnf=?, fraisup=? where pseudop=?', array($remise, $montantgnf, $fraisup, $_SESSION['idpseudo']));
		}

		
	}

	$total=$panier->total();

	$totalpaye=$panier->totalpaye();

	$totalp=$panier->totalcom();

	$products = $DB->query("SELECT stock.id as id, validpaie.id as idv, id_produit, validpaie.quantite as quantite, stock.nom as nom, pvente, pvente as prix_vente, stock.type as type FROM validpaie inner join stock on stock.id=validpaie.id_produit  where pseudov='{$_SESSION['idpseudo']}' order by(validpaie.id)");

	$prodvente = $DB->querys('SELECT * FROM validvente where pseudop=?', array($_SESSION['idpseudo']));?>

	<table class="payement" style="margin-top:0px; margin: 0px;">
		<thead>
			<tr><th colspan="5">COMMANDE <?= $_SESSION['mange'] ;?></th></tr>
			<tr>
				<th>Qtite</th>
				<th>Désignation</th>
				<th>Total</th>
				<th>Offert</th>
				<th></th>
			</tr>

		</thead><?php

		$totachat=0; 

		foreach($products as $product){

			$totachat+=$product->prix_vente*$product->quantite;?>
			

			<form id="quantity" action="accueil.php" method="POST">

				<tbody>


					<td><input style="font-size:25px; width: 80%;" type="text" min="0" name="quantity" value="<?=$product->quantite;?>" onchange="this.form.submit()"><input type="hidden" name="id" value="<?=$product->id;?>"></td>

					<td class="name"><?=ucfirst(strtolower($product->nom)); ?></td>

					<td class="price" style="text-align: right; padding-right: 10px;"><?= number_format($product->prix_vente*$product->quantite,0,',',' '); ?><input type="hidden" name="pvente" value="<?=$product->pvente;?>"></td>

					<td><input type="radio" name="off" value="<?=$product->id;?>" onchange="this.form.submit()" ></td>

					<td class="action"><a href="accueil.php?delPanier=<?= $product->idv; ?>" class="del"><img src="css/img/sup.jpg" width="25"></a></td>

				</tbody>
			</form><?php 

		}?>
	</table>

	<div class="rowtotal"><?php 
		if ($prodvente['remise']) {?>
			Remise: <span class="total"><?= number_format(($prodvente['remise']),0,',',' '); ?></span><?php 
		}?>
		Total: <span class="total"><?= number_format(($totalp),0,',',' '); ?></span>
	</div><?php 

	$montantpgnf=$totalp+$total; ?>

	<table style="margin-top: 10px;" class="payement">
		<thead>
			<tr>
				<th>Remise</th>
				<th>Montant Payé</th>
				<th>Fraisup</th>         
			</tr>
		</thead>
		<tbody>
			<form id='remise' method="POST" action="accueil.php">
				<tr>
					<td><select style="text-align: center;" name="remisep" onchange="this.form.submit()"><?php 
						if (empty($panier->totalcom())) {?>

							<option value="<?=($prodvente['remise']);?>"><?=$prodvente['remise'];?>%</option><?php
							
						}else{?>

							<option value="<?=($prodvente['remise']/$panier->totalcom())*100;?>"><?=($prodvente['remise']/$panier->totalcom())*100;?>%</option><?php 
						}?>
						<option value="0">0</option><?php 
						$r=1;
						while ($r< 100) {?>
							<option value="<?=$r;?>"><?=$r;?>%</option><?php

							//$r=$r+4;

							$r++;
						}?>
					</select></td>
					

					<td ><input style="font-size: 25px; font-weight: bold; width: 92%;" type="text" min="0" onchange="this.form.submit()" name="gnfpaye" value="<?=number_format($prodvente['montantpgnf'],0,',',' ');?>"></td>

					<td><input style="height: 25px; width: 90%; font-size: 25px;"  type="text" min="0" onchange="this.form.submit()" name="fraisup" value="<?=number_format($prodvente['fraisup'],0,',',' ');?>"></td>					

					
				</tr>
			</form>
		</tbody>

		<thead>
			<tr>
				<th>Mode P</th>
				<th>Client</th><?php
				if (!empty($prodvente['montantpgnf'])){
					if ($prodvente['montantpgnf'] < $totalp) {?>

						<th style="background-color: maroon; font-size: 18px;">Reste à Payer</th><?php

					}else{?>

						<th style="background-color: green; font-size: 18px;">Rendu Client</th><?php
					}
				}else{?>

					<th style=" background-color: maroon; font-size: 18px;">Total à Payer</th><?php
				}?>
			</tr>
			
		</thead>

		<tbody>
			<form id='payement' method="POST" action="payement.php" target="_blank">
				<tr>
					<input type="hidden" name="totachat" value="<?=$totachat;?>">
					<input type="hidden" name="reste" value="<?=$total; ?>">												

					<td><?php 
						if ($_SESSION['mange']=='LIVRAISON') {?>
							Plus-tard<input type="hidden" name="mode_payement" value="différé"><?php 
						}else{?>
							<select name="mode_payement" required="" style="height: 25px; width: 95%;" ><?php

			                foreach ($panier->modep as $value) {?>
			                    <option value="<?=$value;?>"><?=$value;?></option><?php 
			                }?></select><?php 
			            }?>
		            </td>

					<td style="width:40%;">
						<input style="width:35%;" id="search-user" type="text" placeholder="rechercher un client" /><?php 
						if ($_SESSION['mange']=='LIVRAISON') {?>											
							<select style="height: 25px; width:55%;" type="text" required="" name="clientvip"><?php 
						}else{?>											
							<select style="height: 25px; width:55%;" type="text" name="clientvip"><?php 
						}

							if (!empty($_SESSION['clientvip'])) {?>

								<option value="<?=$_SESSION['clientvip'];?>"><?=$panier->nomClient($_SESSION['clientvip']);?></option><?php 
							}else{

								if ($_SESSION['mange']=='LIVRAISON') {?>

									<option></option><?php
								}else{?>

									<option value="1">Client cash</option><?php

								}
							}

							$type1='client';
							$type2='clientf';
							
							foreach($panier->clientF($type1, $type2) as $product){?>

								<option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
							}?>
						</select>

						<div style="color:white; background-color: white; font-size: 16px;" id="result-search"></div>
					</td>

					<td style="text-align: center; font-size: 25px; width:20%;"><?= number_format(($total+$prodvente['fraisup']),0,',',' '); ?></td>	
					
				</tr>

				<tr>
					<td style="width:20%;">
						<select  name="compte" required="" ><?php
		                    $type='Banque';

		                    foreach($panier->nomBanqueCaisse() as $product){?>

		                        <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
		                    }?>
		                </select>
		            </td>

		            <td><input type="text" name="coment" placeholder="laissez un commentaire !!!"></td>

		            <td><input type="date" name="datev" value=""></td>
		        </tr>

		        <tr>

		            <td><input id="button" type="submit" name="payer" value="Valider"></td>
		        </tr>
			</form>
		</tbody>
	</table><?php 
}

