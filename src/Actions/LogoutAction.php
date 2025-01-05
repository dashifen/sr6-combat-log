<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Actions\Framework\AbstractPrivateAction;

class LogoutAction extends AbstractPrivateAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $this->request->getSessionObj()->destroy();
    header('Location: /');
  }
}
