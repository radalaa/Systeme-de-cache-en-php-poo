<?php
namespace App\Backend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \OCFram\FormHandler;
use \OCFram\Cache;


class NewsController extends BackController
{
  public function executeDelete(HTTPRequest $request)
  {
    
    $newsId = $request->getData('id');
    $this->deleteCacheNews($newsId);
    

    $this->managers->getManagerOf('News')->delete($newsId);
    $this->managers->getManagerOf('Comments')->deleteFromNews($newsId);

    $this->app->user()->setFlash('La news a bien été supprimée !');
        
    $this->app->httpResponse()->redirect('.');
  }

  public function executeDeleteComment(HTTPRequest $request)
  {
//////////////////////////////////////////////////////////////////////
    //Requette pour chercher l'id d'une news 
    $newsId = $request->getData('id');
    //@Methode deleteCacheComment() pour supprimer le cache
    $this->deleteCacheComment($newsId);
/////////////////////////////////////////////////////////////

    $this->managers->getManagerOf('Comments')->delete($request->getData('id'));
    
    $this->app->user()->setFlash('Le commentaire a bien été supprimé !');
    
    $this->app->httpResponse()->redirect('.');
  }

  public function executeIndex(HTTPRequest $request)
  {
    $this->page->addVar('title', 'Gestion des news');

    $manager = $this->managers->getManagerOf('News');

    $this->page->addVar('listeNews', $manager->getList());
    $this->page->addVar('nombreNews', $manager->count());
  }

  public function executeInsert(HTTPRequest $request)
  {
    $this->processForm($request);

    $this->page->addVar('title', 'Ajout d\'une news');
  }

  public function executeUpdate(HTTPRequest $request)
  {
    $this->processForm($request);

    $this->page->addVar('title', 'Modification d\'une news');
  }

  public function executeUpdateComment(HTTPRequest $request)
  {
   
    $this->page->addVar('title', 'Modification d\'un commentaire');

    if ($request->method() == 'POST')
    {
      $comment = new Comment([
        'id' => $request->getData('id'),
        'auteur' => $request->postData('auteur'),
        'contenu' => $request->postData('contenu')
      ]);
    }
    else
    {
      $comment = $this->managers->getManagerOf('Comments')->get($request->getData('id'));
    }

    $formBuilder = new CommentFormBuilder($comment);
    $formBuilder->build();

    $form = $formBuilder->form();

    $formHandler = new FormHandler($form, $this->managers->getManagerOf('Comments'), $request);

    if ($formHandler->process())
    {
       
      
      
        $this->app->user()->setFlash('Le commentaire a bien été modifié');

/////////////////////////////////////////////////////////////
        $newsId = $request->getData('id');
        //@Methode deleteCacheComment() pour supprimer le cache
        $this->deleteCacheComment($newsId);
/////////////////////////////////////////////////////////////
      $this->app->httpResponse()->redirect('/admin/');
    }

    $this->page->addVar('form', $form->createView());
  }

  public function processForm(HTTPRequest $request)
  {
    if ($request->method() == 'POST')
    {
      $news = new News([
        'auteur' => $request->postData('auteur'),
        'titre' => $request->postData('titre'),
        'contenu' => $request->postData('contenu')
      ]);

      if ($request->getExists('id'))
      {
        $news->setId($request->getData('id'));
      }
    }
    else
    {
      // L'identifiant de la news est transmis si on veut la modifier
      if ($request->getExists('id'))
      {
        $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
      }
      else
      {
        $news = new News;
      }
    }

    $formBuilder = new NewsFormBuilder($news);
    $formBuilder->build();

    $form = $formBuilder->form();

    $formHandler = new FormHandler($form, $this->managers->getManagerOf('News'), $request);

    if ($formHandler->process())
    {
      $this->app->user()->setFlash($news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');
////////////////////////////////////////////////////////
          //supprimer le cahe de la page d'accueil via la function deletecahe
          $cache = new Cache();
          $root = $cache->getChemain();
          $file = $root.'/tmp/cache/views/index.php';
          //appler @Methode pour supprimer le cache de la page d'accueil 
          $cache->deletecache($file,'index.php');
///////////////////////////////////////////////////////////
          $this->app->httpResponse()->redirect('/admin/');
    }

    $this->page->addVar('form', $form->createView());
  }
  public function deleteCacheComment($id){
      //requette pour chercher le id news 
      $comment = $this->managers->getManagerOf('Comments')->getUnique($id);
      //supprimer le cache le cahe de la page d'accueil via la function deletecahe
      $cache = new Cache();          
      $cache->deleteCacheData($comment['news']);
    return true;
   
  }
////////////////////////////////////////////////////////////
  public function deleteCacheNews($id){

      $news = $this->managers->getManagerOf('News')->getUnique($id);
      $cache = new Cache();          
      $cache->deleteCacheData($news['id']);
    return true;
   
  }
/////////////////////////////////////////////////////////////

}