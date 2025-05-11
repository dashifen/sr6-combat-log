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
    $this->combatLog->render('references/edge.twig', [
      'edge' => $this->extractData('edge'),
    ]);
  }
  
  /**
   * Extracts JSON data and returns it as an associative array of headers and
   * the data referenced by those headers.
   *
   * @param string $reference
   *
   * @return array
   */
  protected function extractData(string $reference): array
  {
    $sorted = [];
    $data = parent::extractData($reference);
    
    foreach (['boosts', 'actions'] as $type) {
      foreach ($data[$type] as $expenditure) {
        $sorted[$expenditure['cost']][$type][] = $expenditure;
      }
    }
    
    //self::debug($sorted, true);
    return $sorted;
  }
  
  
}
