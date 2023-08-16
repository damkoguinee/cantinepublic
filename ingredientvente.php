<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {

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

    require 'headerstock.php';?>


    <div class="container-fluid">

      <div class="row" style="overflow: auto">


        <table class="table table-hover table-bordered table-striped table-responsive text-center">
          <thead>

            <tr><th colspan="4"><?="Tableau des ingrédients Vendus " .$datenormale ?></th></tr>

            <tr>
              <form method="POST" action="ingredientvente.php" id="suitec" name="termc">

                <th colspan="2" >
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

              <form method="GET" action="ingredientvente.php">

                <th colspan="2">

                  <select class="form-select" id="search-user" name="clientsearch" onchange="this.form.submit()"><?php 

                    if (isset($_GET['clientsearch'])) {?>
                      <option value="<?=$_GET['clientsearch'];?>"><?=$panier->nomIngredient($_GET['clientsearch']);?></option><?php
                    }else{?>
                      <option></option><?php 
                    }

                    foreach ($panier->ingredient() as $value) {?>

                      <option value="<?=$value->id;?>"><?=$value->nom;?></option><?php 
                    }?>
                    
                  </select>
                    <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
                </th>
              </form>

              
            </tr>

            <tr>
              <th>N°</th>
              <th>Désignation</th>
              <th>Qtite Vendues</th>
              <th>Qtite dispo</th>
            </tr>
          </thead>
          <tbody><?php           

            $cumulmontanremp=0;
            $cumulmontantotp=0;
            $cumulmontanrestp=0;

            if (isset($_GET['clientsearch'])) {

              $prodingredient = $DB->query("SELECT id, nom, qtite FROM ingredient where id='{$_GET['clientsearch']}'");
            }else{

              $prodingredient = $DB->query("SELECT id, nom, qtite FROM ingredient");

            }

            foreach ($prodingredient as $key=> $valuei ){

              $sortie='sortie';

              if (isset($_POST['j1'])) {

                $products=$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv where idstock='{$valuei->id}' and libelle='{$sortie}' and DATE_FORMAT(dateop, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(dateop, \"%Y%m%d\") <= '{$_SESSION['date2']}'");          

              }elseif (isset($_GET['clientsearch'])) {

                $products=$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv where idstock='{$_GET['clientsearch']}' and libelle='{$sortie}' ");         

              }else{

                $products =$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv WHERE  idstock='{$valuei->id}' and libelle='{$sortie}' and DATE_FORMAT(dateop, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(dateop, \"%Y%m%d\") <= '{$_SESSION['date2']}'"); 
              }

              if (!empty($products['qtite'])) {?>

                <tr>
                  <td style="text-align:center;"><?=$key+1;?></td>
                  <td><?=ucwords(strtolower($valuei->nom));?></td>

                  <td style="text-align:center;"><?=number_format(-1*$products['qtite'],2,',',' ');?></td>

                  <td style="text-align:center;"><?=$valuei->qtite;?></td>

                  
                </tr><?php 
              }
            } ?>   
          </tbody>
        </table>
      </div>
    </div><?php 

  }else{

    echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

  }
}?>

</body>
</html><?php

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