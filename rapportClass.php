<?php 
class Rapport
{
	private $DB;
	

	public $fraisins = array(            
	    'inscreche'   => 2000000,
	    'reinscreche'   => 1500000,
	    'insmaternelle'   => 2000000,
	    'reinsmaternelle'   => 1500000,
	    'insprimaire'   => 2000000,
	    'reinsprimaire'   => 1500000,
	    'inscollege'   => 2000000,
	    'reinscollege'   => 1500000,
	    'inslycee'   => 2000000,
	    'reinslycee'   => 1500000,
	    'ins' =>2000000,
	    'reins'=>1500000,
	);
	public $thoraire=65000;

	public $init=['gsb','cspe','cspp'];

	
	function __construct($DB)
	{
		$this->DB = $DB;
	}

	public function fraisInscription($cursus, $promo){
		$prod=$this->DB->querys("SELECT inscription.niveau as niveau, inscription.etat as etat FROM inscription  where niveau='{$cursus}' and annee='{$promo}'");

		$etat=$prod['etat'];

		if ($etat=='inscription' and $prod['niveau']=='creche') {
			$fraisins=$this->fraisins['inscreche'];
		}elseif ($etat=='inscription' and $prod['niveau']=='maternelle') {
			$fraisins=$this->fraisins['insmaternelle'];
		}elseif ($etat=='inscription' and $prod['niveau']=='primaire') {
			$fraisins=$this->fraisins['insprimaire'];
		}elseif ($etat=='inscription' and $prod['niveau']=='college') {
			$fraisins=$this->fraisins['inscollege'];
		}elseif ($etat=='inscription' and $prod['niveau']=='lycee') {
			$fraisins=$this->fraisins['inslycee'];
		}else{
			$fraisins=$this->fraisins['ins'];			
		}

		if ($prod['niveau']=='creche') {
			$fraisreins=$this->fraisins['reinscreche'];
		}elseif ($prod['niveau']=='maternelle') {
			$fraisreins=$this->fraisins['reinsmaternelle'];
		}elseif ($prod['niveau']=='primaire') {
			$fraisreins=$this->fraisins['reinsprimaire'];
		}elseif ($prod['niveau']=='college') {
			$fraisreins=$this->fraisins['reinscollege'];
		}elseif ($prod['niveau']=='lycee') {
			$fraisreins=$this->fraisins['reinslycee'];
		}else{
			$fraisreins=$this->fraisins['reins'];
		}


		return array($fraisins, $fraisreins);
	}


	public function bilanInscription($cursus, $etat, $promo){

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where niveau='{$cursus}' and etat='{$etat}' and annee='{$promo}'");

		return $prod['nbre'];

	}

	public function bilanEffectifRemise($cursus, $etat, $promo){
		$remise=0;

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription inner join payement on payement.matricule=inscription.matricule where niveau='{$cursus}' and etat='{$etat}' and remise='{$remise}' and annee='{$promo}' and promo='{$promo}'");

		return $prod['nbre'];

	}


