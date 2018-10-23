<?php
namespace OCFram;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;
use App\Frontend;

/**
 * @classe pour un system de cache 
 */
class Cache extends ApplicationComponent
{
	/**
	*@params $direname le chemain du root
	**/
	private $dirname ;
	private $chemain;
	private $commentfile;
	private $filenews;
	/*
	*@params $duree la duree de vie du cache par minute
	*/
	private $duree;
	/**
	*@params le nom de l'application qui excute le cache 
	**/
	public function __construct()
	{
	if (!defined('ROOT')) define('ROOT', dirname(__FILE__));
	if (!defined('RE')) define('RE', dirname(ROOT));
	if (!defined('TMPROOT')) define('TMPROOT', dirname(RE));
	$this->dirname = str_replace("\\", "/", TMPROOT);
	$this->setChemain($this->dirname);
	//la duree du cache , ici 1 minute
	$this->duree = 1;
	}

//Methode verif_Cache_Accueil(), permet de verifier la validité(si la vie<durrée de vie du cache), et la disponibilité du cache
	public function verif_Cache_Accueil($action,$controller){
		if (($action == 'executeIndex') and ($controller->app->name() == 'Frontend') and ($_SERVER["REQUEST_URI"] == '/')){
			$file = $this->getchemain() . '/tmp/cache/views'.$_SERVER["REQUEST_URI"]. 'index.php';
			if(file_exists($file)){
				if($this->verif_Expiration($file)){
					$this->deletecache($file,'index.php');
					return false;
				} 
			}
			if (!file_exists($file)) {
				return false;            
			}else{
				return array('existe' => true,'chemain' => $file);
			}    
		}   
	}
//Methode setData() pour creer le cache des données,en appelant la methode write() 
	public function setData($dataNews,$file)
	{
		$this->dataNews = $dataNews;
		$variable = serialize($this->dataNews);
		$this->write($file,$variable.chr(8));
	}
//Methode getData(), qui permet de verifier si le fichier est valide, sinon il le supprime
	public function getData($file,$newsfile,$commentfile)
	{	
		$this->newsfile = $newsfile;
		$this->commentfile = $commentfile;
		if(file_exists($file)){
		$existe =$this->verif_Expiration($file);
		//si le fichier est valide,nous lisons le cache
		//,sinon supprimer le cache à l'aide de la methode deletecache()
		if($existe){
			//appler la methode deletecache() qui permet de supprimer le cache et retourner FAUX pour sortire
			$this->deletecache($this->newsfile,$this->commentfile );
			return false;		
		}

		$var =  file_get_contents($file);
		return unserialize($var);
		}	
	}
//Methode verif_Expiration() qui permet de verifier validité du fichier cache
	private function verif_Expiration($file)
	{
		if(file_exists($file)){
			$lifetime = (time()-filemtime($file))/60;
			if ($lifetime>$this->duree) {
				return true;
			}else{
				echo 'Le cache est crée depuis : '.$lifetime.' s ,il sera supprimer aprés 1 minute de vie';
				return false;
			}
		}
	}

//Methode deleteCacheData() appler permet de supprimer le cache donnée on cas de ,supprision, modification, ajout de commentaire
	public function deleteCacheData($number){
		
			$filenews = $this->dirname.'/tmp/cache/datas/news-'.$number.'.html.txt';
			$filecomment = $this->dirname.'/tmp/cache/datas/commenter-'.$number.'.html.txt';

			if(!file_exists($filenews)){
				return false;
			}else{
				unlink($filenews);
				unlink($filecomment);
				return true;
			}
		}	
//Methode deleteCacheData() appler permet de supprimer le cache donnée on cas de ,supprision, modification, ajout de commentaire
	public function deletecache($newsfile,$commentfile){
		$chaine = $_SERVER['REQUEST_URI'];
		$action = substr($chaine, 0, 11);
		if($_SERVER['REQUEST_URI'] == '/' or $action == '/admin/news'){
			unlink($newsfile);
			return true;
		}else{
			$filenews = $this->dirname.'/tmp/cache/datas/'.$newsfile;
			$filecomment = $this->dirname.'/tmp/cache/datas/'.$commentfile;
			if(!file_exists($filenews)){
				return false;
			}else{
				unlink($filenews);
				unlink($filecomment);
				return true;
			}
		}	
	}
	//Methode pour crée le cache 
	public function write($filenam,$content)
	{
		return file_put_contents($filenam, $content);
	}
	//Méthode setChemain()
	private function setChemain($chemain)
	{
	 $this->chemain = $chemain;
	 return true;
	}
	public function getChemain()
	{
	 return $this->chemain;
	}

	//Methode pour gènérer le nom du fichier cache du commentaire,a l'aide de l'url du news
	public function getUrlComment(){
	  	$chaine = $_SERVER['REQUEST_URI'];
	  	//boucle pour récupurer le numero de news et commentaire
		for ($i = 0; $i <= 10; $i++) {
			preg_match("#[0-9]{".$i."}#",$chaine,$matches);
		 	if(!empty($matches[0])){
	  	  	 $number=$matches[0];	
			}
		}
			$URLCOMMENT = 'commenter-'.$number.'.html.txt';
      		return $URLCOMMENT;
	}
//Methode pour gènérer le nom du fichier cache du news,a l'aide de l'url du news
	public function getUrlNews(){
		$chaine = $_SERVER['REQUEST_URI'];
		//boucle pour récupurer le numero de news et commentaire
		for ($i = 0; $i <= 10; $i++) {
			preg_match("#[0-9]{".$i."}#",$chaine,$matches);
		 	if(!empty($matches[0])){
	  	  	 $number=$matches[0];	
			}
		}
			$URLNEWS = 'news-'.$number.'.html.txt';
      		return $URLNEWS;
	}



}



