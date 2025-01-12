<?php

namespace Dashifen\SR6\CombatLog\Database;

use mysqli;
use mysqli_stmt;
use NumberFormatter;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;

class Database
{
  use DebuggingTrait;
  
  protected mysqli $connection;
  protected mysqli_stmt|bool $query;
  
  /**
   * Database constructor.
   *
   * @param string $database
   * @param string $username
   * @param string $password
   * @param string $host
   * @param string $charset
   *
   * @throws DatabaseException
   */
  public function __construct(
    string $database,
    string $username,
    string $password,
    string $host = 'localhost',
    string $charset = 'utf8mb4',
  ) {
    $this->connection = new mysqli($host, $username, $password, $database);
    
    if ($this->connection->connect_error) {
      throw new DatabaseException(
        "Cannot connect to $host:$database with supplied credentials.",
        DatabaseException::CANNOT_CONNECT
      );
    }
    
    $this->connection->set_charset($charset);
  }
  
  /**
   * Closes the current query (if there is one) and disconnects from the
   * database.
   */
  public function __destruct()
  {
    $this->maybeCloseQuery();
    $this->connection->close();
  }
  
  /**
   * As needed, closes a current query.
   *
   * @return void
   */
  private function maybeCloseQuery(): void
  {
    if (isset($this->query) && is_a($this->query, mysqli_stmt::class)) {
      $this->query->close();
    }
  }
  
  /**
   * Executes a query.
   *
   * @param string $query
   * @param array  $parameters
   *
   * @return Database
   * @throws DatabaseException
   */
  public function execute(string $query, array $parameters = []): Database
  {
    $this->maybeCloseQuery();
    
    // here we try to prepare our statement.  if we can't, we throw and
    // exception and let the calling scope handle it.  notice that we send in
    // the query that broke things so that devs know which one caused the
    // problem.
    
    $this->query = $this->connection->prepare($query);
    if ($this->query === false) {
      throw new DatabaseException(
        'Unable to prepare MySQL statement - ' . $this->connection->error,
        DatabaseException::CANNOT_PREPARE,
        query: $query
      );
    }
    
    // it's possible that there are no parameters to bind to our statement.
    // but, if there are, then we need to identify their types so that we can
    // correctly use the bind_params method of the mysqli statement object.
    
    if (sizeof($parameters) > 0) {
      $this->query->bind_param(
        $this->getTypes($parameters),
        ...array_values($parameters)
      );
    }
    
    $this->query->execute();
    return $this;
  }
  
  /**
   * Returns a string made up of s, d, and i characters corresponding to
   * parameters of string, float, and integer types.
   *
   * @param array $parameters
   *
   * @return string
   * @throws DatabaseException
   */
  private function getTypes(array $parameters): string
  {
    $types = '';
    try {
      foreach ($parameters as $i => $parameter) {
        $types .= match (true) {
          is_int($parameter)    => 'i',
          is_float($parameter)  => 'd',
          is_string($parameter) => 's',
          
          // it's very possible that if we're here that $parameter won't be able
          // to become a string.  that's why we just use $i to reference which
          // caused the problem and then hope the developers can work from there
          // to find the issue.
          
          default               => throw new DatabaseException(
            sprintf('Invalid %s parameter.', new NumberFormatter('en-us', NumberFormatter::ORDINAL)->format($i + 1)),
            DatabaseException::INVALID_PARAMETER
          )
        };
      }
    } catch (DatabaseException $e) {
      
      // if we ran into an issue in the try-block, we'll dump our parameters
      // to the screen and then re-throw the same exception so we can fix
      // whatever went wrong.
      
      self::debug($parameters);
      throw $e;
    }
    
    return $types;
  }
  
  /**
   * Returns an array of the results of a previously executed query.
   *
   * @param int $mode
   *
   * @return array
   * @throws DatabaseException
   */
  public function results(int $mode = MYSQLI_ASSOC): array
  {
    if (!is_a($this->query, mysqli_stmt::class)) {
      throw new DatabaseException('Execute query before accessing results.',
        DatabaseException::MUST_EXECUTE);
    }
    
    if (!in_array($mode, [MYSQLI_NUM, MYSQLI_ASSOC, MYSQLI_BOTH])) {
      throw new DatabaseException('Invalid mode: ' . $mode,
        DatabaseException::INVALID_MODE);
    }
    
    $result = $this->query->get_result();
    $results = $result->fetch_all($mode);
    $result->free();
    
    // here we do something a little weird:  if there's exactly one row to
    // return, we simply return it rather the encapsulating array.  otherwise,
    // we'll just return everything.
    
    return sizeof($results) === 1 ? $results[0] : $results;
  }
  
  /**
   * Returns the number of rows changed, deleted, inserted, or matched by our
   * query.
   *
   * @return int
   */
  public function affectedRows(): int
  {
    $this->query->store_result();
    return $this->query->affected_rows;
  }
  
  /**
   * Returns the ID created by an INSERT query.
   *
   * @return int|string
   */
  public function lastInsertID(): int|string
  {
    return $this->connection->insert_id;
  }
}
