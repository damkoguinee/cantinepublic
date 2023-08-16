<div>
  <div>
    <table class="payement" style="width: 95%;">

      <thead>

        <tr>

          <form method="GET"  action="bulletin.php?client">
            <th colspan="2"><select style="width: 250px; height: 30px; font-size: 19px;" type="text" name="clientsearch" onchange="this.form.submit()"><?php
              if (isset($_GET['clientsearch'])) {
                if (isset($_GET['clientsearch'])) {
                  $_SESSION['reclient']=$_GET['clientsearch'];
                }?>

                <option><?=$panier->nomClient($_SESSION['reclient']);?></option><?php

              }else{?>
                <option>Selectionnez le client</option><?php
              }

              $type1='Employer';
              $type2='Employer';


              foreach($panier->clientF($type1, $type2) as $product){?>

                <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
              }?></select>
            </th>
          
            <th colspan="4" height="30">Compte Personnels 
              <a style="margin-left: 10px;"href="printcomptecategorie.php?comptepersonnel" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a>
              <a style="margin-left: 10px;"href="csv.php?personnel" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/excel.jpg"></a>
            </th>
          </form>
        </tr>

        <tr>
          <th>N°</th>
          <th>Nom</th>
          <th>Solde Compte</th>
          <th></th>
        </tr>

      </thead>

      <tbody><?php 
        $cumulmontant=0;

        $type1='Employer';
        $type2='Employer';

        if (isset($_GET['clientsearch'])) {
          $nomclient = $DB->query("SELECT *FROM client where id='{$_SESSION['reclient']}'");

          $panier=$nomclient;

        }else{

          $panier=$panier->clientF($type1, $type2);

          
        }

        foreach ($panier as $key => $value){

          $products= $DB->querys("SELECT sum(montant) as montant FROM bulletin where nom_client='{$value->id}' ");

          $cumulmontant+=$products['montant'];

          if ($products['montant']>0) {
            $color='red';
            $montant=$products['montant'];
          }else{

            $color='green';
            $montant=-$products['montant'];

          } ?>

          <tr>
            <td style="text-align: center; color: white; font-size: 20px; background-color: <?=$color;?> "><?=$key+1; ?></td>

            <td style="color: white; font-size: 20px; background-color: <?=$color;?>"><?= $value->nom_client; ?></td> 

            <td style="text-align: right; padding-right: 5px; color: white; font-size: 20px; background-color: <?=$color;?>"><?= number_format($montant,0,',',' '); ?></td>

            <td style=""><a href="bulletin.php?soldeclient=<?=$value->id;?>"><input style="width: 100%;height: 30px; font-size: 17px; background-color: orange;color: white; cursor: pointer;"  type="submit" value="Voir+" onclick="return alerteS();"></a></td>
          </tr><?php 
        }?>

      </tbody><?php 

      if ($cumulmontant>0) {
        $color='red';
        $cmontant=$cumulmontant;
      }else{

        $color='green';
        $cmontant=-$cumulmontant;

      }?>

      <tfoot>
          <tr>
            <th colspan="2">Totaux</th>
            <th style="text-align: right; padding-right: 5px; background-color: <?=$color;?>"><?= number_format($cmontant,0,',',' ');?></th>
          </tr>
      </tfoot>

    </table>
  </div>
</div>

