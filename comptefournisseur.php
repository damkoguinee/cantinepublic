<div class="container-fluid">
  <div class="col-sm-12 col-md-8"><?php 
    $colspan=sizeof($panier->monnaie);?>
    <table class="table table-hover table-bordered table-striped table-responsive">

      <thead>

        <tr>

          <th colspan="3" class="text-center">
            <?php 
            if (isset($_GET['clientsearch'])) {
              $_SESSION['reclient']=$_GET['clientsearch'];
            }?>          
            Compte Fournisseurs
              <a style="margin-left: 10px;"href="printcomptecategorie.php?comptefournisseur" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a>
            </th>
        </tr>

        <tr>
          <th class="text-center">N°</th>
          <th class="text-center">Nom</th>
          <th class="text-center">Solde Compte</th>
        </tr>

      </thead>

      <tbody><?php 
        $cumulmontantgnf=0;
        $cumulmontanteu=0;
        $cumulmontantus=0;
        $cumulmontantcfa=0;

         

        if (isset($_GET['clientsearch'])) {

          $prodclient = $DB->query("SELECT *FROM client where id='{$_SESSION['reclient']}'");

        }elseif (isset($_GET['magasin']) and $_GET['magasin']=='general') {

          $type1='fournisseur';
          $type2='fournisseur';

          $prodclient = $DB->query("SELECT *FROM client where (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");

        }elseif (isset($_GET['magasin'])) {

          $type1='fournisseur';
          $type2='fournisseur';

          $prodclient = $DB->query("SELECT *FROM client where positionc='{$_GET['magasin']}' and (typeclient='{$type1}' or typeclient='{$type2}') order by(nom_client) ");

        }else{

          $type1='fournisseur';
          $type2='fournisseur';

          $prodclient = $DB->query("SELECT *FROM client where (type='{$type1}' or type='{$type2}') order by(nom_client) ");
        }
        


        foreach ($prodclient as $key => $value){

          $prodmax= $DB->querys("SELECT max(date_versement) as datev FROM bulletin where nom_client='{$value->id}' ");

          $now = date('Y-m-d');
          $datederniervers = $prodmax['datev'];

          $now = new DateTime( $now );
          $now = $now->format('Ymd');
          $datederniervers = new DateTime($datederniervers);
          $datederniervers = $datederniervers->format('Ymd');

          $deltadate=($now-$datederniervers);

          $delai=30;

          $delaialerte=15;?>

          <tr>

            <td style="text-align: center; font-size: 20px; "><?=$key+1; ?></td>

            <td style="font-size: 20px;"><?= ucwords(strtolower($value->nom_client)); ?>              
              
            </td> <?php

            foreach ($panier->monnaie as $valuem) {        

              $products= $DB->querys("SELECT sum(montant) as montant, devise, nom_client FROM bulletin where nom_client='{$value->id}' and devise='{$valuem}' ");

              if ($products['devise']=='gnf') {
                $cumulmontantgnf+=$products['montant'];
                $devise='gnf';
              }

              if ($products['devise']=='eu') {
                $cumulmontanteu+=$products['montant'];
                $devise='eu';
              }

              if ($products['devise']=='us') {
                $cumulmontantus+=$products['montant'];
                $devise='us';
              }

              if ($products['devise']=='cfa') {
                $cumulmontantcfa+=$products['montant'];
                $devise='cfa';
              }

              if ($products['devise']!='gnf' and $products['devise']!='eu' and $products['devise']!='us' and $products['devise']!='cfa') {
                $devise='gnf';
              }

              if ($products['montant']>0) {
                $color='red';
                $montant=$products['montant'];
              }else{

                $color='green';
                $montant=-$products['montant'];

              }?>

              <td style="text-align: right; padding-right: 5px; color: white; font-size: 20px; background-color: <?=$color;?>"><a style="color:white;" href="bilan.php?bclient=<?=$products['nom_client'];?>&devise=<?=$devise;?>"><?= number_format($montant,0,',',' '); ?></a></td><?php 
            }?>

          </tr><?php

        }?>

      </tbody><?php 

      if ($cumulmontantgnf>0) {

        $cmontantgnf=$cumulmontantgnf;
      }else{
        $cmontantgnf=-$cumulmontantgnf;

      }

      if ($cumulmontanteu>0) {
        
        $cmontanteu=$cumulmontanteu;
      }else{
        $cmontanteu=-$cumulmontanteu;

      }

      if ($cumulmontantus>0) {
        
        $cmontantus=$cumulmontantus;
      }else{
        $cmontantus=-$cumulmontantus;

      }

      if ($cumulmontantcfa>0) {
        
        $cmontantcfa=$cumulmontantcfa;
      }else{
        $cmontantcfa=-$cumulmontantcfa;

      }?>

      <tfoot>
          <tr>
            <th colspan="2">Solde</th>

            <th style="font-size: 20px; text-align: right; padding-right: 5px; background-color: <?=$panier->color($cumulmontantgnf);?>"><?= number_format($cmontantgnf,0,',',' ');?></th>           
          </tr>
      </tfoot>

    </table>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'recherche_utilisateur.php?compteclient',
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

