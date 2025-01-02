<?php

namespace Dashifen\SR6\CombatLog\Actions\AjaxActions;

use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

class GetSessionIdAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    echo uniqid();
  }
}
