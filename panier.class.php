<?php

class panier{
	
	private $DB;

	public $monnaie=['gnf','eu','us', 'cfa'];

	public $modep=['espèces','chèque','virement', 'differé'];

	public $days=['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

	public $times=['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00', "16:00", '17:00','18:00'];

	public $niveau=['TPS','PS','MS','GS','1ere Année','2ème Année','3ème Année','4ème Année','5ème Année','6ème Année','7ème Année','8ème Année','9ème Année','10ème Année','11ème SM ','12ème SM','Terminale SM','11ème SE ','12ème SE','Terminale SE','11ème SS ','12ème SS','Terminale SS'];

	public $matiere=['Anglais','Biologie','Chimie','ECM','Economie','Français','Géographie','Géologie','Histoire','Informatique','Mathématiques','Philosophie','Physique','Sciences'];

	public $moisenlettre=['janvier','fevrier','mars', 'avril', 'mai', 'juin','juillet','aout','septembre','octobre', 'novembre', 'decembre'];

	public function obtenirLibelleMois($mois) {
	  	switch($mois) {
	      case '01': $mois = 'Janvier'; break;
	      case '02': $mois = 'Fevrier'; break;
	      case '03': $mois = 'Mars'; break;
	      case '04': $mois = 'Avril'; break;
	      case '05': $mois = 'Mai'; break;
	      case '06': $mois = 'Juin'; break;
	      case '07': $mois = 'Juillet'; break;
	      case '08': $mois = 'Aout'; break;
	      case '09': $mois = 'Septembre'; break;
	      case '10': $mois = 'Octobre'; break;
	      case '11': $mois = 'Novembre'; break;
	      case '12': $mois = 'Decembre'; break;
	      default: $mois =''; break;
	    }
	    return $mois;
	}

	public function obtenirMois($mois) {
	  	switch($mois) {
	      case 'Janvier': $mois = '01'; break;
	      case 'Février': $mois = '02'; break;
	      case 'Mars': $mois = '03'; break;
	      case 'Avril': $mois = '04'; break;
	      case 'Mai': $mois = '05'; break;
	      case 'Juin': $mois = '06'; break;
	      case 'Juillet': $mois = '07'; break;
	      case 'Août': $mois = '08'; break;
	      case 'Septembre': $mois = '09'; break;
	      case 'Octobre': $mois = '10'; break;
	      case 'Novembre': $mois = '11'; break;
	      case 'Decembre': $mois = '12'; break;
	      default: $mois =''; break;
	    }
	    return $mois;
	}

	public $month = array(            
		1   => 'Janvier',
		2   => 'Février',
		3   => 'Mars',
		4   => 'Avril',
		5   => 'Mai',
		6   => 'Juin',
		7   => 'Juillet',
		8   => 'Août',
		9   => 'Septembre',
		10  => 'Octobre',
		11  => 'Novembre',
		12  => 'Décembre'
	);

	public function jourSemaine($jours) {
		switch($jours) {
		case '0': $jours = 'dimanche'; break;
		case '1': $jours = 'lundi'; break;
		case '2': $jours = 'mardi'; break;
		case '3': $jours = 'mercredi'; break;
		case '4': $jours = 'jeudi'; break;
		case '5': $jours = 'vendredi'; break;
		case '6': $jours = 'samedi'; break;
		default: $jours =''; break;
	  }
	  return $jours;
  	}

	

  public $sexe= array(            
    1   => 'm',
    2   => 'f'        
	);

	public function __construct($DB){

		if(!isset($_SESSION)){
			session_start();
		}

		$this->DB = $DB;

		if (isset($_GET['niveauf'])) {

            $_SESSION['niveauf']=$_GET['niveauf'];
            $_SESSION['niveaufl']='Niveau '.$_GET['niveauf'];
            
        }elseif (isset($_GET['niveauc'])) {

        	unset($_SESSION['niveauf']);
        	$_SESSION['niveaufl']=' du Complexe';
        }
    

        if (isset($_POST['mensuellec'])) {

            $_SESSION['mensuellec']=$_POST['mensuellec'];
            
        }

		if(isset($_POST['mensuellec']) or !empty($_SESSION['mensuellec'])){
			
			$this->mois();
		}

		if (isset($_POST['mois'])){

        $_SESSION['mois']=$_POST['mois'];

    }

	

		if(isset($_POST['mois']) or isset($_GET['mois']) or !empty($_SESSION['mois'])){

			$prodcursus=$DB->query('SELECT nom from cursus');
			
			$this->moisbul();
		}

		if(isset($_POST['mois']) or isset($_GET['mois']) or !empty($_SESSION['mois'])){
			
			$this->cursus();
		}



		if (isset($_GET['ajoute']) or isset($_GET['inscript']) or isset($_POST['semestren'])) {

			if (isset($_POST['semestren'])) {

				$_SESSION['nomcloture']=$_POST['semestren'];

			}else{

				$_SESSION['nomcloture']='inscript';
			}

			$this->cloture();
		}

		if (isset($_GET['typeel'])) {

			$_SESSION['typeel']=$_GET['typeel'];
		}else{

			$_SESSION['typeel']='Elèves';

		}

		if (isset($_POST['groupe'])) {
			$_SESSION['groupe']=$_POST['groupe'];
			$_SESSION['groupep']=$_POST['groupe'];
			$_SESSION['enseigp']=array();
			$param=$_SESSION['groupe'];

		}elseif (isset($_GET['groupeeleve'])) {
			$_SESSION['groupe']=$_GET['groupeeleve'];
			$_SESSION['groupep']=$_GET['groupeeleve'];
			$_SESSION['enseigp']=array();
			$param=$_SESSION['groupe'];

		}elseif(isset($_POST['enseig'])){

			$_SESSION['enseig']=$_POST['enseig'];
			$_SESSION['enseigp']=$_POST['enseig'];
			$param=$_SESSION['enseig'];
			$_SESSION['groupep']=array();

		}elseif(isset($_GET['enseignantplaning'])){

			$_SESSION['enseig']=$_GET['enseignantplaning'];
			$_SESSION['enseigp']=$_GET['enseignantplaning'];
			$param=$_SESSION['enseig'];
			$_SESSION['groupep']=array();

		}else{
			if (!empty($_SESSION['groupep'])) {
				$param=$_SESSION['groupe'];
			}elseif (!empty($_SESSION['enseigp'])) {
				$param=$_SESSION['enseig'];
			}else{
				$param='';
			}

		}

		
		
		
	}

	function isInternetConnected() {
		$url = "http://www.google.com"; // Vous pouvez également utiliser d'autres sites Web fiables

		$headers = @get_headers($url);

		if ($headers && strpos($headers[0], '200 OK') !== false) {
			return true; // L'ordinateur est connecté à Internet
		} else {
			return false; // L'ordinateur n'est pas connecté à Internet
		}
	}

	public function joursEnLettre($date){
		$jours=(new dateTime($date))->format("w");
		if ($jours==0) {
			$joursLettre="dimanche";
		}elseif ($jours==1) {
			$joursLettre="lundi";
		}elseif ($jours==2) {
			$joursLettre="mardi";
		}elseif ($jours==3) {
			$joursLettre="mercredi";
		}elseif ($jours==4) {
			$joursLettre="jeudi";
		}elseif ($jours==5) {
			$joursLettre="vendredi";
		}elseif ($jours==6) {
			$joursLettre="samedi";
		}

		return array(($joursLettre));
	}


	public function deviseformat($value){

		if ($value=='eu') {
			$format='€';
		}elseif ($value=='us') {
			$format='$';
		}elseif ($value=='cfa') {
			$format='CFA';
		}else{

			$format='GNF';

		}
		return($format);
	}

	public function datemin($nombre){
		$dated=date("Y-m-d");
		
		$anneed=(new dateTime($dated))->format("Y");
		$moisd=(new dateTime($dated))->format("m");
		$jourd=(new dateTime($dated))->format("d");

		$datemin = date("Y-m-d", mktime(0, 0, 0, $moisd, $jourd,   $anneed-$nombre));
		$datemax = date("Y-m-d", mktime(0, 0, 0, $moisd, $jourd,   $anneed-$nombre));

		return array($datemin, $datemax);
	}

	public function dateformat($date){
		if (!empty($date)) {
			$dateformat=(new dateTime($date))->format('d/m/Y');
		}else{
			$dateformat="";
		}

		return $dateformat;
	}

	public function nomBanquefecth($banque){

		$prod=$this->DB->querys("SELECT nomb FROM nombanque where id='{$banque}' ");

		return $prod['nomb'];

	}


	
	public function classeInfos($classe, $promo){

		$prod=$this->DB->querys("SELECT *from groupe where nomgr='{$classe}' and promo='{$promo}' ");

		return array($prod['codef'], $prod['nomgr'], $prod['niveau']);
	}

	public function activites($promo){

		$prod=$this->DB->query("SELECT * FROM activites ");

		return $prod;
	}

	public function nomActivites($id){

		$prod=$this->DB->querys("SELECT * FROM activites WHERE id='{$id}' ");

		return array(ucfirst($prod['nomact']), $prod['mensualite']);
	}

	public function colspan($min,$max){

		if(isset($_SESSION['admin']))
			return $max;
		else
			return $min;
	}

	public function formatDate($value){
		return((new DateTime($value))->format("d/m/Y"));
	}


	public function passworddecod($pass){

		$connexion = $this->DB->querys('SELECT * FROM login WHERE pseudo =:Pseudo', array('Pseudo'=>$_SESSION['pseudo']));

		$password=password_verify($pass, $connexion['mdp']);

		return $password;
	}

	public function login($mat){

		$prod=$this->DB->querys("SELECT * FROM login WHERE matricule='{$mat}'");

		return array($prod['type']);
	}

	public function moyennegenmat(){

		$nbre=0;
		$moyenne=0;
			
		$prodnote=$this->DB->query('SELECT  note from note inner join devoir on note.codev=devoir.id inner join inscription on note.matricule= inscription.matricule where nomgroupe=:nom and note.codens=:codens', array('nom'=>'11sm1', 'codens'=>15));

		foreach ($prodnote as $note) {

			$nbre+=count($note->note);
			$moyenne+=$note->note;
			
		}

		return ($moyenne);

		
	}

	public function espace($value){
		return str_replace(' ', '', $value);
	}

	public function mois(){

		if ($_SESSION['mensuellec']==1) {
	        $mois='Janvier';
	    }elseif ($_SESSION['mensuellec']==2) {
	    	$mois='Février';
	  	}elseif ($_SESSION['mensuellec']==3) {
	    	$mois='Mars';
	  	}elseif ($_SESSION['mensuellec']==4) {
	    	$mois='Avril';
	  	}elseif ($_SESSION['mensuellec']==5) {
	    	$mois='Mai';
	  	}elseif ($_SESSION['mensuellec']==6) {
	    	$mois='Juin';
	  	}elseif ($_SESSION['mensuellec']==7) {
	    	$mois='Juillet';
	  	}elseif ($_SESSION['mensuellec']==8) {
	    	$mois='Août';
	  	}elseif ($_SESSION['mensuellec']==9) {
	    	$mois='Septembre';
	  	}elseif ($_SESSION['mensuellec']==10) {
	    	$mois='Octobre';
	  	}elseif ($_SESSION['mensuellec']==11) {
	    		$mois='Novembre';
	  	}elseif ($_SESSION['mensuellec']==12) {
	    	$mois='Décembre';
	  	}

	  	return $mois;
	}

	public function moisbul(){

		if ($_SESSION['mois']==1) {
	        $moisbul='Janvier';
	    }elseif ($_SESSION['mois']==2) {
	    	$moisbul='Fevrier';
	  	}elseif ($_SESSION['mois']==3) {
	    	$moisbul='Mars';
	  	}elseif ($_SESSION['mois']==4) {
	    	$moisbul='Avril';
	  	}elseif ($_SESSION['mois']==5) {
	    	$moisbul='Mai';
	  	}elseif ($_SESSION['mois']==6) {
	    	$moisbul='Juin';
	  	}elseif ($_SESSION['mois']==7) {
	    	$moisbul='Juillet';
	  	}elseif ($_SESSION['mois']==8) {
	    	$moisbul='Août';
	  	}elseif ($_SESSION['mois']==9) {
	    	$moisbul='Septembre';
	  	}elseif ($_SESSION['mois']==10) {
	    	$moisbul='Octobre';
	  	}elseif ($_SESSION['mois']==11) {
	    		$moisbul='Novembre';
	  	}elseif ($_SESSION['mois']==12) {
	    	$moisbul='Decembre';
	  	}else{
	  		$moisbul='Janvier';
	  	}

	  	return $moisbul;
	}

	public function listeClasse(){
		
		$prodgroup=$this->DB->query('SELECT groupe.nomgr as nomgr, codef from groupe where promo=:promo order by(nomgr)', array('promo'=>$_SESSION['promo']));

		return $prodgroup;
	}

	public function cursus(){
		$prodcursus=$this->DB->query('SELECT nom from cursus order by(id)');
		return $prodcursus;
	}


	public function formation(){

		if (isset($_POST['nivcursus'])) {

			$prodformation=$this->DB->query("SELECT classe, nomf, niveau, codef from formation where codef='{$_POST['nivcursus']}' order by(id)");
		}else{
			$prodformation=$this->DB->query('SELECT classe, nomf, niveau, codef from formation order by(id)');
		}
		return $prodformation;
	}

	public function nomClasse($codef){

		$prodformation=$this->DB->querys("SELECT classe, nomf, niveau, codef from formation where codef='{$codef}'");
		if ($prodformation['classe']>1 and $prodformation['classe']<=10) {
			$classe=$prodformation['classe'].'ème Année';
		}elseif ($prodformation['classe']==1) {
			$classe=$prodformation['classe'].'ère Année';
		}elseif ($prodformation['classe']>10 and $prodformation['classe']<=12) {
			$classe=$prodformation['classe'].'ème Année '.$prodformation['nomf'];
		}else{
			$classe=$prodformation['classe'];
		}
		return $classe;
	}

	public function nomClasseById($id){

		$prod=$this->DB->querys("SELECT *from groupe where id='{$id}'");
		return array($prod['nomgr']);
	}

	public function tabGroupe($classe){

		$prodgroupe=$this->DB->querys("SELECT *from groupe inner join formation on formation.codef=groupe.codef where nomgr='{$classe}'");

		return array($prodgroupe['codef'], $prodgroupe['classe']);

	}

	public function classeStat($codef, $promo){
		$prodformation=$this->DB->query("SELECT nomgr, codef from groupe where codef='{$codef}' and promo='{$promo}' order by(id)");
		return $prodformation;
	}

	public function tranche(){
		$prodtranche=$this->DB->query('SELECT nom from tranche where promo=? order by(nom)', array($_SESSION['promo']));
		return $prodtranche;
	}

	public function trancheRapport($promo){
		$prodtranche=$this->DB->query('SELECT nom from tranche where promo=? order by(nom)', array($promo));
		return $prodtranche;
	}

	public function classe(){

		$products = $this->DB->querys('SELECT type, matricule, niveau FROM login WHERE pseudo= :PSEUDO',array('PSEUDO'=>$_SESSION['pseudo']));

		if ($products['type']!='enseignant') {

			if (!empty($_SESSION['niveauf'])) {

	            $prodgroup=$this->DB->query('SELECT groupe.nomgr as nomgr from groupe where promo=:promo and niveau=:niv order by(nomgr)', array('promo'=>$_SESSION['promo'], 'niv'=>$_SESSION['niveauf']));
	            
	        }else{

	            $prodgroup=$this->DB->query('SELECT groupe.nomgr as nomgr from groupe where promo=:promo order by(nomgr)', array('promo'=>$_SESSION['promo']));
	        }
	    }else{

	    	$prodgroup=$this->DB->query('SELECT groupe.nomgr as nomgr from groupe inner join enseignement on groupe.nomgr=enseignement.nomgr where enseignement.codens=:code and groupe.promo=:promo', array('code'=>$products['matricule'], 'promo'=>$_SESSION['promo']));

	    }

        return $prodgroup;
	}

	public function nomCategorie($nom){
		$nomclient = $this->DB->querys("SELECT id, nom FROM categoriedep where id='{$nom}'");

		return ucwords($nomclient['nom']);
	}

	public function listeCategorie(){
		$nomclient = $this->DB->query("SELECT *FROM categoriedep ");

		return $nomclient;
	}


	public function nomCategorieVers($nom){
		$nomclient = $this->DB->querys("SELECT id, nom FROM categorievers where id='{$nom}'");

		return ucwords($nomclient['nom']);
	}

	public function listeCategorieVers(){
		$nomclient = $this->DB->query("SELECT *FROM categorievers ");

		return $nomclient;
	}

	public function nomCollaborateur($nom){
		$nomclient = $this->DB->querys("SELECT id, nom, contact FROM collaborateur where id='{$nom}'");

		return array(ucwords($nomclient['nom']), $nomclient['contact']);
	}

	public function listeCollaborateur(){
		$nomclient = $this->DB->query("SELECT *FROM collaborateur ");

		return $nomclient;
	}


	public function enseignant(){
		$products = $this->DB->querys('SELECT type, matricule, niveau FROM login WHERE pseudo= :PSEUDO',array('PSEUDO'=>$_SESSION['pseudo']));

		if ($products['type']!='enseignant') {

			if (!empty($_SESSION['niveauf'])) {

	            $prodens=$this->DB->query('SELECT *from enseignant order by(prenomen)');
	            
	        }else{

	            $prodens=$this->DB->query('SELECT *from enseignant order by(prenomen)');
	        }
	    }else{

	    	$prodens=$this->DB->query('SELECT *from enseignant where matricule=:code', array('code'=>$products['matricule']));

	    }

        return $prodens;
	}

	public function listeEnseignant(){

    $prodens=$this->DB->query("SELECT matricule, nomen, prenomen from enseignant ");

    return $prodens;
	}


	public function nomEnseignant($param){

    $prodensnom=$this->DB->querys("SELECT nomen, prenomen from enseignant where matricule='{$param}'");

    return ucwords($prodensnom['prenomen']).' '.strtoupper($prodensnom['nomen']);
	}

	public function findEleveByMat($matricule){

	    $eleve=$this->DB->querys("SELECT *from eleve inner join inscription on eleve.matricule=inscription.matricule left join contact on contact.matricule=inscription.matricule where inscription.matricule='{$matricule}' and annee='{$_SESSION['promo']}' ");

	    return $eleve;
	}

	public function findElevePresent($date_acces,$journee){

	    $eleve=$this->DB->query("SELECT accesite.id as id, mat_acces as matricule, nomgr, date_acces, journee from accesite inner join inscription on mat_acces=inscription.matricule where date_format(date_acces,\"%Y%m%d \")='{$date_acces}' and journee='{$journee}' and promo='{$_SESSION['promo']}' and annee='{$_SESSION['promo']}' order by(id) desc limit 30 ");

	    return $eleve;
	}

	public function findEleveAbsent($date_acces,$journee){

	    $absent=$this->DB->query("SELECT *from inscription where annee='{$_SESSION['promo']}' and matricule not in(SELECT mat_acces FROM accesite where date_format(date_acces,\"%Y%m%d \")='{$date_acces}' and journee='{$journee}' and promo='{$_SESSION['promo']}' ) ");

	    return $absent;
	}

	public function findPersonnelByMat($matricule){
		$searchType=$this->DB->querys("SELECT typepers FROM personnelsgen where matpers='{$matricule}' and promopers='{$_SESSION['promo']}'");
		if ($searchType['typepers']=="personnel") {
			$personnel=$this->DB->querys("SELECT numpers as matricule, nom, prenom, email, phone, datenaiss as naissance from personnel left join contact on numpers=matricule where numpers='{$matricule}' ");
		}else{
			$personnel=$this->DB->querys("SELECT enseignant.matricule as matricule, nomen as nom, prenomen as prenom, email, phone, naissance from enseignant left join contact on enseignant.matricule=contact.matricule where enseignant.matricule='{$matricule}' ");
		}
	    return $personnel;
	}

	public function findPersonnelPresent($date_acces,$journee){

	    $eleve=$this->DB->query("SELECT accesitepersonnel.id as id, mat_acces as matricule, typepers, date_acces, journee from accesitepersonnel inner join personnelsgen on mat_acces=matpers where date_format(date_acces,\"%Y%m%d \")='{$date_acces}' and journee='{$journee}' and promo='{$_SESSION['promo']}' and promopers='{$_SESSION['promo']}' order by(id) desc limit 30 ");

	    return $eleve;
	}

	
	public function findPersonnelAbsent($date_acces,$journee){
		
		$absent=$this->DB->query("SELECT *from inscription where annee='{$_SESSION['promo']}' and matricule not in(SELECT mat_acces FROM accesitepersonnel where date_format(date_acces,\"%Y%m%d \")='{$date_acces}' and journee='{$journee}' and promo='{$_SESSION['promo']}' ) ");
		
	    return $absent;
	}
	
	public function findVisiteurPresent($date_acces){

		$eleve=$this->DB->query("SELECT *from accesitevisiteur where date_format(date_acces,\"%Y%m%d \")='{$date_acces}' and promo='{$_SESSION['promo']}' order by(etat) limit 30 ");

		return $eleve;
	}
	
	public function listeEleve(){

	    $prodens=$this->DB->query("SELECT *from eleve inner join inscription on eleve.matricule=inscription.matricule where annee='{$_SESSION['promo']}' ");

	    return $prodens;
	}

	public function infoFormation($param){

		$prod=$this->DB->querys("SELECT *from formation where codef='{$param}'");
	
		return array($prod['nomf']);
	}

	public function nomEleve($param){

		$prodensnom=$this->DB->querys("SELECT nomel, prenomel from eleve where matricule='{$param}'");

		if (empty($prodensnom['nomel'])) {
			return "Selectionnez un élève";
		}else{
			return ucwords($prodensnom['prenomel']).' '.strtoupper($prodensnom['nomel']);
		}
	}

	public function infoEleve($param){

		$prodensnom=$this->DB->querys("SELECT nomel, prenomel, codef from eleve inner join inscription on inscription.matricule=eleve.matricule where eleve.matricule='{$param}'");
	
		return array(ucwords($prodensnom['prenomel']), strtoupper($prodensnom['nomel']), $this->infoFormation($prodensnom['codef'])[0]);
	}

	public function nomElevex($param){

	    $prodensnom=$this->DB->querys("SELECT nom as nomel, prenom as prenomel from elevexterne where matex='{$param}'");

	    if (empty($prodensnom['nomel'])) {
	    	return "";
	    }else{
	    	return ucwords($prodensnom['prenomel']).' '.strtoupper($prodensnom['nomel']);
	    }

    
	}

	public function infosEleve($param){

	    $prod=$this->DB->querys("SELECT *from eleve where matricule='{$param}'");

	    return array((ucwords($prod['prenomel']).' '.strtoupper($prod['nomel'])));
	}

	public function nomPersonnel($param){

		$prodensnom=$this->DB->querys("SELECT nom, prenom from personnel where numpers='{$param}'");

		if (empty($prodensnom['nom'])) {
			return 'Admin';
		}else{

			return ucwords(strtolower($prodensnom['prenom'])).' '.strtoupper($prodensnom['nom']);
		}
	}

	public function listePersonnel(){

    $prodpers=$this->DB->query("SELECT numpers, nom, prenom from personnel order by(prenom)");

    return $prodpers;
	}


	public function nomMatiere($param){

        $prodensnom=$this->DB->querys("SELECT nommat from matiere where codem='{$param}'");

        return ucwords($prodensnom['nommat']);
	}

	public function nomMatiereCodef($param){

    $prodmat=$this->DB->query("SELECT nommat, codef, codem from matiere where codef='{$param}'");

    return $prodmat;
	}



	//Fonction pour cloturer les semestres ou les inscriptions

	public function cloture(){

		$cloture="";

		if (isset($_GET['ajoute']) or isset($_GET['inscript']) or isset($_POST['semestren'])) {

			$values= $this->DB->querys('SELECT nomcloture FROM cloture where nomcloture=:nom and promo=:promo', array('nom'=>$_SESSION['nomcloture'], 'promo'=>$_SESSION['promo']));

			if (!empty($values)) {

				$_SESSION['cloture']='cloturer';

				$cloture=$_SESSION['cloture'];

			}else{

				$_SESSION['cloture']='en-cours';
				$cloture=$_SESSION['cloture'];

			}

			return $cloture;
		}

	}

	
	public function month(){

		if (isset($_GET['moisp'])) {
			$moisp=$_GET['moisp'];
		}else{
			$moisp=date('m');
		}

		$month=$moisp;
		return $month;
	}

	public function year(){

		if (isset($_GET['year'])) {
			$annee=$_GET['year'];
		}else{
			$annee=date('Y');
		}
		return $annee;
	}
	

	public function getStartingDay(){

		if (isset($_GET['moisp'])) {
			$periode=date('Y').'-'.$_GET['moisp'].'-'.'01';
		}else{
			$periode=date('Y-m').'-'.'01';
		}
		return new DateTime($periode);
	}

	public function getWeeks(){
		//$start=new DateTime('2021-02-01');
		//var_dump($start);
		$start=$this->getStartingDay();
		$end=(clone $start)->modify('+1 month -1 day');
		$weeks=intval($end->format('W'))-intval($start->format('W'))+1;
		if ($weeks<0) {
			$weeks=intval($end->format('W'))+1;
		}

		return $weeks;

	}

	public function withinMonth($date):bool{// est ce que le jour est dans le mois en-cours
		return $this->getStartingDay()->format('Y-m')===$date->format('Y-m');
	}

	public function semaine(){

		if (isset($_GET['semainep'])) {
			$moisp=$_GET['semainep'];
		}else{
			$moisp=date('W');
		}

		$month=$moisp;
		return $month;
	}

	public function nexSemaine(){

		$semaine=$this->semaine()+1;

		return ($semaine);
	}

	public function previousSemaine(){

		$semaine=$this->semaine()-1;

		return ($semaine);
	}

	public function nexMonth(){

		$month=$this->month()+1;
		$year=$this->year();

		if ($month>12) {
			$month=1;
			$year+=1;
		}

		return array($month,$year);
	}

	public function previousMonth(){

		$month=$this->month()-1;
		$year=$this->year();

		if ($month<1) {
			$month=12;
			$year-=1;
		}

		return array($month,$year);
	}

	public function print(){
		if (empty($_SESSION['groupep'])) {

			$print='enseignant';

		}else{

			$print='classe';
		}

		return $print;
	}

	public function getEventsBetween($firsday, $end, $param){//Recupere les evenements entre deux dates 


		if ($this->print()=='enseignant') {

			$prodevents=$this->DB->query("SELECT events.id as id, nommat, codem, nomgrp, nomen, prenomen, codensp, name, debut, fin, lieu FROM events inner join matiere on codemp=codem inner join enseignant on matricule=codensp WHERE debut>=:debut and fin<=:fin and codensp=:code order by(debut)", array('debut'=>$firsday->format('Y-m-d 00:00:00'), 'fin'=>$end->format('Y-m-d 23:59:59'), 'code'=>$param));
			

		}elseif ($this->print()=='classe') {

			$prodevents=$this->DB->query("SELECT events.id as id, nommat, codem, nomgrp, nomen, prenomen, codensp, name, debut, fin, lieu FROM events inner join matiere on codemp=codem inner join enseignant on matricule=codensp WHERE debut>=:debut and fin<=:fin and nomgrp=:code order by(debut)", array('debut'=>$firsday->format('Y-m-d 00:00:00'), 'fin'=>$end->format('Y-m-d 23:59:59'), 'code'=>$param));
		}else{
			$prodevents=array();
		}

		return $prodevents;
	}

	public function getEventsBetweenByDay($firsday, $end, $param):array{//Recupere les evenements entre deux dates mais en mettant les dates comme cles 

		$events=$this->getEventsBetween($firsday, $end, $param);

		$days=[];
		foreach ($events as $event) {

			$date=explode(' ', $event->debut)[0];

			if (!isset($days[$date])) {
				$days[$date]=[$event];
			}else{
				$days[$date][]=$event;
			}
		}

		return $days;
	}

	public function find(int $id):array{// Permet de recuperer un evenement

		$prodevent=$this->DB->querys("SELECT *FROM events WHERE id='{$id}' LIMIT 1");

		return $prodevent;

	}

	public function updateEvent(int $id){//Permet de modifier un evenement

		return $this->DB->insert("UPDATE events SET name=?, debut=?, fin=?, description=? WHERE id='{$id}'");
	}

	public function etablissement(){// Permet de recuperer un evenement

		$prod=$this->DB->querys("SELECT nom FROM etablissement");

		return $prod['nom'];

	}

	public function etablissementGen(){// Permet de recuperer un evenement

		$prod=$this->DB->querys("SELECT * FROM etablissement");

		return array($prod['init'], $prod['nom']);

	}

	public function nomBanqueTicket(){// Permet de recuperer un evenement

		$prod=$this->DB->query("SELECT nomb, id, numero FROM nombanque where id >1");

		return $prod;

	}

	public function nomBanque(){// Permet de recuperer un evenement

		$prod=$this->DB->query("SELECT nomb, id, numero FROM nombanque");

		return $prod;

	}

	public function nomBanquen(int $id){

		$prod=$this->DB->querys("SELECT nomb FROM nombanque WHERE id='{$id}'");

		return $prod['nomb']; 
	}

	public function montantCompte($banque){

		$prod=$this->DB->querys("SELECT sum(montant) as montant FROM banque where id_banque='{$banque}' and DATE_FORMAT(date_versement, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_versement, \"%Y%m%d\") <= '{$_SESSION['date2']}' and promob='{$_SESSION['promo']}' ");

		return $prod['montant'];

	}

	public function montantCompteT($banque){

		$prod=$this->DB->querys("SELECT sum(montant) as montant FROM banque ");

		return $prod['montant'];

	}


	public function effectifTotal($promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where etatscol='{$etatscol}' and annee='{$promo}'");

		return $prod['nbre'];

	}

	public function effectifTotCursus($cursus, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where etatscol='{$etatscol}' and niveau='{$cursus}' and annee='{$promo}'");

		return $prod['nbre'];

	}

	public function effectifTotForm($codef, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where etatscol='{$etatscol}' and codef='{$codef}' and annee='{$promo}'");

		return $prod['nbre'];

	}

	public function effectifTotClass($codef, $classe, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where etatscol='{$etatscol}' and codef='{$codef}' and nomgr='{$classe}' and annee='{$promo}'");

		return $prod['nbre'];

	}

	public function percentEff($promo){

		$sexe='m';
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(inscription.id) as nbre from inscription inner join eleve on eleve.matricule=inscription.matricule where etatscol='{$etatscol}' and sexe='{$sexe}' and annee='{$promo}'");

		if (empty($this->effectifTotal($promo))) {
			$effecT=1;
		}else{
			$effecT=$this->effectifTotal($promo);
		}

		$percentg=($prod['nbre']/($effecT))*100;

		$percentf=(100-$percentg);

		return array($percentg, $percentf);

	}


	public function percentEffCursus($cursus, $promo){

		$sexe='m';
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT  count(inscription.id) as nbre from inscription inner join eleve on eleve.matricule=inscription.matricule where etatscol='{$etatscol}' and inscription.niveau='{$cursus}' and sexe='{$sexe}' and annee='{$promo}'");

		if (empty($this->effectifTotCursus($cursus,$promo))) {
			$effecT=1;
		}else{
			$effecT=$this->effectifTotCursus($cursus,$promo);
		}

		$percentg=($prod['nbre']/($effecT))*100;

		$percentf=(100-$percentg);

		return array($percentg, $percentf);

	}


	public function percentEffForm($codef, $promo){

		$sexe='m';
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT  count(inscription.id) as nbre from inscription inner join eleve on eleve.matricule=inscription.matricule where etatscol='{$etatscol}' and inscription.codef='{$codef}' and sexe='{$sexe}' and annee='{$promo}'");

		if (empty($this->effectifTotForm($codef,$promo))) {
			$effecT=1;
		}else{
			$effecT=$this->effectifTotForm($codef,$promo);
		}

		$percentg=($prod['nbre']/($effecT))*100;

		$percentf=(100-$percentg);

		return array($percentg, $percentf);

	}

	public function percentEffClass($codef, $classe, $promo){

		$sexe='m';
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT  count(inscription.id) as nbre from inscription inner join eleve on eleve.matricule=inscription.matricule where etatscol='{$etatscol}' and inscription.codef='{$codef}' and nomgr='{$classe}' and sexe='{$sexe}' and annee='{$promo}'");

		if (empty($this->effectifTotClass($codef, $classe, $promo))) {
			$effecT=1;
		}else{
			$effecT=$this->effectifTotClass($codef, $classe, $promo);
		}

		$percentg=($prod['nbre']/($effecT))*100;

		$percentf=(100-$percentg);

		return array($percentg, $percentf);

	}

	public function effectifSexeClasse($sexe, $codef, $classe, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(inscription.id) as nbre from inscription inner join eleve on eleve.matricule=inscription.matricule where etatscol='{$etatscol}' and inscription.codef='{$codef}' and nomgr='{$classe}' and sexe='{$sexe}' and annee='{$promo}'");

		return array($prod['nbre']);

	}

	public function effectifInscritClasse($etat, $codef, $classe, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(inscription.id) as nbre from inscription where etatscol='{$etatscol}' and inscription.codef='{$codef}' and nomgr='{$classe}' and etat='{$etat}' and annee='{$promo}'");

		return array($prod['nbre']);

	}

	public function effectifStatutClasse($statut, $codef, $classe, $promo){
		$etatscol='actif';

		$prod=$this->DB->querys("SELECT count(inscription.id) as nbre from inscription where etatscol='{$etatscol}' and inscription.codef='{$codef}' and nomgr='{$classe}' and statut!='{$statut}' and annee='{$promo}'");

		return array($prod['nbre']);

	}

	function h($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);
        return $donnees;
    }


	

