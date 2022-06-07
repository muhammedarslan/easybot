<?php

class App
{
  /**
   * @var string
   */
  protected $actualPath;

  /**
   * @var string
   */
  protected $actualMethod;

  /**
   * @var array
   */
  protected $routes = [];

  /**
   * @var \Closure|string
   */
  protected $notFound;


  public function __construct($currentPath, $currentMethod)
  {
    if ($currentPath == '') {
      $currentPath = '/';
    }
    $this->actualPath = $currentPath;
    $this->actualMethod = $currentMethod;

    // 404
    $this->notFound = function () {
      require_once VDIR . '/page.404.php';
    };
  }


  public function get($path, $callback)
  {
    $this->routes[] = ['GET', $path, $callback];
  }


  public function post($path, $callback)
  {
    $this->routes[] = ['POST', $path, $callback];
  }

  public function run()
  {
    foreach ($this->routes as $route) {
      list($method, $path, $callback) = $route;

      $checkMethod = $this->actualMethod == $method;
      $checkPath = preg_match("~^{$path}$~ixs", $this->actualPath, $params);

      if ($checkMethod && $checkPath) {
        array_shift($params);
        return call_user_func_array($callback, $params);
      }
    }

    return call_user_func($this->notFound);
  }
}

$route_path = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
$route_method = $_SERVER['REQUEST_METHOD'];
$route_path = (str_replace(PATH, '/', $route_path) == '') ? '/' : str_replace(PATH, '/', $route_path);
if (StaticFunctions::clear($_SERVER['SERVER_NAME']) != API_DOMAIN && StaticFunctions::clear($_SERVER['SERVER_NAME']) != PUSH_DOMAIN) :
  $route_path = AppLanguage::UrlMaker($route_path);
endif;
$App = new App($route_path, $route_method);
require_once CORE_DIR . '/route.map.php';