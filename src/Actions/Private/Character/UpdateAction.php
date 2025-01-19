<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\Character;

use Dashifen\SR6\CombatLog\Actions\AbstractAjaxAction;

use Dashifen\SR6\CombatLog\Database\DatabaseException;

use function Latitude\QueryBuilder\field;

class UpdateAction extends AbstractAjaxAction
{
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    [$characterId, $values] = $this->extractQueryParams();
    
    $query = $this->combatLog->queryFactory
      ->update('sessions_characters', $values)
      ->where(field('session_id')->eq($this->request->getSessionVar('id')))
      ->andWhere(field('character_id')->eq($characterId))
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    $success = $this->combatLog->db->affectedRows() === 1;
    $this->emitJson(['success' => $success]);
  }
  
  /**
   * Grabs data out of the post vars so we can compile our query.
   *
   * @return array
   */
  private function extractQueryParams(): array
  {
    $post = $this->request->getPost();
    $characterId = $post['character_id'];
    unset($post['character_id']);
    
    // empty values seem to come from the client as the string "null" instead
    // of an actual empty string.  so, here we'll fix that because we don't
    // want to save "null" in the database.
    
    foreach($post as &$value) {
      if ($value === 'null') {
        $value = '';
      }
    }
    
    return [$characterId, $post];
  }
}
