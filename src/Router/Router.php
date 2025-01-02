<?php

namespace Dashifen\SR6\CombatLog\Router;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Router\Router as DashifenRouter;
use Dashifen\SR6\CombatLog\Actions\IndexAction;
use Dashifen\SR6\CombatLog\Actions\LoginAction;
use Dashifen\SR6\CombatLog\Actions\SessionAction;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;
use Dashifen\SR6\CombatLog\Actions\AjaxActions\GetSessionIdAction;

class Router extends DashifenRouter
{
  /**
   * Returns true if the Route produced by an auto-router should be private.
   *
   * @return bool
   */
  protected function isRoutePrivate(): bool
  {
    return str_contains($this->path, 'session');
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
    return match ($this->route->action) {
      'IndexAction'        => new IndexAction($log, $this->request),
      'LoginAction'        => new LoginAction($log, $this->request),
      'SessionAction'      => new SessionAction($log, $this->request),
      
      // the following are all AJAX actions.
      
      'GetSessionIdAction' => new GetSessionIdAction($log, $this->request),
    };
  }
}
