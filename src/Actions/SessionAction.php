<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Actions\Framework\AbstractPrivateAction;

class SessionAction extends AbstractPrivateAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $this->combatLog->render('session.twig');
  }
}
