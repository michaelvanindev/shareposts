<?php
  /*
  * App Core Class
  * Creates URL & loads core controller
  * URL FORMAT - /controller/method/parameters
  */

  class Core {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
      // print out the URL array
      // print_r($this->getUrl());

      // put URL array in a variable
      $url = $this->getUrl();
      // look in controllers for first value
      // this is defined as if I was in index.php due to routing (../app/controllers/)
      // capitalize first letter because all controllers begin with an uppercase letter
      if(isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
        // if exists, set as controller
        $this->currentController = ucwords($url[0]);
        // unset the 0 index
        unset($url[0]);
      }

      // require the controller
      require_once '../app/controllers/' . $this->currentController . '.php';
      // instantiate controller class
      $this->currentController = new $this->currentController;
      // check for second part of URL
      if(isset($url[1])) {
        // check to see if method exists in controller
        if(method_exists($this->currentController, $url[1])) {
          $this->currentMethod = $url[1];
          // unset the 1 index
          unset($url[1]);
        }
      }

      // get parameters
      $this->params = $url ? array_values($url) : [];

      // call a callback with array of parameters
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);

    }

    public function getUrl() {
      if(isset($_GET['url'])) {
        // remove trailing slash from URL
        $url = rtrim($_GET['url'], '/');
        // sanitize url
        $url = filter_var($url, FILTER_SANITIZE_URL);
        // break URL down into array of URL parts
        $url = explode('/', $url);
        return $url;
      }
    }
  }