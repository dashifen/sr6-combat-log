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
  $router = new Router;
  $combatLog = new CombatLog($router->request->getSessionObj());
  $router->getActionObject($combatLog)->execute();
} catch (Exception $e) {
  if (
    is_a($e, ActionException::class)
    && $e->getCode() === ActionException::INAUTHENTIC
  ) {
    
    // if we caught the visitor attempting to access a session without having
    // "authenticated" their session, then we'll just redirect to the index
    // page where they can log in.
    
    header('Location: /');
    exit;
  }
  
  // otherwise, we'll just puke the exception text on-screen and fix it.
  
  CombatLog::catcher($e);
}
