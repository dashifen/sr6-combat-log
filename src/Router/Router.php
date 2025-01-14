<?php

namespace Dashifen\SR6\CombatLog\Router;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Router\Router as DashifenRouter;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

class Router extends DashifenRouter
{
  /**
   * Returns true if the Route produced by an auto-router should be private.
   *
   * @return bool
   */
  protected function isRoutePrivate(): bool
  {
    return str_contains($this->request->getServerVar('REQUEST_URI'), 'session');
  }
  
  /**
   * Returns an AbstractAction extension to the calling scope.
   *
   * @param CombatLog $log
   *
   * @return AbstractAction
   */
  public function getActionObject(CombatLog $log): AbstractAction
  {
    // since this is a fairly small application, all of our actions are in the
    // same namespace.  if we prepend that namespace to the action our parent
    // identified as the one to handle this route, we get the full class name
    // of the object we want to instantiate and return as follows:
    
    $action = 'Dashifen\\SR6\\CombatLog\\Actions\\' . $this->route->action;
    return new $action($log, $this->request);
  }
}
