<?php
require 'header3.php';

if (!empty($_SESSION['pseudo'])) {
    
    if ($_SESSION['level']<4) {?>

        <div class="alertes">Des autorisations sont requises pour consulter cette page</div><?php

    }else{?>

    	<div class="container-fluid mt-3"><?php 

			if (isset($_GET['ajout_en'])) {?>

				<div class="col">
				
				    <form id="naissance" method="POST" action="personnel.php">

				    	<fieldset><legend>Ajouter un Personnel</legend>

				    		<div class="row">

				    			<div class="col-sm-12 col-md-6">

					    			<div class="mb-1">
					    				<label class="form-label">Type personnel</label>
					    				<select class="form-select" type="text" name="perso" required="">
											<option></option>
											<option value="Responsable">Responsable</option>
											<option value="superviseur">Vendeur</option>
											<option value="vendeur">Vendeur</option>
											<option value="caissier">Caissier</option>
										</select>
									</div>

									<div class="mb-1">
										<label class="form-label">Nom</label>
										<input class="form-control" type="text" name="nom" required="">
									</div>

									<div class="mb-1">
								    	<label class="form-label">Téléphone</label>
								    	<input class="form-control" type="number" name="tel" >
								    </div>

								    <div class="mb-1"><label class="form-label">Pseudo</label>
								    	<input class="form-control" type="text" name="pseudo" >
								    </div>

								    <div class="mb-1">
								    	<label class="form-label">Mot de Passe</label>
								    	<input class="form-control" type="text" name="mdp" >
								    </div>

								    <div class="mb-1"><label>Niveau</label>
										<select class="form-select" type="number" name="niv" required="">
											<option value="1">Niveau 1</option>
											<option value="2">Niveau 2</option>
											<option value="3">Niveau 3</option>
											<option value="4">Niveau 4</option>
											<option value="5">Niveau 5</option>
											<option value="6">Niveau 6</option>
											<option value="7">Niveau 7</option>
										</select>
									</div>
								</div>
							</div>

						</fieldset>
						<input class="btn btn-light" type="reset" value="Annuler" name="annuldec" /><input  class="btn btn-primary" type="submit" value="Valider" name="ajouteen" onclick="return alerteV();"/></fieldset>
					</form>
				</div><?php
			}

			if(isset($_POST['ajouteen'])){

				if($_POST['nom']!=""  and $_POST['perso']!=""){
					
					$nom=addslashes(Htmlspecialchars($_POST['nom']));
					$phone=addslashes(Htmlspecialchars($_POST['tel']));
					$pseudo=addslashes(Nl2br(Htmlspecialchars($_POST['pseudo'])));
					$mdp=addslashes(Nl2br(Htmlspecialchars($_POST['mdp'])));
					$type=addslashes(Nl2br(Htmlspecialchars($_POST['perso'])));
					$niveau=addslashes(Nl2br(Htmlspecialchars($_POST['niv'])));	

					$mdp=password_hash($mdp, PASSWORD_DEFAULT);		

					$nb=$DB->querys('SELECT id from personnel where nom=:nom', array(
						'nom'=>$nom
						));

						if(!empty($nb)){?>
							<div class="alert alert-warning">Ce Personnel existe</div><?php
						}else{

							$nb=$DB->querys('SELECT max(id) as id from personnel');


							$matricule=$nb['id']+1;

							$DB->insert('INSERT INTO personnel(identifiant, nom, telephone, agence, pseudo, mdp, level, statut, dateenreg) values(?, ?, ?, ?, ?, ?, ?, ?, now())', array($matricule, $nom, $phone, 'BoutiqueB3', $pseudo, $mdp, $niveau, $type));

							?>	

							<div class="alert alert-success">Personnel ajouté avec succée!!!</div><?php
						}

					

					}else{?>	

						<div class="alert alert-warning">Remplissez les champs vides</div><?php
					}
				}


				//Modifier un enseignant

				if (isset($_GET['modif_en'])) {?>

					<div class="col-sm-12 col-md-12">
					
					    <form id="naissance" method="POST" action="personnel.php" style="width: 60%;">

					    	<fieldset><legend>Modifier un personnel</legend><?php

								$prodm=$DB->querys('SELECT * from personnel  where id=:mat', array('mat'=>$_GET['modif_en']));?>

								<div class="row">

					    			<div class="col">

						    			<div class="mb-1">
						    				<label class="form-label">Type personnel</label>
						    				<select class="form-select" type="text" name="perso" required="">
												<option value="<?=$prodm['statut'];?>"><?=$prodm['statut'];?></option>
												<option value="Responsable">Responsable</option>
												<option value="superviseur">Vendeur</option>
												<option value="vendeur">Vendeur</option>
												<option value="caissier">Caissier</option>
											</select>
											<input type="hidden" name="id" value="<?=$_GET['modif_en'];?>">
										</div>

										<div class="mb-1">
											<label class="form-label">Nom</label>
											<input class="form-control" type="text" name="nom" required="" value="<?=$prodm['nom'];?>">
										</div>

										<div class="mb-1">
									    	<label class="form-label">Téléphone</label>
									    	<input class="form-control" type="number" name="tel" value="<?=$prodm['telephone'];?>">
									    </div>

									    <div class="mb-1"><label class="form-label">Pseudo</label>
									    	<input class="form-control" type="text" name="pseudo" value="<?=$prodm['pseudo'];?>" >
									    </div>

									    <div class="mb-1">
									    	<label class="form-label">Mot de Passe</label>
									    	<input class="form-control" type="text" name="mdp" required="">
									    </div>

									    <div class="mb-1"><label>Niveau</label>
											<select class="form-select" type="number" name="niv" required="">
												<option value="<?=$prodm['level'];?>"><?=$prodm['level'];?></option>
												<option value="1">Niveau 1</option>
												<option value="2">Niveau 2</option>
												<option value="3">Niveau 3</option>
												<option value="4">Niveau 4</option>
												<option value="5">Niveau 5</option>
												<option value="6">Niveau 6</option>
												<option value="7">Niveau 7</option>
											</select>
										</div>
									</div>
								</div>
							</fieldset>

							<input class="btn btn-light" type="reset" value="Annuler" name="annuldec" /><input class="btn btn-primary" type="submit" value="Modifier" name="modifen" onclick="return alerteV();" />
						</form>
					</div><?php
				}

				if(isset($_POST['modifen'])){
						
					$nom=addslashes(Htmlspecialchars($_POST['nom']));
					$phone=addslashes(Htmlspecialchars($_POST['tel']));
					$pseudo=addslashes(Nl2br(Htmlspecialchars($_POST['pseudo'])));
					$mdp=addslashes(Nl2br(Htmlspecialchars($_POST['mdp'])));
					$type=addslashes(Nl2br(Htmlspecialchars($_POST['perso'])));
					$niveau=addslashes(Nl2br(Htmlspecialchars($_POST['niv'])));

					$mdp=password_hash($mdp, PASSWORD_DEFAULT);

					

					$DB->insert('UPDATE personnel SET nom = ?, telephone=?, pseudo=?, mdp=?, level=?, statut=?  WHERE id = ?', array($nom, $phone, $pseudo, $mdp, $niveau, $type, $_POST['id']));?>	

					<div class="alert alert-success"> Modification effectuée avec succée!!!</div><?php
					
				}

				// fin modification

			    if (isset($_GET['enseig']) or isset($_POST['ajouteen'])  or isset($_GET['del_en']) or isset($_GET['del_pers']) or isset($_POST['modifen']) or isset($_GET['matiereen']) or isset($_GET['personnel']) or isset($_GET['payempcherc'])) {

			    	if (isset($_GET['del_pers'])) {

			          $DB->delete('DELETE FROM personnel WHERE id = ?', array($_GET['del_pers']));?>

			          <div class="alert alert-success">Suppression reussie!!!</div><?php 
			        }

	       

	        		$statut='admin';
					$prodm=$DB->query("SELECT * from personnel where statut!='{$statut}'");?>

					<div class="col">
			    
				    	<table class="table table-hover table-bordered table-striped table-responsive">

				    		<thead>
			    				<tr>
			                    	<th colspan="2" class="text-center bg-info" style="text-align: center">Liste du personnels</th>

									<th colspan="5" class="text-center bg-info"><a href="personnel.php?ajout_en">Ajouter un personnel</a></th>
			                    	
			                  	</tr>

								<tr>
									<th>Nom & Prénom</th>
									<th>Fonction</th>
									<th>Phone</th>
									<th>Identifiant</th>
									<th>Mot de passe</th>

									<th colspan="2"></th>
								</tr>

							</thead>

							<tbody><?php

								if (empty($prodm)) {
									# code...
								}else{

									foreach ($prodm as $formation) {?>

										<tr>

											<td><?=ucwords($formation->nom);?></td>

											<td><?=ucfirst($formation->statut);?></td>

					                        <td><?=$formation->telephone;?></td>

											<td><?=$formation->pseudo;?></td>
											<td></td>

											<td colspan="2"><?php if ($_SESSION['statut']!='caissier') {?>
												<a class="btn btn-warning" href="personnel.php?modif_en=<?=$formation->id;?>">Modifier</a>

					                        	<a class="btn btn-danger" href="personnel.php?del_pers=<?=$formation->id;?>" onclick="return alerteS();">Supprimer</a><?php }?>
					                        </td><?php

										}?>

									</tr><?php
								}?>

							
							</tbody>

						</table>

					</div><?php
				}
			}
	
		}else{

		    header('Location: deconnexion.php');


		  }?>
  
</body>
</html><?php

require 'footer.php';?>

		

<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>