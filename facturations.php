<?php require 'header3.php';

if (!empty($_SESSION['pseudo'])) {

  if (isset($_GET['deletemodif'])) {

    $DB->delete('DELETE FROM validpaiemodif WHERE pseudov=?', array($_SESSION['idpseudo']));

    $DB->delete('DELETE FROM validventemodif where pseudop=?', array($_SESSION['idpseudo']));    

    $_SESSION['panier'] = array();
    $_SESSION['panieru'] = array();
    $_SESSION['error']=array();
    $_SESSION['clientvip']=array();
    $_SESSION["montant_paye"]=array();
    $_SESSION['remise']=array();
    $_SESSION['product']=array();
    unset($_SESSION['banque']);
    unset($_SESSION['proformat']);
    unset($_SESSION['alertesvirement']);
    unset($_SESSION['alerteschequep']);
  }

  $pseudo=$_SESSION['pseudo'];

  if ($_SESSION['level']>=3) {

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

      $_SESSION['date01']=$_POST['j1'];
      $_SESSION['date1'] = new DateTime($_SESSION['date01']);
      $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
      
      $_SESSION['date02']=$_POST['j2'];
      $_SESSION['date2'] = new DateTime($_SESSION['date02']);
      $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

      $_SESSION['dates1']=(new DateTime($_SESSION['date01']))->format('d/m/Y');
      $_SESSION['dates2']=(new DateTime($_SESSION['date02']))->format('d/m/Y');  
    }

    if (isset($_POST['j2'])) {

      $datenormale='entre le '.$_SESSION['dates1'].' et le '.$_SESSION['dates2'];

    }else{

      $datenormale=(new DateTime($dates))->format('d/m/Y');
    }

    if (isset($_POST['clientliv'])) {
      $_SESSION['clientliv']=$_POST['clientliv'];
    }

    require 'headercompta.php';?>

    <div class="container-fluid">

      <div class="col" style="overflow: auto;">
          
        <table class="table table-hover table-bordered table-striped table-responsive">
          <thead>
            <tr><th class="text-center bg-info" colspan="11" height="30"><?="Liste des Facturations " .$datenormale ?></th></tr>

            <tr>
              <form method="POST" action="facturations.php" id="suitec" name="termc">

                <th colspan="7" class="text-center bg-info">

                  <div class="container">
                    <div class="row">
                      <div class="col"><?php

                        if (isset($_POST['j1'])) {?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                        }else{?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                        }?>
                      </div>

                      <div class="col"><?php 

                        if (isset($_POST['j2'])) {?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j2" value="<?=$_POST['j2'];?>" onchange="this.form.submit()"><?php

                        }else{?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                        }?>
                      </div>
                    </div>
                </th>
              </form>

              <form method="POST" action="facturations.php">

                <th colspan="4" class="text-center bg-info">

                  <input class="form-control me-2" id="search-user" name="clientsearch" type="search" placeholder="Search" aria-label="Search">
                    <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
                </th>
              </form>              
            </tr>

            <tr>
              <th>N°</th>
              <th>N° Cmd</th>
              <th>Date Cmd</th>
              <th>Etat</th>
              <th>Remise</th>
              <th>Total</th>
              <th>Montant</th>
              <th>Position</th>
              <th>Client</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody><?php 

            if ($_SESSION['level']>6) {

              if (isset($_POST['j1'])) {

                $products=$DB->query("SELECT *FROM payement where DATE_FORMAT(date_cmd, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");          

              }elseif (isset($_GET['clientsearch'])) {

                $products=$DB->query("SELECT *FROM payement where num_client='{$_GET['clientsearch']}' ");         

              }else{

                $products =$DB->query("SELECT *FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' ");
                

              }
            }else{

              if (isset($_POST['j1'])) {

                $products=$DB->query("SELECT *FROM payement where vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");          

              }elseif (isset($_GET['clientsearch'])) {

                $products=$DB->query("SELECT *FROM payement where vendeur='{$_SESSION['idpseudo']}' and num_client='{$_GET['clientsearch']}' ");         

              }else{

                $products =$DB->query("SELECT *FROM payement WHERE  vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' ");
                

              }

            }        

            $cumulmontanremp=0;
            $cumulmontantotp=0;
            $cumulmontanrestp=0;

            foreach ($products as $key=> $product ){

              $cumulmontanremp+=$product->remise;
              $cumulmontantotp+=$product->Total-$product->remise;
              $cumulmontanrestp+=$product->montantpaye; ?>

              <tr>
                <td style="text-align:center;"><?=$key+1;?></td>

                <td><a target="_blank" style="color: red;" href="ticket_pdf.php?ticket=<?=$product->num_cmd;?>" ><?= $product->num_cmd; ?></a></td>

                <td style="text-align:center;"><?= $panier->formatDate($product->date_cmd); ?></td>

                <td style="text-align:center;"><?= $product->etat; ?></td>

                <td style="text-align:right"><?= number_format($product->remise,0,',',' '); ?></td>

                <td style="text-align: right"><?= number_format(($product->Total-$product->remise),0,',',' '); ?></td>
                <td style="text-align:right"><?= number_format($product->montantpaye,0,',',' '); ?> </td>
                <td style="text-align:left;"><?= $product->position; ?></td>

                <td><?= $panier->nomClient($product->num_client); ?></td>

                <td><?php if ($_SESSION['level']>=6) {?><a onclick="return alerteM();" class="btn btn-warning" href="modifventeprod.php?numcmdpaye=<?=$product->num_cmd;?>&numticketpaye=<?=$product->num_ticket;?>&surplace">Modifier</a><?php }?></td>

                <td><?php if ($_SESSION['level']>6) {?><a onclick="return alerteS();" class="btn btn-danger" href="comptasemaine.php?num_cmd=<?=$product->num_cmd;?>&total=<?=$product->Total-$product->remise;?>"> Supprimer</a><?php }?></td>
              </tr><?php 
            } ?>   
          </tbody>

          <tfoot>
            <tr>
              <th colspan="4"></th>
              <th style="text-align: right;"><?= number_format($cumulmontanremp,0,',',' ');?></th>
              <th style="text-align: right;"><?= number_format($cumulmontantotp,0,',',' ');?></th>
              <th style="text-align: right;"><?= number_format($cumulmontanrestp,0,',',' ');?></th>
            </tr>
          </tfoot>
        </table>
        </div>
        </div><?php 

  }else{

    echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

  }
}else{
  header('Location: deconnexion.php');
}

require 'footer.php';?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
  $(document).ready(function(){
      $('#search-user').keyup(function(){
          $('#result-search').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'recherche_utilisateur.php?clientfact',
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

  function alerteM(){
    return(confirm('Voulez-vous vraiment modifier cette vente?'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>