	public function resteTranche($mat, $tranche, $promo, $codef){

        $prodtott=$this->DB->querys("SELECT sum(montant) as montant FROM histopayefrais WHERE matricule='{$mat}' and tranche='{$tranche}' and promo='{$promo}'");

        $prodscol=$this->DB->querys("SELECT sum(montant) as montant from scolarite WHERE codef='{$codef}' and tranche='{$tranche}' and promo='{$promo}'");

        $restetranche=$prodscol['montant']-$prodtott['montant'];

        return $restetranche;
	}

	public function totPaye($mat, $promo){
		$prodtot=$this->DB->querys("SELECT sum(montant) as montant FROM histopayefrais WHERE matricule='{$mat}' and promo='{$promo}'");

        $prodins=$this->DB->querys("SELECT sum(montant) as montant from payement WHERE matricule='{$mat}' and promo='{$promo}'");

        $totpaye=$prodtot['montant']+$prodins['montant'];

        return $totpaye;
	}


	public function fraisIns($mat, $promo){

    $prodins=$this->DB->querys("SELECT montant, datepaye from payement WHERE matricule='{$mat}' and promo='{$promo}'");

    $totpaye=$prodins['montant'];

    return array($prodins['montant'], $prodins['datepaye']);
	}

	public function resteAnnuel($mat, $promo, $codef){

		$prodscola=$this->DB->querys("SELECT sum(montant) as montant from scolarite WHERE codef='{$codef}' and promo='{$promo}'");

		$prodrem = $this->DB->querys("SELECT remise FROM inscription WHERE matricule='{$mat}' and annee='{$promo}'");

    $prodins=$this->DB->querys("SELECT montant, datepaye from payement WHERE matricule='{$mat}' and promo='{$promo}'");

    $prodtot=$this->DB->querys("SELECT sum(montant) as montant FROM histopayefrais WHERE matricule='{$mat}' and promo='{$promo}'");

    $diff=$prodscola['montant']*(1-($prodrem['remise']/100))-$prodtot['montant'];

    return $diff;
	}


