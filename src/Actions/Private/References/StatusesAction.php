<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\References;

use Dashifen\SR6\CombatLog\Actions\AbstractReferenceAction;

class StatusesAction extends AbstractReferenceAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $data = $this->extractData('statuses');
    $this->combatLog->render('references/statuses.twig', [
      'reference' => 'statuses',
      'statuses'  => [
        'headers' => array_keys($data[0] ?? []),
        'data'    => $data,
      ],
    ]);
  }
  
}
