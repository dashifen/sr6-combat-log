<?php

namespace Dashifen\SR6\CombatLog\Actions\Private;

use Dashifen\SR6\CombatLog\Actions\AbstractPrivateAction;

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
