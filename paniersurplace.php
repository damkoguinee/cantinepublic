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

	$bdd='tablecommande'; 

	$DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `id_produit` int(50) DEFAULT NULL,
	  `idtable` int(2) DEFAULT NULL,
	  `codebvc` varchar(50) DEFAULT NULL,
	  `quantite` float NOT NULL,
	  `pvente` double DEFAULT NULL,
	  `type` varchar(15) CHARACTER SET utf8 DEFAULT 'simple',
	  `liaison` int(11) DEFAULT '0',
	  `pseudov` varchar(50) DEFAULT NULL,
	  `datecmd` datetime DEFAULT NULL,
	  PRIMARY KEY (`id`)
	)");


	$bdd='tablevalide'; 

	$DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `idtable` int(11) NOT NULL,
	  `remise` double DEFAULT '0',
	  `montantpgnf` double DEFAULT '0',
	  `fraisup` double DEFAULT '0',
	  `pseudop` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ");

	if (isset($_GET['delPanier'])) {

		$DB->delete('DELETE FROM tablecommande WHERE id = ? and idtable=? and pseudov=?', array($_GET['delPanier'], $_SESSION['tableresto'], $_SESSION['idpseudo']));
	}

	$prodverifdispo = $DB->querys("SELECT quantite FROM tablecommande where idtable='{$_SESSION['tableresto']}'");

	if (empty($prodverifdispo['quantite'])) {
		
		$DB->insert('UPDATE tableresto SET dispo=? where id=? ', array(1, $_SESSION['tableresto']));
	}


	if (isset($_SESSION['error']) AND $_SESSION['error']!= array()) {?>
		<div class="alertes"><?php echo $_SESSION['error']; ?></div>
		<?php
	}else{

	}

	if (isset($_GET['nom'])) {

		$idc=$panier->h($_GET['idc']);		

		if ($_SESSION['typecmd']=='menu' and isset($_GET['suplement'])) {
			$DB->insert('INSERT INTO tablecommande (id_produit, idtable, codebvc, quantite, pvente, type, liaison, pseudov, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, now())', array($idc, $_SESSION['tableresto'], 1, 1, 0, $_SESSION['typecmd'], 0, $_SESSION['idpseudo']));
		}else{

			$prodvalidcverif = $DB->querys('SELECT quantite FROM tablecommande where idtable=? and id_produit=? and pseudov=?', array($_SESSION['tableresto'], $idc, $_SESSION['idpseudo']));

			if (empty($prodvalidcverif)) {
						
				$DB->insert('INSERT INTO tablecommande (id_produit, idtable, codebvc, quantite, pvente, type, liaison, pseudov, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, now())', array($idc, $_SESSION['tableresto'], 1, 1, $_GET['pv'], $_SESSION['typecmd'], 0, $_SESSION['idpseudo']));

				$DB->insert('UPDATE tableresto SET dispo=? where id=? ', array(0, $_SESSION['tableresto']));

			}else{

				$qtitesup=$prodvalidcverif['quantite']+1;

				$DB->insert('UPDATE tablecommande SET quantite=? where idtable=? and id_produit=? and pseudov=?', array($qtitesup, $_SESSION['tableresto'], $idc, $_SESSION['idpseudo']));

				$DB->insert('UPDATE tableresto SET dispo=? where id=? ', array(0, $_SESSION['tableresto']));

			}
				
			
		}

		
	}

	

	if (isset($_POST['pvente']) or isset($_POST['quantity'])) {
		$pvente=$panier->espace($_POST['pvente']);
		
		$DB->insert('UPDATE tablecommande SET quantite=?, pvente=? where id_produit=? and idtable=? and pseudov=?', array($_POST['quantity'], $pvente, $_POST['id'], $_SESSION['tableresto'], $_SESSION['idpseudo']));
	}

	if (isset($_POST['off'])) {
		$pvente=$panier->espace($_POST['pvente']);
		
		$DB->insert('UPDATE tablecommande SET pvente=? where id_produit=? and idtable=? and pseudov=?', array(0, $_POST['id'], $_SESSION['tableresto'], $_SESSION['idpseudo']));
	}

	if (isset($_GET['nom']) or isset($_POST['quantity']) or isset($_GET['delPanier'])) {		

		$proddirect = $DB->query('SELECT *FROM tablecommande where idtable=? and pseudov=?', array($_SESSION['tableresto'], $_SESSION['idpseudo']));
		$totcomd=0;
		foreach ($proddirect as $valued) {
			$totcomd+=$valued->quantite*$valued->pvente;
		}

		$prodverifv=$DB->querys("SELECT id from tablevalide where idtable='{$_SESSION['tableresto']}' and pseudop='{$_SESSION['idpseudo']}'");

		if (empty($prodverifv)) {

			$DB->insert('INSERT INTO tablevalide (idtable, remise, montantpgnf, Fraisup, pseudop) VALUES(?, ?, ?, ?, ?)', array($_SESSION['tableresto'], 0, $totcomd, 0, $_SESSION['idpseudo']));
		}else{
		
			$DB->insert('UPDATE tablevalide SET montantpgnf=? where idtable=? and pseudop=?', array($totcomd, $_SESSION['tableresto'], $_SESSION['idpseudo']));
		}

		$DB->insert('UPDATE tablevalide SET remise=? where idtable=? and pseudop=?', array(0, $_SESSION['tableresto'], $_SESSION['idpseudo']));
	}

	if (isset($_POST['remise']) or isset($_POST['remisep']) or isset($_POST['gnfpaye'])) {
		if (!empty($_POST['remisep'])) {
			$remise=$panier->h($panier->espace(($panier->totalcomTable()*($_POST['remisep']/100))));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye'])*(1-($_POST['remisep']/100)));
		}elseif(!empty($_POST['remise'])) {
			$remise=$panier->h($panier->espace($_POST['remise']));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye']));
		}else{

			$remise=$panier->h($panier->espace($_POST['remisep']));
			$montantgnf=$panier->h($panier->espace($_POST['gnfpaye']));

		}
		$fraisup=$panier->h($panier->espace($_POST['fraisup']));
		

		$prodverifv=$DB->querys("SELECT id from tablevalide where idtable='{$_SESSION['tableresto']}' and pseudop='{$_SESSION['idpseudo']}'");

		if (empty($prodverifv)) {

			$DB->insert('INSERT INTO tablevalide (remise, montantpgnf, fraisup, idtable, pseudop) VALUES(?, ?, ?, ?, ?)', array($remise, $montantgnf, $fraisup, $_SESSION['tableresto'], $_SESSION['idpseudo']));
		}else{
		
			$DB->insert('UPDATE tablevalide SET remise=?, montantpgnf=?, fraisup=? where pseudop=? and idtable=?', array($remise, $montantgnf, $fraisup, $_SESSION['idpseudo'], $_SESSION['tableresto']));
		}

		
	}

	$total=$panier->totaltable();

	$totalpaye=$panier->totalpayetable();

	$totalp=$panier->totalcomTable();

	$products = $DB->query("SELECT stock.id as id, tablecommande.id as idv, id_produit, tablecommande.quantite as quantite, stock.nom as nom, pvente, pvente as prix_vente, stock.type as type FROM tablecommande inner join stock on stock.id=tablecommande.id_produit  where idtable='{$_SESSION['tableresto']}' and pseudov='{$_SESSION['idpseudo']}' order by(tablecommande.id)");

	$prodvente = $DB->querys('SELECT * FROM tablevalide where idtable=? and pseudop=?', array($_SESSION['tableresto'], $_SESSION['idpseudo']));?>

	<table class="payement" style="margin-top:0px;">
		<thead>
			<tr><th colspan="5" style="font-size: 20px; color: blue;">COMMANDE <label><?= $panier->nomTable($_SESSION['tableresto'])[0] ;?></label></th></tr>
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
					<td><input style="font-size:25px; width: 85%;" type="text" min="0" name="quantity" value="<?=$product->quantite;?>" onchange="this.form.submit()"><input type="hidden" name="id" value="<?=$product->id;?>"></td>

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
	</div>

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
						if (empty($panier->totalcomTable())) {?>

							<option value="<?=($prodvente['remise']);?>"><?=$prodvente['remise'];?>%</option><?php
							
						}else{?>

							<option value="<?=($prodvente['remise']/$panier->totalcomTable())*100;?>"><?=($prodvente['remise']/$panier->totalcomTable())*100;?>%</option><?php 
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
			<form id='payement' method="POST" action="tablepayement.php" target="_blank">
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

