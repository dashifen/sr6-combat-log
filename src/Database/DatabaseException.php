<?php

namespace Dashifen\SR6\CombatLog\Database;

use Throwable;
use Dashifen\Exception\Exception;

class DatabaseException extends Exception
{
  public const int CANNOT_CONNECT    = 1;
  public const int CANNOT_PREPARE    = 2;
  public const int INVALID_PARAMETER = 3;
  public const int MUST_EXECUTE      = 4;
  public const int INVALID_MODE      = 5;
  
  /**
   * DatabaseException constructor
   *
   * Sends most of the parameters to our parent and adds the query property
   * via promotion.
   *
   * @param string         $message
   * @param int|string     $code
   * @param Throwable|null $previous
   * @param string         $query
   */
  public function __construct(
    string $message = "",
    int|string $code = 0,
    ?Throwable $previous = null,
    protected(set) string $query = ''
  ) {
    parent::__construct($message, $code, $previous);
  }
}
