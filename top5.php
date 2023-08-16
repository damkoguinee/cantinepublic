<?php require 'header3.php';

require 'headerstatistiques.php';

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
}?>

<div class="container-fluid">

  <div class="row">

    <div class="col-sm-12 col-md-6">

      <table class="table table-hover table-bordered table-striped table-responsive ">

        <thead>

          <tr>

            <form method="POST" action="top5.php" id="suitec" name="termc">

              <th colspan="3" class="bg-info" >

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

                    <div class="col"><?="Top 10 des Produits";?></div>
                  </div>
                </div>

              </th>
            </form>
          </tr>

          <tr>
            <th>N°</th>
            <th>Désignation</th>
            <th class="text-center">Qtités</th>
          </tr>

        </thead>

        <tbody><?php 
          $cumul=0;
          $cumulben=0;

          $prod = $DB->query('SELECT *FROM stock where type!="supplements" and type!="accompagnements"');

          foreach ($prod as $product ){

            $nbre=$panier->nbreprodstatpardate($product->id, $_SESSION['date1'], $_SESSION['date2']);

            $benefice=$panier->beneficeprodstatpardate($product->id, $_SESSION['date1'], $_SESSION['date2']);

            if (!empty($nbre)) {
              
              $DB->insert('INSERT INTO intertopproduit (idprod, quantite, benefice, pseudo) VALUES(?, ?, ?, ?)', array($product->id, $nbre, $benefice, $_SESSION['idpseudo']));
            }
          }

          $products = $DB->query("SELECT *FROM intertopproduit where pseudo='{$_SESSION['idpseudo']}' order by(quantite) desc");


          foreach ($products as $key=> $product ){

            if ($key<=9) {

              $cumul+=$product->quantite;
              $cumulben+=$product->benefice;?>

              <tr>

                <td style="text-align: center;"><?= $key+1; ?></td>

                <td><?=ucwords(strtolower($panier->nomProduit($product->idprod))); ?></td>

                <td style="text-align: center;"><?=number_format($product->quantite,0,',',' ');?></td>

              </tr><?php
            }

          }?>

        </tbody>

        <tfoot>
            <tr>
                <th colspan="2" class="text-center">Totaux</th>
                <th style="text-align: center;"><?=number_format($cumul,0,',',' ');?></th>
            </tr>
        </tfoot>

      </table>
    </div>
  </div>
  </div><?php 

  $DB->delete('DELETE FROM intertopproduit');?>
      
