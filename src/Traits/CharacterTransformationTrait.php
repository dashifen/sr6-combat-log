<?php

namespace Dashifen\SR6\CombatLog\Traits;

trait CharacterTransformationTrait
{
  /**
   * Handles any transformation needed when moving a character from the
   * database into the format expected by our JavaScript application.
   *
   * @param array $character
   *
   * @return array
   */
  protected function transformCharacter(array $character): array {
    $actions = str_split($character['actions']);
    $character['actions'] = [
      'major' => (bool) array_shift($actions),
      'minor' => array_map(fn($x) => (bool) $x, $actions),
    ];
    
    return $character;
  }
}
