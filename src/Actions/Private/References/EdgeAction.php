<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\References;

use Dashifen\SR6\CombatLog\Actions\AbstractReferenceAction;

class EdgeAction extends AbstractReferenceAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $this->combatLog->render('references/reference.twig', [
      'statuses'  => $this->extractData('edge'),
      'reference' => 'edge',
    ]);
  }
  
}
