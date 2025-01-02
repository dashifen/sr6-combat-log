<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Traits\SessionTrait;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

class LoginAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $session = $this->request->getSessionObj();
    $session->login($this->request->getPostVar('character'));
    $session->set('session-id', $this->request->getPostVar('session-id'));
    header('Location: /session');
  }
}
