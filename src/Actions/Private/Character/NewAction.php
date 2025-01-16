<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\Character;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractPrivateAction;
use Dashifen\SR6\CombatLog\Traits\CharacterTransformationTrait;

use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;

class NewAction extends AbstractPrivateAction
{
  use CharacterTransformationTrait;
  
  /**
   * Executes the behaviors necessary to follow a Route.
   *
   * @return void
   * @throws DatabaseException
   */
  public function execute(): void
  {
    $name = $this->getName();
    $id = $this->insertCharacter($name);
    echo json_encode($this->getCharacter($id));
  }
  
  /**
   * Returns this new character's name.
   *
   * @return string
   * @throws DatabaseException
   */
  private function getName(): string
  {
    $name = $this->request->getGetVar('name');
    
    if (empty($name) || $name === 'undefined') {
      $npcCount = $this->getSessionNPCCount();
      $name = $this->getMetatype() . ' ' . chr(ord('A') + $npcCount);
    }
    
    return $name;
  }
  
  /**
   * Returns the number of NPCs in the current combat session.
   *
   * @return int
   * @throws DatabaseException
   */
  private function getSessionNPCCount(): int
  {
    $query = $this->combatLog->queryFactory
      ->select(alias(func('COUNT', 'character_id'), 'count'))
      ->from('characters')
      ->where(field('type')->eq('npc'))
      ->compile();
    
    $results = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->quickResults();
    
    return $results['count'] ?? 0;
  }
  
  /**
   * Returns a random metatype using information from the Seattle 2072
   * source book.
   *
   * @return string
   */
  private function getMetatype(): string
  {
    $roll = mt_rand(1, 100);
    
    return match (true) {
      $roll <= 66 => 'Human',
      $roll <= 79 => 'Elf',
      $roll <= 78 => 'Dwarf',
      $roll <= 97 => 'Ork',
      $roll <= 99 => 'Troll',
      default     => 'Other',
    };
  }
  
  /**
   * Adds a character to this combat session.
   *
   * @param string $name
   *
   * @return int
   * @throws DatabaseException
   */
  private function insertCharacter(string $name): int
  {
    $query = $this->combatLog->queryFactory
      ->insert('characters')
      ->columns('name', 'session_id')
      ->values($name, $this->request->getSessionVar('id'))
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $this->combatLog->db->lastInsertID();
  }
  
  /**
   * Gets a character out of the database based on its ID.
   *
   * @param int $characterId
   *
   * @return array
   * @throws DatabaseException
   */
  private function getCharacter(int $characterId): array
  {
    $query = $this->combatLog->queryFactory
      ->select('*')
      ->from('characters')
      ->where(field('character_id')->eq($characterId))
      ->compile();
    
    $character = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->quickResults();
    
    return $this->transformCharacter($character);
  }
}
