<?php

namespace Dashifen\SR6\CombatLog\Router;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Router\Router as DashifenRouter;
use Dashifen\SR6\CombatLog\Actions\AbstractAction;

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
    // since this is a mostly-private application, the only public actions
    // are the index and login ones.  so, if this is not one of those, we know
    // the object we need to instantiate and return is in the private
    // namespace.
    
    $namespace =  !in_array($this->route->action, ['IndexAction','LoginAction'])
      ? 'Dashifen\\SR6\\CombatLog\\Actions\\Private\\'
      : 'Dashifen\\SR6\\CombatLog\\Actions\\Public\\';
    
    $action =  $namespace . $this->route->action;
    return new $action($log, $this->request);
  }
}
