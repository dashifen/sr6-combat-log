<?php

namespace Dashifen\SR6\CombatLog\Actions\Private;

use Dashifen\SR6\CombatLog\Actions\AbstractAjaxAction;

use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\field;

class EndAction extends AbstractAjaxAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   */
  public function execute(): void
  {
    $query = $this->combatLog->queryFactory
      ->update('sessions', ['ended' => func('CURRENT_TIMESTAMP')])
      ->where(field('tag')->eq($this->request->getSessionVar('tag')))
      ->compile();
    
    try {
      $this->combatLog->db->execute($query->sql(), $query->params());
      $success = $this->combatLog->db->affectedRows() === 1;
      $this->request->getSessionObj()->destroy();
    } catch (DatabaseException) {
      $success = false;
    }
    
    $this->emitJson(['success' => $success]);
  }
}
