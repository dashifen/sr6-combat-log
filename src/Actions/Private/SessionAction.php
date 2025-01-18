<?php

namespace Dashifen\SR6\CombatLog\Actions\Private;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractPrivateAction;
use Dashifen\SR6\CombatLog\Traits\CharacterTransformationTrait;

class SessionAction extends AbstractPrivateAction
{
  use CharacterTransformationTrait;
  
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $sessionCharacters = $this->getSessionCharacters();
    $sessionCharacterIds = array_column($sessionCharacters, 'character_id');
    $this->combatLog->render('session.twig', [
      'players'    => $this->getPlayers($sessionCharacterIds),
      'characters' => $sessionCharacters,
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
  protected function getSessionCharacters(bool $namesOnly = false): array
  {
    // the transformCharacter method is brought into this object's scope by the
    // use of the CharacterTransformationTrait above.  it handles changes that
    // we need to make between the database and the Vuex state object.  we can
    // apply that transformation to the characters in the database as follows:
    
    return array_map([$this, 'transformCharacter'], parent::getSessionCharacters());
  }
}