	public function bilanInscriptionApayer($cursus, $etat, $promo){

		$remise=0;

		$prodsansremise=$this->DB->querys("SELECT count(payement.id) as nbre FROM inscription inner join payement on payement.matricule=inscription.matricule where niveau='{$cursus}' and motif='{$etat}' and payement.remise='{$remise}' and annee='{$promo}' and promo='{$promo}'");

		$remiseins=0;

		$prodremiseins = $this->DB->query("SELECT  montant, payement.remise as remise FROM payement inner join inscription on inscription.matricule=payement.matricule WHERE  niveau='{$cursus}' and motif='{$etat}' and payement.remise!='{$remiseins}' and annee='{$promo}' and promo='{$promo}'");

		$totremise=0;

		if ($etat=='inscription') {
			$fraisinsrein=$this->fraisInscription($cursus, $promo)[0];
		}else{
			$fraisinsrein=$this->fraisInscription($cursus, $promo)[1];
		}
		

		foreach ($prodremiseins as $valueremise) {

			if ($valueremise->remise==100) {
				$totremise+=0;
			}else{

				$totremise+=$fraisinsrein*(1-$valueremise->remise/100);

			}

			
		}



		

		$prod=$this->DB->querys("SELECT sum(montant) as montant, inscription.niveau as niveau FROM payement inner join inscription on payement.matricule=inscription.matricule where niveau='{$cursus}' and motif='{$etat}' and annee='{$promo}' and promo='{$promo}'");

		if ($etat=='inscription' and $prod['niveau']=='creche') {
			$fraisins=$this->fraisins['inscreche'];
		}elseif ($etat=='inscription' and $prod['niveau']=='maternelle') {
			$fraisins=$this->fraisins['insmaternelle'];
		}elseif ($etat=='inscription' and $prod['niveau']=='primaire') {
			$fraisins=$this->fraisins['insprimaire'];
		}elseif ($etat=='inscription' and $prod['niveau']=='college') {
			$fraisins=$this->fraisins['inscollege'];
		}elseif ($etat=='inscription' and $prod['niveau']=='lycee') {
			$fraisins=$this->fraisins['inslycee'];
		}else{
			if ($prod['niveau']=='creche') {
				$fraisins=$this->fraisins['reinscreche'];
			}elseif ($prod['niveau']=='maternelle') {
				$fraisins=$this->fraisins['reinsmaternelle'];
			}elseif ($prod['niveau']=='primaire') {
				$fraisins=$this->fraisins['reinsprimaire'];
			}elseif ($prod['niveau']=='college') {
				$fraisins=$this->fraisins['reinscollege'];
			}elseif ($prod['niveau']=='lycee') {
				$fraisins=$this->fraisins['reinslycee'];
			}else{
				$fraisins=$this->fraisins['reins'];
			}
		}

		$apayer=$prodsansremise['nbre']*$fraisins+$totremise;

		$resteapayer=$apayer-$prod['montant'];

		if ($apayer==0) {
			$taux=0;
		}else{

			$taux=($prod['montant']/$apayer)*100;

		}

		return array($resteapayer, $prod['montant'], $taux);

	}

	public function bilanFormation(){

		$prodformation=$this->DB->query('SELECT classe, nomf, niveau, codef from formation');

		return $prodformation;
	}


	public function bilanFraiscol($cursus, $tranche, $promo){


		$cumulmontant=0;
		$cumulmontantrem=0;
		foreach ($this->bilanFormation() as $valuef) {
			
			$prodmontantscol = $this->DB->querys("SELECT  sum(scolarite.montant) as montant FROM scolarite  WHERE codef='{$valuef->codef}' and promo='{$promo}'");

			$prodinscrit=$this->DB->query("SELECT remise FROM inscription  WHERE codef='{$valuef->codef}' and niveau='{$cursus}'  and annee='{$promo}'");

			foreach ($prodinscrit as $valueins) {

				if ($valueins->remise==0) {
					$cumulmontant+=$prodmontantscol['montant'];
				}else{
				
					$cumulmontant+=$prodmontantscol['montant']*(1-$valueins->remise/100);
				}
			}
		}
		

		$prodpayer=$this->DB->querys("SELECT sum(montant) as montant FROM payementfraiscol inner join inscription on payementfraiscol.matricule=inscription.matricule where niveau='{$cursus}' and annee='{$promo}' and promo='{$promo}'");

		if ($cumulmontant==0) {
			$taux=0;
		}else{

			$taux=($prodpayer['montant']/$cumulmontant)*100;

		}
		
		$reste=$cumulmontant-$prodpayer['montant'];

		return array($reste, $prodpayer['montant'], $taux);

	}


	public function nbrePersonnel(){

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM personnel ");

		return $prod['nbre'];

	}

	public function salairePrevPersonnel($promo){

		$prod=$this->DB->querys("SELECT sum(salaire) as salaire FROM salairepers where promo='{$promo}' and numpers not in(SELECT matricule from liaisonenseigpers where promo='{$promo}') ");

		$prodprime=$this->DB->querys("SELECT sum(montantp) as prime FROM primepers where promop='{$promo}' ");

		$salaireprev=($prod['salaire']+$prodprime['prime'])*12;

		return $salaireprev;

	}

