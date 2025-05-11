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
      : $this->sessionStart();
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
      ->quickResults();
    
    return $results['session_id'] ?? 0;
  }
  
  /**
   * Starts a new combat session.
   *
   * @return int
   * @throws DatabaseException
   */
  private function sessionStart(): int
  {
    $query = $this->combatLog->queryFactory
      ->insert('sessions', ['tag' => uniqid()])
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    $sessionId = $this->combatLog->db->lastInsertID();
    
    // now that the session has been started, we're going to add our PCs to it.
    // for now, we assume all PCs will be a part of the session; the GM can
    // remove them from the screen later if they're not.  note:  our query
    // factory does not have the means to produce an INSERT INTO SELECT FROM
    // query, so we'll have to build this one by hand.
    
    $insert = <<< STATEMENT
      INSERT INTO sessions_characters (character_id, session_id, reaction, intuition, dice, edge)
      SELECT character_id, "$sessionId", reaction, intuition, dice, edge
      FROM characters
      WHERE type=?
    STATEMENT;
    
    $this->combatLog->db->execute($insert, ['pc']);
    return $sessionId;
  }
}
