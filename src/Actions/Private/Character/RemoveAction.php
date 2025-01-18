<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\Character;

use Latitude\QueryBuilder\Query;
use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractAjaxAction;

use function Latitude\QueryBuilder\field;

class RemoveAction extends AbstractAjaxAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $query = $this->request->getPostVar('from', 'session') === 'session'
      ? $this->removeCharacter()
      : $this->deleteCharacter();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    $success = $this->combatLog->db->affectedRows() === 1;
    $this->emitJson(['success' => $success]);
  }
  
  /**
   * Removes a character from the current combat session.
   *
   * @return Query
   */
  private function removeCharacter(): Query
  {
    return $this->combatLog->queryFactory
      ->delete('sessions_characters')
      ->where(field('character_id')->eq($this->request->getPostVar('character_id')))
      ->andWhere(field('session_id')->eq($this->request->getSessionVar('id')))
      ->compile();
  }
  
  /**
   * Deletes an NPC from the database.
   *
   * @return Query
   */
  private function deleteCharacter(): Query
  {
    return $this->combatLog->queryFactory
      ->delete('characters')
      ->where(field('character_id')->eq($this->request->getPostVar('character_id')))
      ->andWhere(field('type')->eq('npc'))
      ->compile();
  }
}
