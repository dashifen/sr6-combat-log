<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\References;

use JetBrains\PhpStorm\ArrayShape;
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
    $minor = [];
    $major = [];
    $data = parent::extractData($reference)['data'];
    
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