	public function montPaye($param, $promo, $codef){// à terminer
		$products=$this->DB->query('SELECT histopayefrais.matricule as matricule, montant,tranche, nomel, prenomel, nomgr, codef FROM histopayefrais inner join eleve on eleve.matricule=histopayefrais.matricule inner join inscription on inscription.matricule=histopayefrais.matricule WHERE histopayefrais.famille= ? and annee=? order by(nomgr)', array($_GET['numfac'], $_SESSION['promo']));

        $prodins=$this->DB->querys("SELECT sum(montant) as montant from payement WHERE matricule='{$param}' and promo='{$promo}'");

        if ($_GET['tranche']=='1ere tranche') {

          $montpaye=$product->montant+$prodins['montant'];
        }else{
          $montpaye=$product->montant;
        }

        return $montpaye;
	}





	#**************GESTION DE LA LICENCE*****************************

	public function licence(){
		
		$licence="";

		$prodli= $this->DB->querys('SELECT num_licence, DATE_FORMAT(date_souscription, \'%d/%m/%Y\') AS debut, DATE_FORMAT(date_fin, \'%d/%m/%Y\') AS datefin, date_fin AS fin FROM licence');

       	$now = date('Y-m-d');
       	$datefin = $prodli['fin'];

       	$now = new DateTime( $now );
       	$now = $now->format('Ymd');
       	$datefin = new DateTime( $datefin );
       	$datefin = $datefin->format('Ymd');

       	if ($now >= $datefin) {

       		$licence="expiree";

       	}else{

       		$licence="ok";
       	}

		return $licence;			
	}

