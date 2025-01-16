<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Request\RequestInterface;
use Dashifen\Router\Action\ActionInterface;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;
use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\on;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;

abstract class AbstractAction implements ActionInterface
{
  use DebuggingTrait;
  
  public function __construct(
    protected CombatLog $combatLog,
    protected RequestInterface $request
  ) {
  }
  
  /**
   * Returns an array of character data or, perhaps, only their names.
   *
   * @return array
   * @throws DatabaseException
   */
  protected function getSessionCharacters(): array
  {
    $query = $this->combatLog->queryFactory
      ->select('*')
      ->from(alias('characters', 'c'))
      
      // note, the way this join works is like array_merge where the
      // information from the latter table will overwrite matching data in the
      // former one.  so, if a person's reaction and initiative dice are higher
      // than their default during this combat, e.g. due to an increased
      // reflexes spell, the values in sessions_characters that record that
      // modification will be what we see when we execute this select.
      
      ->leftJoin(alias('sessions_characters', 'sc'), on('c.character_id', 'sc.character_id'))
      ->where(field('session_id')->eq($this->request->getSessionVar('id')))
      ->orderBy('score', 'desc')
      ->compile();
    
    return $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
  }
  
  /**
   * Returns a map of character IDs to names for PCs in the database.
   *
   * @return array
   * @throws DatabaseException
   */
  protected function getPlayers(): array
  {
    $query = $this->combatLog->queryFactory
      ->select('character_id', 'name')
      ->from('characters')
      ->where(field('type')->eq('pc'))
      ->compile();
    
    $results = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
    
    return array_combine(
      array_column($results, 'character_id'),
      array_column($results, 'name')
    );
  }
}
