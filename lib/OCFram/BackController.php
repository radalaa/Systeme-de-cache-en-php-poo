<?php
namespace OCFram;

use \OCFram\Cache;

abstract class BackController extends ApplicationComponent
{
  protected $action = '';
  protected $module = '';
  protected $page = null;
  protected $view = '';
  protected $managers = null;
  protected $name = '';
  protected $contoller;
  public $adresse;




  public function __construct(Application $app, $module, $action)
  {

    parent::__construct($app);

    //echo $app;
    $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    $this->page = new Page($app);
    $this->setModule($module);
    $this->setAction($action);
    $this->setView($action);

  }

  public function getchemain(){
    if (!defined('ROOT')) define('ROOT', dirname(__FILE__));
    if (!defined('RE')) define('RE', dirname(ROOT));
    if (!defined('TMPROOT')) define('TMPROOT', dirname(RE));
    $chemain = str_replace("\\", "/", TMPROOT);
    return $chemain ;
  }

    // verifier si index existe
   public function caheverif($contoller){
      $cache = new Cache();
      if ($contoller == 'executeIndex'){
           
          $file = $this->getchemain() . '/tmp/cache/views'.$_SERVER["REQUEST_URI"]. 'index.php';
        
          if (!file_exists($file)) {
            //$cache->createcache($file);
            return false;
                      
          }else{
             return array('existe' => true,
                     'chemain' => $file);
          }
          

      }elseif ($contoller == 'executeShow') {
      
          $file = $this->getchemain() . '/tmp/cache/datas'.$_SERVER["REQUEST_URI"];

          if (!file_exists($file)) {
            return false;
                    
          }else{
            return array('existe' => true,
                     'chemain' => $file);   
          }

      }elseif ($contoller == 'executeInsertComment') {
        //appler une function pour supprrimer les commenaire d'une news
          $file = $this->getchemain() . '/tmp/cache/datas'.$_SERVER["REQUEST_URI"];
          if (!file_exists($file)) {
              return false;       
          }else{
             return array('existe' => true,
                     'chemain' => $file); 
          }
      }   
   }

 

  public function execute()
  {
    //recucpurer le nom de l'application 
    $this->name = $this->app->name();
    $methood = 'execute'.ucfirst($this->action);
    //instancier la class cache
    //si le fichier cache existe on fait le traitement
    $cache = new Cache();
    //echo $cache->getChemain();
      
    
    
    if (!is_callable([$this, $methood]))
    {
      throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
    }
    
    //print_r($this->$methood);
    $this->$methood($this->app->httpRequest());
   return $methood;
  }

  public function page()
  {
    return $this->page;
  }

  public function setModule($module)
  {
    if (!is_string($module) || empty($module))
    {
      throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
    }

    $this->module = $module;
   // echo $this->module;  (news)
  }

  public function setAction($action)
  {
    if (!is_string($action) || empty($action))
    {
      throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
    }

    $this->action = $action;
    //echo $this->action;  (index)
  }



 

  public function setView($view)
  {
    if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

    $this->view = $view;

    //si il existe en cache et la date est correcte il faut excuter le cache 
    //if (! $variable = $cache->read('variable')){
    //$filename = $actresss.'/App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php';
    //echo  $filename;
    $var = $this->getchemain();
    $this->page->setContentFile($var.'/App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
    
   
  }
}