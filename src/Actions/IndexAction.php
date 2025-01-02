<?php

namespace Dashifen\SR6\CombatLog\Actions;

use JsonException;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

class IndexAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $this->combatLog->render('index.twig', $this->getCharacters());
  }
  
  /**
   * Returns an array containing the list of characters in our data folder.
   *
   * @return array
   */
  private function getCharacters(): array
  {
    try {
      $json = file_get_contents($this->combatLog->dataFolder . '/characters.json');
      $json = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
      $this->combatLog::catcher($e);
    }
    
    // we've arranged the JSON in the characters.json file such that it
    // produces an array with one index:  characters.  that's exactly what our
    // twig template expects, so we can return it without modification here.
    
    return $json;
  }
}
