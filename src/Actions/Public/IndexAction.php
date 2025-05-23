<?php

namespace Dashifen\SR6\CombatLog\Actions\Public;

use Dashifen\SR6\CombatLog\Actions\AbstractAction;
use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\field;

class IndexAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $this->combatLog->render('index.twig', [
      'characters' => $this->getPlayers(),
      'tag'        => $this->getRecentSessionTag(),
    ]);
  }
  
  /**
   * Returns the most recent session tag.
   *
   * @return string
   * @throws DatabaseException
   */
  private function getRecentSessionTag(): string
  {
    $query = $this->combatLog->queryFactory
      ->select('tag')
      ->from('sessions')
      ->where(field('ended')->isNull())
      ->orderBy('started', 'desc')
      ->limit(1)
      ->compile();
    
    $results = $this->combatLog->db
      ->execute($query->sql())
      ->quickResults();
    
    return $results['tag'] ?? '';
  }
}
