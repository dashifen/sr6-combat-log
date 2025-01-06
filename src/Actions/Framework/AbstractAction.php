<?php

namespace Dashifen\SR6\CombatLog\Actions\Framework;

use JsonException;
use Dashifen\SR6\CombatLog\CombatLog;
use Dashifen\Request\RequestInterface;
use Dashifen\Router\Action\ActionInterface;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;

abstract class AbstractAction implements ActionInterface
{
  use DebuggingTrait;
  
  public function __construct(
    protected CombatLog $combatLog,
    protected RequestInterface $request
  ) {
  }
  
  /**
   * Returns an array containing the list of characters in our data folder.
   *
   * @return array
   */
  protected function getCharacters(): array
  {
    try {
      $json = file_get_contents($this->combatLog->dataFolder . '/characters.json');
      $json = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
      $this->combatLog::catcher($e);
    }
    
    return array_column($json, 'name');
  }
}
