<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

use function Latitude\QueryBuilder\field;

class CharactersAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $characters = $this->getCharacters();
    $characters = $this->request->getGetVar('namesOnly') !== 'true'
      ? $this->transformCharactersForState($characters)
      : array_column($characters, 'name');
    
    echo json_encode($characters);
  }
  
  /**
   * Returns an array of character data.
   *
   * @return array
   * @throws DatabaseException
   */
  private function getCharacters(): array
  {
    return !empty($sessionId = $this->request->getSessionVar('id'))
      ? $this->getAllCharacters($sessionId)
      : $this->getPlayerCharacters();
  }
  
  /**
   * Returns all player characters regardless of combat session.
   *
   * @return array
   * @throws DatabaseException
   */
  private function getPlayerCharacters(): array
  {
    $query = $this->combatLog->queryFactory
      ->select('*')
      ->from('characters')
      ->where(field('session_id')->isNull())
      ->compile();
    
    return $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
  }
  
  /**
   * Returns all characters involved in a particular combat session.
   *
   * @param int $sessionId
   *
   * @return array
   * @throws DatabaseException
   */
  private function getAllCharacters(int $sessionId): array
  {
    $query = $this->combatLog->queryFactory
      ->select('*')
      ->from('characters')
      ->where(field('session_id')->eq($sessionId))
      ->orWhere(field('session_id')->isNull())
      ->compile();
    
    return $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
  }
  
  /**
   * Alters the information we selected from the database so that it's in the
   * format expected by our JavaScript application.
   *
   * @param array $characters
   *
   * @return array
   */
  private function transformCharactersForState(array $characters): array
  {
    foreach ($characters as &$character) {
      
      // the data in the database is almost exactly what our JavaScript wants.
      // the only difference is how the DB handles actions.  it stores a single
      // string of zeros for actions remaining and ones for actions taken.  the
      // first bit refers to the major action; the other six are possible minor
      // actions.
      
      $actions = str_split($character['actions']);
      $character['actions'] = [
        'major' => (bool) array_shift($actions),
        'minor' => array_map(fn($x) => (bool) $x, $actions),
      ];
    }
    
    return $characters;
  }
}
