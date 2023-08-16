
<div class="container-fluid text-left bg-info mt-3 mb-3" >

  <div class="row p-4 m-0"><?php

    if ($_SESSION['level']>3) {?>

      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light " href="dec.php?retrait=<?='decaissement client';?>&frais">RETRAIT</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="decdepense.php?depense=<?='depenses';?>">DEPENSES</a>
      </div><?php
      
    }?>
  </div>
</div>
