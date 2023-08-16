<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {

    $pseudo=$_SESSION['pseudo'];


    if ($_SESSION['level']>=3) {

        if (isset($_GET['deleteret'])) {

            $DB->delete("DELETE from decdepense where numdec='{$_GET['deleteret']}'");

            $DB->delete("DELETE from bulletin where numero='{$_GET['deleteret']}'");

            $DB->delete("DELETE from banque where numero='{$_GET['deleteret']}'");?>

            <div class="alerteV">Suppression reussi!!</div><?php 
        }

        require 'navdec.php'; ?>

        <div class="container-fluid"><?php 

            if (isset($_GET['ajout'])) {?>

                <form method="post"  action="decdepense.php" enctype="multipart/form-data">              

                    <table class="table table-hover table-bordered table-striped table-responsive text-center">

                        <thead>
                          <tr>
                            <th colspan="6" class="text-center bg-info">Enregistrer une dépense</th>  
                          </tr>

                          <tr>
                            <th>Montant décaissé</th>
                            <th>Payement</th>
                            <th>Compte à Prélever</th>
                            <th>commentaires</th>
                            <th>Date</th>
                            <th>Justificatifs</th>               
                          </tr>

                        </thead>
                            
                        <tbody>
                                <td><input class="form-control" type="number" min="0"  name="montant" required="" ></td>
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

                                <td><input class="form-control" type="text" name="coment" required=""></td>

                                <td><input class="form-control" type="date" name="datedep"></td>

                                <td>
                                    <input class="form-control" type="file" name="just[]"multiple id="photo" />
                                    <input class="form-control" type="hidden" value="b" name="env"/>
                                  </td>

                            </tbody>

                        </table><?php

                        if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

                            <input class="btn btn-primary" id="button"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();"><?php

                        }else{?>

                            <div class="alert alert-danger"> CAISSE CLOTUREE OU LA LICENCE EST EXPIREE </div><?php

                        }?>
                    
                    </form><?php
                        
                }?>

            </div><?php


            if (isset($_POST['valid'])){

                if ($_POST['montant']<0){?>

                    <div class="alert alert-warning">FORMAT INCORRECT</div><?php

                }elseif ($_POST['montant']>$panier->montantCompte($_POST['compte'])) {?>

                    <div class="alert alert-warning">Echec montant decaissé est > au montant disponible en caisse</div><?php

                }elseif ($_POST['montant']>$panier->montantCompte($_POST['compte'])) {?>

                    <div class="alert alert-warning">Echec montant decaissé est > au montant disponible</div><?php

                }else{                         

                    if ($_POST['montant']!="") {

                        $numdec = $DB->querys('SELECT max(id) AS id FROM decdepense ');
                        $numdec=$numdec['id']+1;

                        if(isset($_POST["env"])){

                          require "uploadep.php";
                        }

                        $heure=date("H:i:s");

                        if (empty($_POST['datedep'])) {
          
                          $dateop=$_SESSION['datev'].' '.$heure;
                          
                        }else{

                          $dateop=$_POST['datedep'].' '.$heure;
                        }

                        if (empty($dateop)) {
                            
                            $DB->insert('INSERT INTO decdepense (numdec, montant, payement, coment, cprelever, date_payement) VALUES(?, ?, ?, ?, ?,  now())',array('retd'.$numdec, $_POST['montant'], $_POST['mode_payement'], $_POST['coment'], $_POST['compte']));

                            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], -$_POST['montant'], "Retrait(".$_POST['coment'].')', 'retd'.$numdec));
                        }else{

                            $DB->insert('INSERT INTO decdepense (numdec, montant, payement, coment, cprelever, date_payement) VALUES(?, ?, ?, ?, ?,?)',array('retd'.$numdec, $_POST['montant'], $_POST['mode_payement'], $_POST['coment'], $_POST['compte'], $dateop));

                            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], -$_POST['montant'], "Retrait(".$_POST['coment'].')', 'retd'.$numdec, $dateop));

                        }?>

                        <div class="alert alert-success">Retrait enregistré avec succèe!!</div><?php

                    } else{?>

                      <div class="alert alert-warning">Saisissez tous les champs vides</div><?php

                    }

                }

            }else{

            }?>

            <div class="container-fluid mt-"> 

                <table class="table table-hover table-bordered table-striped table-responsive">

                    <thead>

                        <tr>
                          <th class="text-center" colspan="7"><?="Liste des depenses de  " .date("Y"). "  à  ".date("H:i") ?><a class="btn btn-warning" href="decdepense.php?ajout">Effectuer un Retraît</a></th>
                        </tr>

                        <tr>
                          <th>N°</th>
                          <th>Montant</th>
                          <th>Paiement</th>
                          <th>Motif</th>
                          <th>Date</th>
                          <th>Justificatif</th>
                          <th></th>
                        </tr>

                    </thead>

                    <tbody><?php 
                        $cumulmontant=0;

                        $prodep = $DB->query('SELECT numdec, montant, payement, coment, DATE_FORMAT(date_payement, \'%d/%m/%Y \à %H:%i:%s\')AS DateTemps FROM decdepense WHERE YEAR(date_payement) = :annee ORDER BY(date_payement)DESC', array('annee' => date('Y')));

                        foreach ($prodep as $product ){

                            $cumulmontant+=$product->montant;?>

                            <tr>

                                <td style="text-align: center;"><?= $product->numdec; ?></td>
                                
                                <td style="text-align: right; padding-right: 20px;"><?= number_format($product->montant,0,',',' '); ?></td>

                                <td><?= Ucwords($product->payement); ?></td>

                                <td><?= Ucwords($product->coment); ?></td>

                                <td><?= $product->DateTemps; ?></td>

                                <td style="text-align: center"><?php
                                    $num=$product->numdec;
                                    $nom_dossier="justificatifdep/".$product->numdec."/";
                                    if (file_exists($nom_dossier)) {

                                      $dossier=opendir($nom_dossier);
                                      while ($fichier=readdir($dossier)) {

                                        if ($fichier!='.' && $fichier!='..') {?>

                                          <a href="justificatifdep/<?=$product->numdec;?>/<?=$fichier;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a><?php
                                        }
                                      }closedir($dossier);
                                    }?>
                                  </td>

                                <td><?php 

                                if ($_SESSION['statut']!="vendeur") {?><a class="btn btn-danger" onclick="return alerteS();" href="decdepense.php?deleteret=<?=$product->numdec;?>">Supprimer</a><?php }?></td>

                            </tr><?php

                        }?>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th style="text-align: right; padding-right: 20px;"><?= number_format($cumulmontant,0,',',' ');?></th>
                        </tr>
                    </tfoot>

                </table>

            </div><?php

        }else{

            echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
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