	public function salairePayePersonnel($promo){

		$prod=$this->DB->querys("SELECT sum(montant) as salaire FROM payepersonnel where promo='{$promo}'");

		$prodavance=$this->DB->querys("SELECT sum(montant) as salaire FROM accompte inner join personnel on numpers=accompte.matricule where anneescolaire='{$promo}' ");

		$prodprime=$this->DB->querys("SELECT sum(montantp) as salaire FROM primepers where promop='{$promo}' ");

		$salairepaye=$prod['salaire']+$prodavance['salaire']+$prodprime['salaire'];// a revoir
		$salairepaye=$prod['salaire'];

		return $salairepaye;

	}


	public function nbreEnseignant(){

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM enseignant");

		return $prod['nbre'];

	}

	public function salairePrevEnseignant($promo){
		$moisref=11;

		$prod=$this->DB->querys("SELECT sum(montant) as salaire FROM payenseignant where mois='{$moisref}' and anneescolaire='{$promo}' ");

		$prodpers=$this->DB->querys("SELECT sum(salaire) as salaire FROM salairepers where promo='{$promo}' and numpers in(SELECT matricule from liaisonenseigpers where promo='{$promo}') ");

		$montantpersenseig=$prodpers['salaire'];

		$prodprime=$this->DB->querys("SELECT sum(montantp) as prime FROM prime where promop='{$promo}'");

		$salaireprev=($prod['salaire']+$prodprime['prime'])*10.5;

		return $salaireprev;

	}

	public function salairePayeEnseignant($promo){

		$prod=$this->DB->querys("SELECT sum(montant) as salaire FROM payenseignant where anneescolaire='{$promo}' ");

		$prodavance=$this->DB->querys("SELECT sum(montant) as salaire FROM accompte inner join enseignant on enseignant.matricule=accompte.matricule where anneescolaire='{$promo}' ");

		$prodprime=$this->DB->querys("SELECT sum(montantp) as salaire FROM prime where promop='{$promo}' ");

		//$salairepaye=$prod['salaire']+$prodavance['salaire']+$prodprime['salaire']; a revoir
		$salairepaye=$prod['salaire'];

		return $salairepaye;

	}


	public function bilanDepense($promo){
		$moisref=11;

		$prod=$this->DB->querys("SELECT count(id) as nbre, sum(montant) as salaire FROM decaissement where promo='{$promo}' ");

		$depense=$prod['salaire'];
		$nbredepense=$prod['nbre'];

		return array($depense,$nbredepense);

	}

	public function inscriptionTotal($promo){

		$prod=$this->DB->querys("SELECT sum(montant) as montant FROM payement inner join inscription on payement.matricule=inscription.matricule where annee='{$promo}' and promo='{$promo}'");

		return $prod['montant'];

	}

	public function effectifTotal($promo){

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where annee='{$promo}'");

		return $prod['nbre'];

	}

	public function effectifTotForm($codef, $promo){

		$prod=$this->DB->querys("SELECT count(id) as nbre FROM inscription where codef='{$codef}' and annee='{$promo}'");

		return $prod['nbre'];

	}


	public function inscriptionTotCursus($cursus, $promo){

		$prod=$this->DB->querys("SELECT sum(montant) as montant FROM payement inner join inscription on payement.matricule=inscription.matricule where niveau='{$cursus}' and annee='{$promo}' and promo='{$promo}'");

		return array($prod['montant']);

	}

	public function resteIns($cursus, $promo){
		$pr=$promo-1;
		$montant=0;
		$remise=100;
		$ins='inscription';
		$reins='reinscription';

		$prodins=$this->DB->querys("SELECT count(inscription.matricule) as nbre FROM payement inner join inscription on payement.matricule=inscription.matricule where motif='{$ins}' and niveau='{$cursus}' and annee='{$promo}' and montant='{$montant}' and payement.remise!='{$remise}' and promo='{$promo}' ");

		$prodreins=$this->DB->querys("SELECT count(inscription.matricule) as nbre FROM payement inner join inscription on payement.matricule=inscription.matricule where motif='{$reins}' and niveau='{$cursus}' and annee='{$promo}' and montant='{$montant}' and payement.remise!='{$remise}' and promo='{$promo}' ");

		return array($prodins['nbre']*$this->fraisInscription($cursus, $promo)[0]+$prodreins['nbre']*$this->fraisInscription($cursus, $promo)[1]);

	}

