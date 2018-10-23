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

  protected $var; 




  public function __construct(Application $app, $module, $action)
  {

    parent::__construct($app);
    $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    $this->page = new Page($app);
    $this->setModule($module);
    $this->setAction($action);
    $this->setView($action);

  }
  public function execute()
  {
    //recucpurer le nom de l'application 
    $this->name = $this->app->name();
    $methood = 'execute'.ucfirst($this->action);
      
    if (!is_callable([$this, $methood]))
    {
      throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
    }

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
  }

  public function setAction($action)
  {
    if (!is_string($action) || empty($action))
    {
      throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
    }
    $this->action = $action;
  }

  public function setView($view)
  {
    if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

    $this->view = $view;
    ////////////////////////////////////////////
    //instancier la class cache pour appler la methode getchemain()
    $cache = new Cache();
    ////////////////////////////////////////
    $this->page->setContentFile($cache->getchemain().'/App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
    
   
  }
}