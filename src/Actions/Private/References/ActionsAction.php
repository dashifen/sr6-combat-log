<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\References;

use Dashifen\SR6\CombatLog\Actions\AbstractReferenceAction;

class ActionsAction extends AbstractReferenceAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $this->combatLog->render('references/actions.twig', [
      'actions' => $this->extractData('actions'),
    ]);
  }
  
  /**
   * Returns the data from our actions.json reference.
   *
   * @param string $reference
   *
   * @return array
   */
  protected function extractData(string $reference): array
  {
    $minor = [];  // declared here so we can use variable variables in the
    $major = [];  // loop below to fill these arrays with actions.
    
    $data = parent::extractData($reference);
    foreach (['minor','major'] as $type) {
      foreach($data[$type] as $action) {
        $$type[strtolower($action['type'])][] = $action;
      }
    }
    
    return [
      'minor' => $minor,
      'major' => $major,
    ];
  }
}