	public function totFraiscolPaye($tranche,$promo){

		$prodfrais=$this->DB->querys("SELECT sum(montant) as montant FROM payementfraiscol where tranche='{$tranche}' and promo='{$promo}'");

		return $prodfrais['montant'];

	}

	public function totRestScol($codef, $tranche, $promo){

		$prodscol=$this->DB->querys("SELECT montant FROM scolarite where codef='{$codef}' and tranche='{$tranche}' and promo='{$promo}'");

		if (empty($prodscol['montant'])) {
			$totap=($this->effectifTotForm($codef,$promo))*0;	
		}else{

			$totap=($this->effectifTotForm($codef,$promo))*$prodscol['montant'];
		}

		$reste=$totap-($this->totFraiscolPayeTranche($codef, $tranche, $promo));

		return $reste;

	}

	public function totFraiscolPayeTranche($codef, $tranche, $promo){

		$prodfrais=$this->DB->querys("SELECT sum(montant) as montant FROM payementfraiscol inner join inscription on payementfraiscol.matricule=inscription.matricule  where codef='{$codef}' and tranche='{$tranche}' and promo='{$promo}' and annee='{$promo}'");

		return $prodfrais['montant'];

	}

	public function totRestTranche($codef, $tranche, $promo){

		$prodscol=$this->DB->querys("SELECT montant FROM scolarite where codef='{$codef}' and tranche='{$tranche}' and promo='{$promo}'");

		if (empty($prodscol['montant'])) {
			$montantscol=0;
		}else{
			$montantscol=$prodscol['montant'];
		}

		$totap=$this->effectifTotForm($codef, $promo)*$montantscol;

		$reste=$totap-$this->totFraiscolPayeTranche($codef, $tranche, $promo);

		return $reste;

	}



	public function restescol($cursus, $promo){
		$pr=$promo-1;
		$montant=0;

		$prodins=$this->DB->querys("SELECT count(payement.matricule) as nbre FROM payement inner join inscription on payement.matricule=inscription.matricule where payement.matricule not in(SELECT matricule from payement where promo='{$pr}') and niveau='{$cursus}' and annee='{$promo}' and montant='{$montant}' and promo='{$promo}' ");

		$prodreins=$this->DB->querys("SELECT count(payement.matricule) as nbre FROM payement inner join inscription on payement.matricule=inscription.matricule where payement.matricule in(SELECT matricule from payement where promo='{$pr}') and niveau='{$cursus}' and annee='{$promo}' and montant='{$montant}' and promo='{$promo}' ");

		return array($prodins['nbre']*$this->fraisInscription($cursus, $promo)[0]+$prodreins['nbre']*$this->fraisInscription($cursus, $promo)[1]);

	}

