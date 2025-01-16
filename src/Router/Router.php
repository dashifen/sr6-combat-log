<?php

namespace Dashifen\SR6\CombatLog\Router;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Router\Router as DashifenRouter;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;
use Dashifen\SR6\CombatLog\Actions\AbstractAction;

class Router extends DashifenRouter
{
  use DebuggingTrait;
  
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
    $namespace = $this->getNamespace();
    $action = $namespace . $this->route->action;
    return new $action($log, $this->request);
  }
  
  /**
   * Returns the name space for an action.
   *
   * @return string
   */
  private function getNamespace(): string
  {
    $request = $this->request->getServerVar('REQUEST_URI');
    return 'Dashifen\\SR6\\CombatLog\\Actions\\' . match (true) {
        str_contains($request, 'character') => 'Private\\Character\\',
        str_contains($request, 'session')   => 'Private\\',
        default                             => 'Public\\',
      };
  }
}
