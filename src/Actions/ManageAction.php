<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractPrivateAction;

use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\param;
use function Latitude\QueryBuilder\alias;

class ManageAction extends AbstractPrivateAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $this->combatLog->render('manage.twig', [
      'sessions' => $this->getSessions()
    ]);
  }
  
  /**
   * Returns the list of sessions in the database.
   *
   * @return array
   * @throws DatabaseException
   */
  private function getSessions(): array
  {
    $query = $this->combatLog->queryFactory
      ->select('session_id', 'tag')
      ->addColumns(alias(func('DATE_FORMAT', 'started', param('%M %e, %Y')), 'date'))
      ->from('sessions')
      ->orderBy('started', 'desc')
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $this->combatLog->db->results();
  }
}
