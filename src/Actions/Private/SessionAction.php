<?php

namespace Dashifen\SR6\CombatLog\Actions\Private;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractPrivateAction;

class SessionAction extends AbstractPrivateAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $this->combatLog->render('session.twig', [
      'characters' => $this->getCharacters()
    ]);
  }
  
  /**
   * Returns all characters involved in a particular combat session.
   *
   * @param bool $namesOnly
   *
   * @return array
   * @throws DatabaseException
   */
  protected function getCharacters(bool $namesOnly = false): array
  {
    $characters = parent::getCharacters($namesOnly);
    
    // the data in the database is almost exactly what our JavaScript wants.
    // the only difference is how the DB handles actions.  it stores a single
    // string of zeros for actions remaining and ones for actions taken.  the
    // first bit refers to the major action; the other six are possible minor
    // actions.  we convert those to booleans that Vue uses as the v-model for
    // checkboxes on-screen.
    
    foreach ($characters as &$character) {
      $actions = str_split($character['actions']);
      $character['actions'] = [
        'major' => (bool) array_shift($actions),
        'minor' => array_map(fn($x) => (bool) $x, $actions),
      ];
    }
    
    return $characters;
  }
}
