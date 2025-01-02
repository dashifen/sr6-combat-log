<?php

namespace Dashifen\SR6\CombatLog\Traits;

use Dashifen\Debugging\DebuggingTrait as ParentTrait;

trait DebuggingTrait
{
  use ParentTrait;
  
  /**
   * Returns true because, for now, we're considering this app to always be
   * in a development environment.
   *
   * @return true
   */
  public static function isDebug(): true
  {
    return true;
  }
}