	public function licencea(){
		
		$licencea="";

		$prodlia = $this->DB->querys('SELECT num_licence, DATE_FORMAT(date_souscription, \'%d/%m/%Y\') AS debut, DATE_FORMAT(date_fin, \'%Y%m\') AS datefin FROM licence');

       	$nowa = date('Ym');
       	$datefina = $prodlia['datefin'];
       	if ($nowa-$datefina>=-2) {
       		$licencea=$nowa-$datefina;
       	}else{
       		$licencea=$nowa-$datefina;	       		
       	}
		return $licencea;			
	}

	public function dettes($banque, $devise){
		$catdette="dette";
		$catdettep="paiementdette";
		$proddette=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where comptedep='{$banque}' and devise='{$devise}' and categorie='{$catdette}' ");
		$proddettep=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where comptedep='{$banque}' and devise='{$devise}' and categorie='{$catdettep}' ");
		$dette=$proddette['montant'];
		$dettep=$proddettep['montant'];
		$dettes=$dette-$dettep;

		return $dettes;
	}

	public function creances($banque, $devise){
		$catcreance="creance";
		$catcreancep="paiementcreance";
		$prodcreance=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where comptedep='{$banque}' and devise='{$devise}' and categorie='{$catcreance}' ");
		$prodcreancep=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where comptedep='{$banque}' and devise='{$devise}' and categorie='{$catcreancep}' ");
		$creance=$prodcreance['montant'];
		$creancep=$prodcreancep['montant'];
		$creances=$creance-$creancep;

		return $creances;
	}

