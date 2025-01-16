<?php

namespace Dashifen\SR6\CombatLog\Actions\Public;

use Dashifen\SR6\CombatLog\Actions\AbstractAction;
use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\field;

class LoginAction extends AbstractAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $session = $this->request->getSessionObj();
    $session->login($this->request->getPostVar('character'));
    
    // notice that we assign the tag value posted here to the local $tag
    // variable and store the results of that assignment in the session by side
    // effect.  we then pass that same value to the getSessionId method below.
    // finally, since this action has no display, we redirect to the page that
    // shows information about the current combat session.
    
    $session->set('tag', ($tag = $this->request->getPostVar('tag')));
    $session->set('id', $this->getSessionId($tag));
    header('Location: /session');
  }
  
  /**
   * Inserts the session tag into the database and returns the inserted id.
   *
   * @param string $tag
   *
   * @return int
   * @throws DatabaseException
   */
  private function getSessionId(string $tag): int
  {
    return !empty($tag)
      ? $this->sessionReconnect($tag)
      : $this->sessionStart($tag);
  }
  
  /**
   * Reconnects to an existing combat session.
   *
   * @param string $tag
   *
   * @return int
   * @throws DatabaseException
   */
  private function sessionReconnect(string $tag): int
  {
    $query = $this->combatLog->queryFactory
      ->select('session_id')
      ->from('sessions')
      ->where(field('tag')->eq($tag))
      ->compile();
    
    $results = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->results();
    
    // because tags are unique, we know that if we have data for this session
    // then we have only one index (the zeroth) in our results.  otherwise, we
    // just return zero.  theoretically, we should only be in a situation that
    // would produce a zero if someone deletes a session while someone else is
    // logging in, and we'll just try not to do that.
    
    return sizeof($results) > 0
      ? $results[0]['session_id']
      : 0;
  }
  
  /**
   * Starts a new combat session.
   *
   * @param string $tag
   *
   * @return int
   * @throws DatabaseException
   */
  private function sessionStart(string $tag): int
  {
    $query = $this->combatLog->queryFactory
      ->insert('sessions', ['tag' => $tag])
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $this->combatLog->db->lastInsertID();
  }
}
