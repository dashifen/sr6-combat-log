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
}
