<?php

namespace Dashifen\SR6\CombatLog\Actions\Framework;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Request\RequestInterface;

abstract class AbstractPrivateAction extends AbstractAction
{
  /**
   * AbstractPrivateAction constructor which ensures that our session is
   * authentic before proceeding.
   *
   * @param CombatLog        $combatLog
   * @param RequestInterface $request
   *
   * @throws ActionException
   */
  public function __construct(CombatLog $combatLog, RequestInterface $request)
  {
    if (!$request->getSessionObj()->isAuthenticated()) {
      throw new ActionException('Inauthentic request', ActionException::INAUTHENTIC);
    }
    
    parent::__construct($combatLog, $request);
  }
}
