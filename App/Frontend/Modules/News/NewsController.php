<?php
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;
use \OCFram\Cache;

class NewsController extends BackController
{
  
  public function executeIndex()
  {
// si le cache existe alors appler la function read cache de index 
//sinon on contunue le traitment normal pour creer la page et géner un cache
    $cache = new Cache();

    $nombreNews = $this->app->config()->get('nombre_news');

    $nombreCaracteres = $this->app->config()->get('nombre_caracteres');
    
    // On ajoute une définition pour le titre.
    $this->page->addVar('title', 'Liste des '.$nombreNews.' dernières news');
    
    // On récupère le manager des news.
    $manager = $this->managers->getManagerOf('News');
    
    $listeNews = $manager->getList(0, $nombreNews);

    $dataCache = $cache->setData($listeNews,$nombreCaracteres);
    //var_dump($dataCache);

    /*
    $zer = serialize($listeNews);
    var_dump($zer);
    var_dump(unserialize($zer));
    */
    foreach ($dataCache as $news)
    {
      if (strlen($news->contenu()) > $nombreCaracteres)
      {
        $debut = substr($news->contenu(), 0, $nombreCaracteres);
        $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
        
        $news->setContenu($debut);
      }
    }
    
   
    $this->page->addVar('listeNews', $dataCache);

     

    

  }
  
  public function executeShow(HTTPRequest $request)
  {
    $cache = new Cache();
    $root = str_replace("/", "\\", $cache->getChemain());
    $filename = str_replace("/", "\\", $_SERVER['REQUEST_URI']);
    $file = $root.'\tmp\cache\datas'.$filename.'.txt';
    

    
    if(!file_exists($file)){
      $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
     $cache->setData($news,$file);
    }else{
      $news = $cache->getData($file);
      echo 'c est le contenu du cache ';
    }
    
    //var_dump($news);

    //$cache->setData($news);

    //var_dump($news);

    if (empty($news))
    {
      $this->app->httpResponse()->redirect404();
    }
    
    $this->page->addVar('title', $news->titre());
    $this->page->addVar('news', $news);
    $this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));
  }

  public function executeInsertComment(HTTPRequest $request)
  {
    // Si le formulaire a été envoyé.
    if ($request->method() == 'POST')
    {
      $comment = new Comment([
        'news' => $request->getData('news'),
        'auteur' => $request->postData('auteur'),
        'contenu' => $request->postData('contenu')
      ]);
    }
    else
    {
      $comment = new Comment;
    }

    $formBuilder = new CommentFormBuilder($comment);
    $formBuilder->build();

    $form = $formBuilder->form();

    $formHandler = new FormHandler($form, $this->managers->getManagerOf('Comments'), $request);

    if ($formHandler->process())
    {
      $this->app->user()->setFlash('Le commentaire a bien été ajouté, merci !');
      
      $this->app->httpResponse()->redirect('news-'.$request->getData('news').'.html');
    }

    $this->page->addVar('comment', $comment);
    $this->page->addVar('form', $form->createView());
    $this->page->addVar('title', 'Ajout d\'un commentaire');
  }

  public function executePagecache(HTTPRequest $request)
  {
   
   echo 'Je cherche la page cache !!!!!';
       

  }

}