	public function cumulDettes($devise){
		$catdette="dette";
		$catdettep="paiementdette";
		$proddette=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where devise='{$devise}' and categorie='{$catdette}' ");
		$proddettep=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where devise='{$devise}' and categorie='{$catdettep}' ");
		$dette=$proddette['montant'];
		$dettep=$proddettep['montant'];
		$dettes=$dette-$dettep;

		return $dettes;
	}

	public function cumulCreances($devise){
		$catcreance="creance";
		$catcreancep="paiementcreance";
		$prodcreance=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where devise='{$devise}' and categorie='{$catcreance}' ");
		$prodcreancep=$this->DB->querys("SELECT sum(montant) as montant FROM dettesCreances where devise='{$devise}' and categorie='{$catcreancep}' ");
		$creance=$prodcreance['montant'];
		$creancep=$prodcreancep['montant'];
		$creances=$creance-$creancep;

		return $creances;
	}

	public function caisse($banque, $devise){
		// frais d'inscription
		$prodins =$this->DB->querys("SELECT sum(montant/taux) as montant FROM payement where caisse='{$banque}' and devise='{$devise}' ");
		$prodscol =$this->DB->querys("SELECT sum(montant/taux) as montant FROM histopayefrais where caisse='{$banque}' and devise='{$devise}' ");
		$prodact =$this->DB->querys("SELECT sum(montantp/taux) as montant FROM activitespaiehistorique where caisse='{$banque}' and devise='{$devise}' ");
		$prodvers =$this->DB->querys("SELECT sum(montant/taux) as montant FROM versement where comptedep='{$banque}' and devisevers='{$devise}' ");
		if ($devise=='gnf') {
			$prodbu =$this->DB->querys("SELECT sum(totalp) as montant FROM payelivre where caisse='{$banque}' ");
			$prodec =$this->DB->querys("SELECT sum(montant) as montant FROM decaissement where caisse='{$banque}' ");
			$prodens =$this->DB->querys("SELECT sum(montant) as montant FROM payenseignant where caisse='{$banque}' ");
			$prodpers =$this->DB->querys("SELECT sum(montant) as montant FROM payepersonnel where caisse='{$banque}' ");
			$prodacc =$this->DB->querys("SELECT sum(montant) as montant FROM accompte where caisse='{$banque}' ");
			$montantbu=$prodbu['montant'];
			$montantdec=$prodec['montant'];
			$montantens=$prodens['montant'];
			$montantpers=$prodpers['montant'];
			$montantacc=$prodacc['montant'];
		}else{
			$montantbu=0;
			$montantdec=0;
			$montantens=0;
			$montantpers=0;
			$montantacc=0;
		}

		$montantins=($this->dettes($banque, $devise)+$prodins['montant']+$prodscol['montant']+$prodact['montant']+$prodvers['montant']+$montantbu)-($montantdec+$montantens+$montantpers+$montantacc+$this->creances($banque, $devise));

		return array($montantins);
	}

