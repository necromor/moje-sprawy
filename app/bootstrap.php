<?php 
  // Load config
  require_once 'config/config.php';

  // Load helpers
  require_once 'helpers/url_helper.php';
  require_once 'helpers/session_helper.php';
  require_once 'helpers/manipulacja_id.php';
  require_once 'helpers/format_kwoty.php';
  require_once 'helpers/dostep.php';

  // Autoload Core Libraries
  spl_autoload_register( function($className) {
    require_once 'libraries/' . $className . '.php';
  });
