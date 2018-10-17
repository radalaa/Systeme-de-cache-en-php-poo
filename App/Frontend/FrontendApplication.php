<?php
namespace App\Frontend;

use \OCFram\Application;
use \OCFram\Cache;

class FrontendApplication extends Application
{
  protected $existe;
  protected $chemain;

  public function __construct()
  {
    parent::__construct();

    $this->name = 'Frontend';
  }

  public function run()
  {
   

  $controller = $this->getController();
  $Mymethod = $controller->execute();
  $file = $controller->caheverif($Mymethod);
  $existe = $file['existe'];
  $chemain = $file['chemain'];

  if ($file['existe']) {
    $this->httpResponse->sendCache($file['chemain']);
  }else{
    $this->httpResponse->setPage($controller->page());
    $this->httpResponse->send();
  }
    

 
    
  }
}