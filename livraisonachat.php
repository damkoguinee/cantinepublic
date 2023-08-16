<?php require 'header3.php';

if (!empty($_SESSION['pseudo'])) { 

  $pseudo=$_SESSION['pseudo'];

  require 'entetelivraisonachat.php';

  if (isset($_GET['delete'])) {

    $numcmd=$panier->h($_GET['numcmd']);

    $DB->insert("UPDATE payement SET etatliv=? WHERE num_cmd=? ", array('nonlivre', $numcmd));

    //$DB->insert('INSERT INTO livraisondelete (numcmdliv, id_clientliv, idpersonnel, datedelete) VALUES(?, ?, ?, ?, ?, ?, now())', array($id, $qtiteliv, $numcmd, $idclient, $_SESSION['idpseudo'], $idstock));

    $DB->delete('DELETE FROM livraison WHERE numcmdliv = ?', array($numcmd));?>

    <div class="alert alert-success">Commande annulée avec succèe!!!</div><?php
  }?>

  <div class="container-fluid"><?php 

    if (isset($_GET['nonlivre']) or isset($_POST['j1']) or isset($_POST['numcmd']) or isset($_POST['clientliv'])) {

      if (!isset($_POST['j1'])) {

        $_SESSION['date']=date("Ymd");  
        $dates = $_SESSION['date'];
        $dates = new DateTime( $dates );
        $dates = $dates->format('Ymd'); 
        $_SESSION['date']=$dates;
        $_SESSION['date1']=$dates;
        $_SESSION['date2']=$dates;
        $_SESSION['dates1']=$dates; 

      }else{

        $_SESSION['date1']=$_POST['j1'];
        $_SESSION['date1'] = new DateTime($_SESSION['date1']);
        $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
        $_SESSION['date2'] = new DateTime($_SESSION['date2']);
        $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

        $_SESSION['dates1']=$_SESSION['date1'];
        $_SESSION['dates2']=$_SESSION['date2']; 

          
      }

      if (isset($_POST['clientliv'])) {
        $_SESSION['clientliv']=$_POST['clientliv'];
      }

      if (isset($_POST['numcmd'])) {
        $_SESSION['numcmd']=$_POST['numcmd'];
      }
      

      if ($_SESSION['level']>=3) {?>


        <div class="col"> 

          <table class="table table-hover table-bordered table-striped table-responsive text-center">

            <thead>

              
              <tr>

                <form method="POST" action="livraisonachat.php">

                  <th style="border-right: 0px;" colspan="3"><?php

                    if (isset($_POST['j1'])) {?>

                      <input class="form-control" id="reccode" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                    }else{?>

                      <input class="form-control" id="reccode" style="width: 120px; font-size: 14px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                    }?>
                  </th>
                </form>

                <form method="POST" action="livraisonachat.php">

                  <th>

                    <input class="form-control" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
                  </th>
                </form>

                <form method="POST" action="livraisonachat.php">

                  <th colspan="2"><?php

                    if (isset($_POST['numcmd'])) {?>

                      <input class="form-control" type = "text" name = "numcmd" value="<?=$_POST['numcmd'];?>" onchange="this.form.submit()"><?php

                    }else{?>

                      <input class="form-control" type = "text" name = "numcmd" placeholder="rechercher par N°" onchange="this.form.submit()"><?php

                    }?>
                  </th>
                </form>
              </tr>

              <tr>

                <th class="text-center bg-info" colspan="6"><?="Liste des Commandes non Livrées";?> </th>
              </tr>

              <tr>
                <th>N°</th>
                <th>N° cmd</th>
                <th>Total Achat</th>
                <th>Nom du Client</th>
                <th>Date cmd</th>
                <th>Action</th>
              </tr>

            </thead>

            <tbody><?php 
              $cumulmontant=0;
              $etatliv='livre';
              if (isset($_POST['j1'])) {

                $products= $DB->query("SELECT *FROM payement inner join client on client.id=num_client  WHERE etatliv!='{$etatliv}' and (DATE_FORMAT(date_cmd, \"%Y-%m-%d \")='{$_POST['j1']}') order by(date_cmd) desc LIMIT 10");

              }elseif (isset($_GET['searchnlclient'])) {

                $products= $DB->query("SELECT *FROM payement inner join client on client.id=num_client  WHERE etatliv!='{$etatliv}' and (num_client='{$_GET['searchnlclient']}') order by(date_cmd) desc LIMIT 10");

              }elseif (isset($_POST['numcmd'])) {

                $products= $DB->query("SELECT *FROM payement inner join client on client.id=num_client  WHERE etatliv!='{$etatliv}' and (num_cmd='{$_POST['numcmd']}') order by(date_cmd) desc LIMIT 10");

              }else{

                $products= $DB->query("SELECT *FROM payement inner join client on client.id=num_client  WHERE etatliv!='{$etatliv}' order by(date_cmd) desc LIMIT 30");
              }

              foreach ($products as $key=> $product ){

                $cumulmontant+=($product->Total-$product->remise); ?>

                <tr>
                  <td style="text-align: center;"><?= $key+1; ?></td>

                  <td style="text-align: center;"><a style="color: red; text-align: center;" href="recherche.php?recreditc=<?=$product->num_cmd;?>"><?= $product->num_cmd; ?></a></td>

                  <td style="text-align: right; padding-right: 10px;"><?= number_format($product->Total,0,',',' '); ?></td>

                  <td><?= ucwords(strtolower($product->nom_client)).' Tél: '.$product->telephone; ?></td>                 

                  <td style="text-align:center;"><?=(new DateTime($product->date_cmd))->format('d/m/Y'); ?></td>

                  <td><a class="btn btn-warning" href="livraison.php?livraison=<?=$product->num_cmd;?>">Livrer</a></td>
                </tr><?php 
              }?>

            </tbody>

            <tfoot>
                <tr>
                  <th colspan="2">Totaux</th>
                  <th style="text-align: right; padding-right: 10px;"><?= number_format($cumulmontant,0,',',' ');?></th>
                </tr>
            </tfoot>

          </table>

        </div><?php

      }else{

        echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

      }
    }else{


      if (!isset($_POST['j2'])) {

      $_SESSION['date']=date("Ymd");  
      $dates = $_SESSION['date'];
      $dates = new DateTime( $dates );
      $dates = $dates->format('Ymd'); 
      $_SESSION['date']=$dates;
      $_SESSION['date1']=$dates;
      $_SESSION['date2']=$dates;
      $_SESSION['dates1']=$dates; 

    }else{

      $_SESSION['date1']=$_POST['j2'];
      $_SESSION['date1'] = new DateTime($_SESSION['date1']);
      $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
      $_SESSION['date2'] = new DateTime($_SESSION['date2']);
      $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

      $_SESSION['dates1']=$_SESSION['date1'];
      $_SESSION['dates2']=$_SESSION['date2']; 

        
    }

    if (isset($_POST['clientlivr'])) {
      $_SESSION['clientliv']=$_POST['clientlivr'];
    }

    if (isset($_POST['numcmdliv'])) {
      $_SESSION['numcmd']=$_POST['numcmdliv'];
    }
    

    if ($_SESSION['level']>=3) {?>


      <div class="col"> 

        <table class="table table-hover table-bordered table-striped table-responsive text-center">

          <thead>              
            <tr>

              <form method="POST" action="livraisonachat.php">

                <th style="border-right: 0px;" colspan="2"><?php

                  if (isset($_POST['j1'])) {?>

                    <input id="reccode" class="form-control" type = "date" name = "j2" onchange="this.form.submit()" value="<?=$_POST['j2'];?>"><?php

                  }else{?>

                    <input id="reccode" class="form-control" type = "date" name = "j2" onchange="this.form.submit()"><?php

                  }?>
                </th>
              </form>

              <form method="POST" action="livraisonachat.php">

                <th colspan="2">

                  <input class="form-control" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                  <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
                </th>
              </form>

              <form method="POST" action="livraisonachat.php">

                <th colspan="2"><?php

                  if (isset($_POST['numcmd'])) {?>

                    <input class="form-control" type = "text" name = "numcmdliv" value="<?=$_POST['numcmdliv'];?>" onchange="this.form.submit()"><?php

                  }else{?>

                    <input class="form-control" type = "text" name = "numcmdliv" placeholder="rechercher par N°" onchange="this.form.submit()"><?php

                  }?>
                </th>
              </form>
            </tr>

            <tr>

              <th class="text-center bg-info" colspan="6"><?="Liste des commandes Livrés ";?> </th>
            </tr>

            <tr>
              <th>N°</th>
              <th>N° cmd</th>
              <th>Nom du Client</th>
              <th>Livreur</th>
              <th>Date Livraison</th>
              <th>Action</th>
            </tr>

          </thead>

          <tbody><?php 

            $etatliv='nonlivre';
            if (isset($_POST['j2'])) {

              $products= $DB->query("SELECT livraison.id as id, numcmdliv, id_clientliv, livreur, dateliv FROM livraison WHERE (DATE_FORMAT(dateliv, \"%Y-%m-%d \")='{$_POST['j2']}') order by(id_clientliv)");

            }elseif (isset($_GET['searchversclient'])) {

              $products= $DB->query("SELECT livraison.id as id, numcmdliv, id_clientliv, livreur, dateliv FROM livraison  WHERE (id_clientliv='{$_GET['searchversclient']}') order by(id_clientliv)");

            }elseif (isset($_POST['numcmdliv'])) {

              $products= $DB->query("SELECT * FROM livraison WHERE (numcmdliv='{$_POST['numcmdliv']}') order by(id_clientliv)");

            }else{

              $products= $DB->query("SELECT * FROM livraison order by(dateliv) LIMIT 10");
            }

            $totqtite=0;

            foreach ($products as $key=> $product ){?>

              <tr>
                <td style="text-align: center;"><?= $key+1; ?></td>

                <td style="text-align: center;"><a style="color: red; text-align: center;" href="recherche.php?recreditc=<?=$product->numcmdliv;?>"><?= $product->numcmdliv; ?></a></td>

                <td><?=$panier->nomClient($product->id_clientliv); ?></td> 

                <td><?=ucwords($panier->nomPersonnel($product->livreur)[0]); ?></td>                 

                <td style="text-align:center;"><?=(new DateTime($product->dateliv))->format('d/m/Y'); ?></td>

                <td><a class="btn btn-danger" onclick="return alerteL();" href="livraisonachat.php?delete=<?=$product->id;?>&numcmd=<?=$product->numcmdliv;?>">Annuler</a></td>
              </tr><?php 
            }?>

          </tbody>

        </table>

        </div><?php

      }else{

        echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

      }
    }

  }else{

    header('Location: deconnexion.php');

  }

  require 'footer.php';?>?>
    
</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script><?php 

if (isset($_GET['livre'])) {?>

  <script>
      $(document).ready(function(){
          $('#search-user').keyup(function(){
              $('#result-search').html("");

              var utilisateur = $(this).val();

              if (utilisateur!='') {
                  $.ajax({
                      type: 'GET',
                      url: 'recherche_utilisateur.php?clientliv',
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
  </script><?php 
}else{?>

  <script>
      $(document).ready(function(){
          $('#search-user').keyup(function(){
              $('#result-search').html("");

              var utilisateur = $(this).val();

              if (utilisateur!='') {
                  $.ajax({
                      type: 'GET',
                      url: 'recherche_utilisateur.php?clientnonliv',
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
  </script><?php
}?>

<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteL(){
        return(confirm('Confirmer la livraison'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>
