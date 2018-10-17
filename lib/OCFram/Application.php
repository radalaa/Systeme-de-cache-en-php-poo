<?php
namespace OCFram;

abstract class Application
{
  protected $httpRequest;
  protected $httpResponse;
  protected $name;
  protected $user;
  protected $config;
  protected $cache;


  public function __construct()
  {
    $this->httpRequest = new HTTPRequest($this);
    $this->httpResponse = new HTTPResponse($this);
    $this->user = new User($this);
    $this->config = new Config($this);
    $this->name = '';
    
  }

  

    /**
     * function
     * @return mixed
     */
    public function getController()
  {
 
    $router = new Router;

    $xml = new \DOMDocument;
    $xml->load(__DIR__.'/../../App/'.$this->name.'/Config/routes.xml');

    $routes = $xml->getElementsByTagName('route');

    // On parcourt les routes du fichier XML.
    foreach ($routes as $route)
    {
      $vars = [];

      // On regarde si des variables sont présentes dans l'URL.
      if ($route->hasAttribute('vars'))
      {
        $vars = explode(',', $route->getAttribute('vars'));
      }

      // On ajoute les routes au routeur.
      $router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
    }
    // recupurer la route
    try
    {
      // On récupère la route correspondante à l'URL.
      $matchedRoute = $router->getRoute($this->httpRequest->requestURI());
     // var_dump($matchedRoute);
    }
    catch (\RuntimeException $e)
    {
      if ($e->getCode() == Router::NO_ROUTE)
      {
        // Si aucune route ne correspond, c'est que la page demandée n'existe pas.
        $this->httpResponse->redirect404();
      }
    }

    // On ajoute les variables de l'URL au tableau $_GET.
    $_GET = array_merge($_GET, $matchedRoute->vars());
   

    // On instancie le contrôleur.
    $controllerClass = 'App\\'.$this->name.'\\Modules\\'.$matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
    return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
  }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @return HTTPRequest
     */
    public function httpRequest()
  {
    return $this->httpRequest;
  }

    /**
     * @return HTTPResponse
     */
    public function httpResponse()
  {
    return $this->httpResponse;
  }

    /**
     * @return string
     */
    public function name()
  {
    return $this->name;
  }

    /**
     * @return Config
     */
    public function config()
  {
    return $this->config;
  }

    /**
     * @return User
     */
    public function user()
  {
    return $this->user;
  }

}