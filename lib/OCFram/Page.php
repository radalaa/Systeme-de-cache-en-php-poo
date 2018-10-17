<?php
namespace OCFram;

use \OCFram\Cache;

class Page extends ApplicationComponent
{
  protected $contentFile;
  protected $vars = [];
  protected $chemain;
  protected $fiile;

 /*
public function setchemain($chemain){
 $this->chemain = $chemain;
 echo $this->chemain;
}
public function getchemain(){

}
*/
public function cacheGeneratedPage($file){
    echo $this->contentFile;
    if (!file_exists($file))
    {
      throw new \RuntimeException('La vue spécifiée n\'existe pas');
    }
      $user = $this->app->user();

      extract($this->vars);
      ob_start();
      require $file;
      $content = ob_get_clean();
      ob_start();
      require __DIR__.'/../../App/'.$this->app->name().'/Templates/layout.php';;
      return ob_get_clean();
   
    }
  

  public function addVar($var, $value)
  {
    if (!is_string($var) || is_numeric($var) || empty($var))
    {
      throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractères non nulle');
    }
    
    $this->vars[$var] = $value;
  }

  public function getGeneratedPage()
  {
    
     $cache = new Cache();

    if (!file_exists($this->contentFile))
    {
      throw new \RuntimeException('La vue spécifiée n\'existe pas');
    }
      $user = $this->app->user();
      extract($this->vars);
      ob_start();
      require $this->contentFile;
      $content = ob_get_clean();
      $filename = basename($this->contentFile);
      if ($filename == 'index.php'){
        $file = 'E:\xampp\htdocs\monsite\tmp\cache\views\index.php';
      }else{
        $server = str_replace("/", "\\", $_SERVER['REQUEST_URI']);
        $file = 'E:\xampp\htdocs\monsite\tmp\cache\datas'.$server;
      }
      //appler la function pour écrire le cache
      $cache->createcache($file,$content);
      ob_start();
      require __DIR__.'/../../App/'.$this->app->name().'/Templates/layout.php';;
      return ob_get_clean();
    
   
  }

  

  public function setContentFile($contentFile)
  {
    if (!is_string($contentFile) || empty($contentFile))
    {
      throw new \InvalidArgumentException('La vue spécifiée est invalide');
    }    
    $this->contentFile = $contentFile;
    
  }


  public function getContentFile()
  {
      
    return $this->contentFile;
    
  }

  //function pour envoyer le contenue
}