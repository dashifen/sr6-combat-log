<?php

namespace Dashifen\SR6;

use Exception;
use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\SR6\CombatLog\Router\Router;
use Dashifen\SR6\CombatLog\Actions\Framework\ActionException;

if (!class_exists(CombatLog::class)) {
  require_once 'vendor/autoload.php';
}

@ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  
  // this complicated statement instantiates our router.  since we don't pass
  // it a collection of routes, it's an auto-router, and it'll determine the
  // action we're here to execute based on the current URL path.  we get that
  // object by calling its getActionObject method passing it an instance of
  // our CombatLog object, and then call that object's execute method.
  
  (new Router)
    ->getActionObject(new CombatLog)
    ->execute();
  
} catch (Exception $e) {
  if (
    is_a($e, ActionException::class)
    && $e->getCode() === ActionException::INAUTHENTIC
  ) {
    
    // if we caught the visitor attempting to access a session without having
    // "authenticated" their session, then we'll just redirect to the homepage.
    
    header('Location: /');
    exit;
  }
  
  // otherwise, we'll just puke the exception text on-screen and fix it.
  
  CombatLog::catcher($e);
}
