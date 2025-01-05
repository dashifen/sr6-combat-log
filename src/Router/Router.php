<?php

namespace Dashifen\SR6\CombatLog\Router;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Router\Router as DashifenRouter;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;
use Dashifen\SR6\CombatLog\Actions\Framework\ActionException;
use Dashifen\SR6\CombatLog\Actions\{
  IndexAction,
  LoginAction,
  LogoutAction,
  SessionAction};

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
   * @throws ActionException
   */
  public function getActionObject(CombatLog $log): AbstractAction
  {
    return match ($this->route->action) {
      'IndexAction'   => new IndexAction($log, $this->request),
      'LoginAction'   => new LoginAction($log, $this->request),
      'LogoutAction'  => new LogoutAction($log, $this->request),
      'SessionAction' => new SessionAction($log, $this->request),
    };
  }
}
