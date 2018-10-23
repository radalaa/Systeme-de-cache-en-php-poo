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
  /////Début//////la mise en cache de la page d'accueil////////////////////////////
  //instancier la class Cache
  $cache = new Cache();
  //appler la methode verif_Cache_Accueil() qui prend en parametre le controlleur et l'action a excuter
  //Rôle de la methode :  verifier la vie du fichier et si il existe, return le chemain du fichier
  $file = $cache->verif_Cache_Accueil($Mymethod,$controller);
  $existe = $file['existe'];
  $chemain = $file['chemain'];
  //si n'existe pas traitement normal, generer la page et creer un cache
  if (!$file['existe']) {
    $this->httpResponse->setPage($controller->page());
    $this->httpResponse->send();
  }else{
    //appler la methode sendCache(), prend en parametre le chemain du fichier pour afficher le cache 
    //qui est valide
    $this->httpResponse->sendCache($file['chemain']);  
  }
  ///////Fin /////////////////////////////////////////////    
  }
}