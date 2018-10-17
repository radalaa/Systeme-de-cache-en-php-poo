<?php
namespace OCFram;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;

/**
 * @classe pour un system de cache 
 */
class Cache extends ApplicationComponent
{
	/**
	*@params le chemain du dosssier a mettre en cache
	**/

	    //echo TMPROOT;
	  
	
	public $dirname ;
	protected $chemain;
	protected $contenu;
	protected $cho;
	private $page;
	protected $MyObject;
	/**
	*@params la durrer de vie de du cache
	**/
	public $duree;
	protected $tab = [];
	
	
	/**
	*@params le nom de l'application qui excute le cache 
	**/
	//private $name = '' ;
	//public $action = '' ;
	
	public function __construct()
	{
	if (!defined('ROOT')) define('ROOT', dirname(__FILE__));
	if (!defined('RE')) define('RE', dirname(ROOT));
	if (!defined('TMPROOT')) define('TMPROOT', dirname(RE));
	$this->dirname = str_replace("\\", "/", TMPROOT);
	
	
	$this->duree = 60;
	$this->setChemain($this->dirname);
	}
	
	public function setData($dataNews,$file)
	{
		$this->dataNews = $dataNews;
		$variable = serialize($this->dataNews);
		$this->write($file,$variable.chr(8));
	}

	public function getData($file)
	{
		$var2 =  file_get_contents($file);
		return unserialize($var2);
	}

	public function setContenu($contenu)
	{
		echo $this->contenu;
		$this->contenu = $contenu;
		
	}
	public function getContenu(){
		return $this->contenu;
	}

	public function read($filename){
		return file_get_contents($this->dirname.'\\'.$filename);
	}
	public function write($filenam,$content)
	{
		
		return file_put_contents($filenam, $content);
	}

	public function createcache($che,$contenu)
	{
	return file_put_contents($che, $contenu);
	}
	
	public function inc($file){

	$filename = basename($file);

	ob_start();
      require $file;
    $content = ob_get_clean();
    return ob_get_clean();
	}

public function getChemain()
{
 return $this->chemain;
}
public function setChemain($chemain)
{
 $this->chemain = $chemain;
 return true;
}




}



