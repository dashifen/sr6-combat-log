<?php

namespace Dashifen\SR6\CombatLog\Actions;

use Dashifen\Exception\Exception;

class ActionException extends Exception
{
  public const int INAUTHENTIC = 1;
}