	public function cumulCaisse($devise){
		// frais d'inscription
		$prodins =$this->DB->querys("SELECT sum(montant/taux) as montant FROM payement where devise='{$devise}' ");
		$prodscol =$this->DB->querys("SELECT sum(montant/taux) as montant FROM histopayefrais where devise='{$devise}' ");
		$prodact =$this->DB->querys("SELECT sum(montantp/taux) as montant FROM activitespaiehistorique where devise='{$devise}' ");
		$prodvers =$this->DB->querys("SELECT sum(montant/taux) as montant FROM versement where devisevers='{$devise}' ");
		if ($devise=='gnf') {
			$prodbu =$this->DB->querys("SELECT sum(totalp) as montant FROM payelivre ");
			$prodec =$this->DB->querys("SELECT sum(montant) as montant FROM decaissement ");
			$prodens =$this->DB->querys("SELECT sum(montant) as montant FROM payenseignant ");
			$prodpers =$this->DB->querys("SELECT sum(montant) as montant FROM payepersonnel ");
			$prodacc =$this->DB->querys("SELECT sum(montant) as montant FROM accompte ");
			$montantbu=$prodbu['montant'];
			$montantdec=$prodec['montant'];
			$montantens=$prodens['montant'];
			$montantpers=$prodpers['montant'];
			$montantacc=$prodacc['montant'];
		}else{
			$montantbu=0;
			$montantdec=0;
			$montantens=0;
			$montantpers=0;
			$montantacc=0;
		}

		$montantins=($this->cumulDettes($devise)+$prodins['montant']+$prodscol['montant']+$prodact['montant']+$prodvers['montant']+$montantbu)-($montantdec+$montantens+$montantpers+$montantacc+$this->cumulCreances($devise));

		return $montantins;
	}
}