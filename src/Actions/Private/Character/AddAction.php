<?php

namespace Dashifen\SR6\CombatLog\Actions\Private\Character;

use Dashifen\SR6\CombatLog\Database\DatabaseException;
use Dashifen\SR6\CombatLog\Actions\AbstractAjaxAction;
use Dashifen\SR6\CombatLog\Traits\CharacterTransformationTrait;

use function Latitude\QueryBuilder\on;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;

class AddAction extends AbstractAjaxAction
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
    // the action expects a query string variable named character.  that will
    // be either a number representing a player character, the name of a new
    // NPC, or an empty string indicating that we want a random grunt.
    
    $character = $this->request->getGetVar('character');
    
    $id = is_numeric($character)
      ? $this->addCharacter($character)
      : $this->addNpc($character);
    
    $this->emitJson($this->getCharacter($id));
  }
  
  /**
   * Adds the specified player character to this combat session.
   *
   * @param int $characterId
   *
   * @return int
   * @throws DatabaseException
   */
  private function addCharacter(int $characterId): int
  {
    $query = $this->combatLog->queryFactory
      ->insert('sessions_characters')
      ->columns('session_id', 'character_id')
      ->values($this->request->getSessionVar('id'), $characterId)
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $characterId;
  }
  
  /**
   * Adds a non-player character to this session.
   *
   * @param string $name
   *
   * @return int
   * @throws DatabaseException
   */
  private function addNpc(string $name): int
  {
    // if $name isn't empty, we can insert it directly using the method below.
    // otherwise, we'll call the getName method first and pass its results to
    // insertCharacter instead.  once this character is inserted, we'll add it
    // to this session by calling the message above.
    
    $name = $name ?: $this->getName();
    $characterId = $this->insertCharacter($name);
    return $this->addCharacter($characterId);
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
      ->columns('name')
      ->values($name)
      ->compile();
    
    $this->combatLog->db->execute($query->sql(), $query->params());
    return $this->combatLog->db->lastInsertID();
  }
  
  /**
   * Returns this new character's name.
   *
   * @return string
   * @throws DatabaseException
   */
  private function getName(): string
  {
    $npcCount = $this->getSessionNPCCount();
    return sprintf('%s %s', $this->getMetatype(), chr(ord('A') + $npcCount));
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
      ->from(alias('characters', 'c'))
      ->leftJoin(alias('sessions_characters', 'sc'), on('c.character_id', 'sc.character_id'))
      ->where(field('session_id')->eq($this->request->getSessionVar('id')))
      ->andWhere(field('sc.character_id')->eq($characterId))
      ->orderBy('score', 'desc')
      ->compile();
    
    $character = $this->combatLog->db
      ->execute($query->sql(), $query->params())
      ->quickResults();
    
    return $this->transformCharacter($character);
  }
}
