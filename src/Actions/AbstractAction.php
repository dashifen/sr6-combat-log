<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Latitude\QueryBuilder\Query;
use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Request\RequestInterface;
use Dashifen\Router\Action\ActionInterface;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;
use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\field;

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
   * @param bool $namesOnly
   *
   * @return array
   * @throws DatabaseException
   */
  protected function getCharacters(bool $namesOnly = false): array
  {
    $query = !empty($sessionId = $this->request->getSessionVar('id'))
      ? $this->getSessionCharacterQuery($sessionId)
      : $this->getPlayerCharacterQuery();
    
    $characters = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
    
    return $namesOnly ? array_column($characters, 'name') : $characters;
  }
  
  /**
   * Returns a Query object that selects all characters in a combat session.
   *
   * @param int $sessionId
   *
   * @return Query
   */
  protected function getSessionCharacterQuery(int $sessionId): Query
  {
    return $this->combatLog->queryFactory
      ->select('*')
      ->from('characters')
      ->where(field('session_id')->eq($sessionId))
      ->orWhere(field('session_id')->isNull())
      ->compile();
  }
  
  /**
   * Returns a Query object that selects only the player characters.
   *
   * @return Query
   */
  protected function getPlayerCharacterQuery(): Query
  {
    return $this->combatLog->queryFactory
      ->select('*')
      ->from('characters')
      ->where(field('type')->eq('pc'))
      ->compile();
  }
  
  /**
   * A convenience method that calls getCharacters above and passes it the
   * true flag so it returns only character names.
   *
   * @return array
   * @throws DatabaseException
   */
  protected function getCharacterNames(): array
  {
    return $this->getCharacters(true);
  }
}
