<?php

  /*
   *
   * App Core Class
   * Creates URL & loads core controller
   * URL FORMAT - /controller/method/params
   */

  class Core {

    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];    


    public function __construct() {
      //print_r($this->getUrl());
  
      $url = $this->getUrl();

      // check if we have a controller for that
      if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
        // set current controller
        $this->currentController = ucwords($url[0]);
        // unset 0 index
        unset($url[0]);
      } 

      // Require the controller
      require_once '../app/controllers/' . $this->currentController . '.php';
   
      // Instantiate controller class
      $this->currentController = new $this->currentController;

      // Call appriopriate method if it exists in the url and in the controller
      if (isset($url[1])) {
        if (method_exists($this->currentController, $url[1])) {
          $this->currentMethod = $url[1];
        }
        // unset 1 index
        unset($url[1]);
      }

      // Set params if exists
      $this->params = $url ? array_values($url) : [];

      // Call a callback with array of params
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);

    }

    public function getUrl() {
      
      if (isset($_GET['url'])) {
        //strip ending slah
        $url = rtrim($_GET['url'], '/');
        //sanitize
        $url = filter_var($url, FILTER_SANITIZE_URL);
        //break into an array by slash
        $url = explode('/', $url);
      
        return $url;
      }
    }


  }
