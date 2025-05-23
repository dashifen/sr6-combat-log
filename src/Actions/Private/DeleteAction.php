<?php

namespace Dashifen\SR6\CombatLog\Actions\Private;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractAjaxAction;
use Dashifen\SR6\CombatLog\Actions\AbstractPrivateAction;

use function Latitude\QueryBuilder\field;

class DeleteAction extends AbstractAjaxAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $sessions = $this->request->getPostVar('sessions', []);
    if (sizeof($sessions) > 0 && $this->isValid($sessions)) {
      if (!$this->delete($sessions)) {
        throw new DatabaseException('Affected rows did not match session count.');
      }
    }
    
    header('Location: /session');
  }
  
  /**
   * Returns true or throws a database exception if $sessions is invalid.
   *
   * @param array $sessions
   *
   * @return true
   * @throws DatabaseException
   */
  private function isValid(array $sessions): true
  {
    $badIds = [];
    foreach ($sessions as $id) {
      if ((int) $id === $id) {
        $badIds[] = $id;
      }
    }
    
    if (sizeof($badIds) > 0) {
      throw new DatabaseException(
        'Invalid session id(s): ' . join(', ', $badIds) . '.',
        DatabaseException::INVALID_DATA
      );
    }
    
    return true;
  }
  
  /**
   * Deletes sessions listed in $sessions from the database.
   *
   * @param array $sessions
   *
   * @return bool
   * @throws DatabaseException
   */
  private function delete(array $sessions): bool
  {
    $count = sizeof($sessions);
    $query = $this->combatLog->queryFactory
      ->delete('sessions')
      ->where(field('session_id')->in(...$sessions))
      ->limit($count)
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $this->combatLog->db->affectedRows() === $count;
  }
}
