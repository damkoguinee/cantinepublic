<?php require 'header3.php';

if (!empty($_SESSION['pseudo'])) {

  if (isset($_GET['deletec'])) {

    $DB->delete('DELETE FROM client WHERE id = ?', array($_GET['deletec']));
  }

  if ($_SESSION['statut']!="vendeur") {?>

    <div class="container-fluid mt-2">

      <form method="post"  action="client.php">

        <table class="table table-hover table-bordered table-striped table-responsive text-center">

        <thead>
          <tr>
            <th colspan="5" class="text-center bg-info"><?= "Enregistrer un collaborateur" ?></th>    
          </tr>

          <tr>
            <th>Nom</th>
            <th>Téléphone</th>
            <th>E.mail</th>
            <th>Adresse</th>
            <th>Type</th>
          </tr>
        </thead><?php

        if (isset($_GET['modifc'])) {

          $prodc=$DB->querys('SELECT * FROM client WHERE id= ?', array($_GET["modifc"]));?>
          
          <tbody>

            <tr>
              <td><input class="form-control" type="text" name="client" value="<?=$prodc['nom_client'];?>"><input type="hidden" name="id" value="<?=$prodc['id'];?>"><input type="hidden"   name="mdp" value="0000"></td>                        
              <td><input class="form-control" type="number"   name="tel" value="<?=$prodc['telephone'];?>"></td>
              <td><input class="form-control" type="text"   name="email" value="<?=$prodc['mail'];?>"></td>
              <td><input class="form-control" type="text"   name="ad" value="<?=$prodc['adresse'];?>"></td>

              <td style="text-align: left; width:10%;"><select class="form-select" name="type" required="">
                <option value="<?=$prodc['type'];?>"><?=$prodc['type'];?></option>
                <option value="fournisseur">Fournisseur</option>
                <option value="client">Client</option>
                <option value="clientf">Client & Fournisseur</option>
                <option value="compte">Compte</option>              
                <option value="banque">Banque</option>             
                <option value="employer">Salarié</option>
                <option value="proprietaire">Propriétaire</option>
                <option value="autres">Autres</option></select>
              </td>
            </tr>

          </tbody><?php
        }else{?>

          <tbody>

            <tr>
              <td><input class="form-control" type="text" name="client" required=""></td>                        
              <td><input class="form-control" type="number"   name="tel" required=""></td>
              <td><input class="form-control" type="text"   name="email"></td>
              <td><input class="form-control" type="text"   name="ad"></td>

              <td class="name"><select class="form-select" name="type" required="">
                <option value=""></option>
                <option value="fournisseur">Fournisseur</option>
                <option value="client">Client</option>
                <option value="clientf">Client & Fournisseur</option>
                <option value="compte">Compte</option>              
                <option value="Banque">Banque</option>             
                <option value="employer">Salarié</option>
                <option value="proprietaire">Propriétaire</option>
              <option value="autres">Autres</option></select>
              </td>
            </tr>

          </tbody><?php
        }?>

      </table><?php

      if (isset($_GET['modifc'])) {?>

        <input class="btn btn-primary" id="button" name="modifc"  type="submit" value="VALIDER" onclick="return alerteV();"><?php

      }else{?>

        <input class="btn btn-primary" id="button"  type="submit" value="VALIDER" onclick="return alerteV();"><?php

      }?>           

    </form>

  </div>

  <div class="container-fluid mt-2"><?php

    if (!isset($_POST["client"])) {
            
    }else{

      if (isset($_POST['modifc'])) {
        $client=$panier->h($_POST['client']);
        $tel=$panier->h($_POST['tel']);
        $ad=$panier->h($_POST['ad']);
        $type=$panier->h($_POST['type']);
        $id=$panier->h($_POST['id']);
        $mail=strtolower($_POST['email']);
        $mdp=$_POST['mdp'];
        $mdp=password_hash($mdp, PASSWORD_DEFAULT);

        $DB->insert('UPDATE client SET nom_client = ?, telephone=?, mail=?, pseudo=?, mdp=?, type=? WHERE id = ?', array($client, $tel, $mail, $tel, $mdp, $type, $_POST['id']));?>

          <div class="alert alert-success">Collaborateur modifié avec succèe!!:</div><?php 

      }else{

        $products=$DB->querys('SELECT * FROM client WHERE nom_client= ?', array($_POST["client"]));

        if (empty($products)) {

          if ($_POST["client"]=='' OR $_POST["tel"]=='' OR $_POST["type"]=='') {?>

            <div class="alert alert-warning"><?="Completez tous les champs"; ?></div><?php

          }else{
            $client=$panier->h($_POST['client']);
            $tel=$panier->h($_POST['tel']);
            $ad=$panier->h($_POST['ad']);
            $type=$panier->h($_POST['type']);

            $mail=strtolower($_POST['email']);
            $mdp='0000';
            $mdp=password_hash($mdp, PASSWORD_DEFAULT);

            //var_dump($client, $tel, $ad, $type, $mail, $mdp);

            $DB->insert('INSERT INTO client(nom_client, telephone, mail, adresse, pseudo, mdp, type) values(?, ?, ?, ?, ?, ?, ?)', array(ucwords($client), $tel, $mail, ucwords($ad), $tel, $mdp, ucwords($type)));


            $prodclient=$DB->querys("SELECT max(id) as id from client ");

            

            if ($type=='Banque') {
              $nombanque=$panier->nomClientad($prodclient['id'])[0];

              $typeb=strtolower($type);

              $DB->insert('INSERT INTO nombanque (id, nomb, numero, type) VALUES(?, ?, ?, ?)', array($prodclient['id'], $nombanque, $prodclient['id'], $typeb));
            }?>

            <div class="alert alert-success">Collaborateur ajouté avec succèe!!:</div><?php 

          }

        }else{?>

            <div class="alert alert-warning"><?php echo "Ce nom est déjà attribué"; ?></div><?php        

        }
      }

    }

// Liste des clients
    $products = $DB->query('SELECT * FROM client order by(nom_client)');

    if (empty($products)) {

    }else{?>

      <table class="table table-hover table-bordered table-striped table-responsive">

        <thead>

          <tr>
            <th class="text-center bg-info" colspan="6"><?php echo "Liste des collaborateurs  " ?></th>
          </tr>

          <tr>
            <th>Nom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Type</th>
            <th></th>
            <th></th>
          </tr>

        </thead>

        <tbody><?php

          foreach ($products as $product ): ?>

            <tr>
                      
              <td><?=ucwords(strtolower($product->nom_client));?></td>
              <td><?=$product->telephone; ?></td>
              <td><?=$product->mail; ?></td>
              <td><?=ucwords(strtolower($product->type)) ; ?></td> 

              <td><a href="client.php?modifc=<?=$product->id;?>"> <input class="btn btn-warning" type="submit" value="Modifier"></a></td>

              <td><a href="client.php?deletec=<?=$product->id;?>"> <input class="btn btn-success" type="submit" value="Supprimer" onclick="return alerteS();"></a></td>

            </tr>

          <?php endforeach ?>

        </tbody>

      </table>

      </div><?php
    }


  }else{

    echo "Vous n'avez pas toutes les autorisations réquises";

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
