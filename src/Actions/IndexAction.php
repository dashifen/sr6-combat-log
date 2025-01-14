<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\Framework\AbstractAction;

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
      'tag' => $this->getRecentSessionTag(),
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
      ->orderBy('started', 'desc')
      ->limit(1)
      ->compile();
    
    $this->combatLog->db->execute($query->sql());
    $results = $this->combatLog->db->results();
    
    // because we limit our results to a single selection, as long as we have
    // results, we know that we can get a tag from the zeroth index of the
    // array.
    
    return sizeof($results) > 0
      ? $results[0]['tag']
      : '';
  }
}