	public function codefSuivant($codef){

		if ($codef=='tpsm') {

			$codefsuiv='psm';
			$niveau='maternelle';
			$classe='Petite Section';
			$niveauactu='maternelle';
			$classeactu='Toute Petite Section';

		}elseif ($codef=='psm') {

			$codefsuiv='msm';
			$niveau='maternelle';
			$classe='Moyenne Section';
			$niveauactu='maternelle';
			$classeactu='Petite Section';

		}elseif ($codef=='msm') {

			$codefsuiv='gsm';
			$niveau='maternelle';
			$classe='Grande Section';
			$niveauactu='maternelle';
			$classeactu='Moyenne Section';

		}elseif ($codef=='gsm') {

			$codefsuiv='1pri';
			$niveau='primaire';
			$classe='1ère Année';
			$niveauactu='maternelle';
			$classeactu='Grande Section';

		}elseif ($codef=='1pri') {

			$codefsuiv='2pri';
			$niveau='primaire';
			$classe='2ème Année';
			$niveauactu='primaire';
			$classeactu='1ère Année';

		}elseif ($codef=='2pri') {

			$codefsuiv='3pri';
			$niveau='primaire';
			$classe='3ème Année';
			$niveauactu='primaire';
			$classeactu='2ème Année';

		}elseif ($codef=='3pri') {

			$codefsuiv='4pri';
			$niveau='primaire';
			$classe='4ème Année';
			$niveauactu='primaire';
			$classeactu='3ème Année';

		}elseif ($codef=='4pri') {

			$codefsuiv='5pri';
			$niveau='primaire';
			$classe='5ème Année';
			$niveauactu='primaire';
			$classeactu='4ème Année';

		}elseif ($codef=='5pri') {

			$codefsuiv='6pri';
			$niveau='primaire';
			$classe='6ème Année';
			$niveauactu='primaire';
			$classeactu='5ème Année';

		}elseif ($codef=='6pri') {

			$codefsuiv='7col';
			$niveau='college';
			$classe='7ème Année';
			$niveauactu='primaire';
			$classeactu='6ème Année';

		}elseif ($codef=='7col') {

			$codefsuiv='8col';
			$niveau='college';
			$classe='8ème Année';
			$niveauactu='college';
			$classeactu='7ème Année';

		}elseif ($codef=='8col') {

			$codefsuiv='9col';
			$niveau='college';
			$classe='9ème Année';
			$niveauactu='college';
			$classeactu='8ème Année';

		}elseif ($codef=='9col') {

			$codefsuiv='10col';
			$niveau='college';
			$classe='10ème Année';
			$niveauactu='college';
			$classeactu='9ème Année';

		}elseif ($codef=='10col') {

			$codefsuiv='11sm';
			$niveau='lycee';
			$classe='11ème Année';
			$niveauactu='lycee';
			$classeactu='10ème Année';

		}elseif ($codef=='11sm' or $codef=='11eme S.M') {
			if ($codef=='11sm') {
				$codefsuiv='12sm';
			}else{
				$codefsuiv='12eme S.M';
			}
			$niveau='lycee';
			$classe='12ème Année Mathématiques';
			$niveauactu='lycee';
			$classeactu='11ème Année Scientifique';

		}elseif ($codef=='11se' or $codef=='11eme S.E') {

			if ($codef=='11se') {
				$codefsuiv='12se';
			}else{
				$codefsuiv='12eme S.E';
			}

			$codefsuiv='12se';
			$niveau='lycee';
			$classe='12ème Année Littéraire';
			$niveauactu='lycee';
			$classeactu='11ème Année Littéraire';

		}elseif ($codef=='12sm' or $codef=='12eme S.M') {

			if ($codef=='12sm') {
				$codefsuiv='tsm';
			}else{
				$codefsuiv='T S.M';
			}

			$codefsuiv='tsm';
			$niveau='lycee';
			$classe='Terminale SM';
			$niveauactu='lycee';
			$classeactu='12ème Année Scientifique';

		}elseif ($codef=='12se' or $codef=='12eme S.E') {

			if ($codef=='12se') {
				$codefsuiv='tse';
			}else{
				$codefsuiv='T S.E';
			}

			$codefsuiv='tse';
			$niveau='lycee';
			$classe='Terminale SE';
			$niveauactu='lycee';
			$classeactu='12ème Année Littéraire';
		}else{

			$codefsuiv='';
			$niveau='';
			$classe='';
			$niveauactu='';
			$classeactu='';

		}

		return array($codef, $codefsuiv, $niveau, $classe, $niveauactu, $classeactu);

	}


	public function seuilClasse($codef, $promo){

		$prod=$this->DB->querys("SELECT classe, nomf FROM formation where codef='{$codef}' ");

		$totalinscrit=$this->effectifTotForm($codef, $promo);

		if ($prod['classe']=='petite section') {

			$limiteclasse=100;

		}elseif ($prod['classe']=='moyenne section') {
			
			$limiteclasse=100;
		}elseif ($prod['classe']=='grande section') {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==1) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==2) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==3) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==4) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==5) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==6) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==7) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==8) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==9) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==10) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==11) {
			
			$limiteclasse=100;
		}elseif ($prod['classe']==12 and $prod['nomf']=='sciences maths') {
			
			$limiteclasse=100;

		}elseif ($prod['classe']==12 and $prod['nomf']=='sciences experimentales') {
			
			$limiteclasse=100;

		}elseif ($prod['classe']=='terminale' and $prod['nomf']=='sciences maths') {
			
			$limiteclasse=100;
		}else{
			$limiteclasse='0';
		}

		return ($limiteclasse-$totalinscrit);

	}
}