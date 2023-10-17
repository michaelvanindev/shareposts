<?php
  // Load config
  require_once 'config/config.php';
  // Load helpers
  require_once 'helpers/url_helper.php';
  require_once 'helpers/session_helper.php';

  // Load libraries
  // require_once 'libraries/Core.php';
  // require_once 'libraries/Controller.php';
  // require_once 'libraries/Database.php';

  // Autoload core libraries
  spl_autoload_register(function($className) {
    // libraries filename must equal the class name
    require_once 'libraries/' . $className . '.php';
  });