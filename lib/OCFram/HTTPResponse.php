<?php
namespace OCFram;

class HTTPResponse extends ApplicationComponent
{
  protected $page;

  public function addHeader($header)
  {
    header($header);
  }

  public function redirect($location)
  {
    header('Location: '.$location);
    exit;
  }

  public function redirect404()
  {
    $this->page = new Page($this->app);
    $this->page->setContentFile(__DIR__.'/../../Errors/404.html');
    
    $this->addHeader('HTTP/1.0 404 Not Found');
    
    $this->send();
  }


  
  public function send()
  {
    // traitement du cache ici
    //La vue correspondant à la page d’accueil du site.
    //chacune des  news
    //Toutes les listes de commentaires (une liste de commentaires correspond à l’ensemble des commentaires liés à une news). 

    
    // Actuellement, cette ligne a peu de sens dans votre esprit.
    // Promis, vous saurez vraiment ce qu'elle fait d'ici la fin du chapitre
    // (bien que je suis sûr que les noms choisis sont assez explicites !).
    exit($this->page->getGeneratedPage());
}


  public function setPage(Page $page)
  {
    $this->page = $page;
  }

  // Changement par rapport à la fonction setcookie() : le dernier argument est par défaut à true
  public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
  {
    setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
  }





  public function sendCache($file){
    $this->page = new Page($this->app);
   exit($this->page->cacheGeneratedPage($file));
  }
  
}