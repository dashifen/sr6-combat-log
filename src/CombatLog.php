<?php

namespace Dashifen\SR6\CombatLog;

use Dotenv\Dotenv;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use Twig\Error\Error as TwigException;
use Dashifen\Session\SessionInterface;
use Latitude\QueryBuilder\QueryFactory;
use Dashifen\SR6\CombatLog\Database\Database;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;
use Dashifen\SR6\CombatLog\Database\DatabaseException;

class CombatLog
{
  use DebuggingTrait;
  
  private(set) string $projectFolder;
  private(set) string $templateFolder;
  private(set) string $dataFolder;
  
  /**
   * CombatLog constructor which sets the folder properties and creates a
   * default twig Environment when one is not provided for it.
   *
   * @param SessionInterface  $session
   * @param Environment|null  $twig
   * @param Database|null     $db
   * @param QueryFactory|null $queryFactory
   *
   * @throws DatabaseException
   */
  public function __construct(
    private readonly SessionInterface $session,
    private ?Environment $twig = null,
    private(set) ?Database $db = null,
    private(set) ?QueryFactory $queryFactory = null
  ) {
    $this->projectFolder = dirname(__DIR__);
    $this->templateFolder = $this->projectFolder . '/assets/twigs';
    $this->dataFolder = $this->projectFolder . '/assets/data';
    
    // theoretically, we could pass these data into the log.  it's unlikely
    // that we ever will, but just in case we do, we'll use the null coalescing
    // assignment operator to set them to reasonable defaults.
    
    $this->queryFactory ??= new QueryFactory(new MySqlEngine());
    $this->twig ??= $this->produceTwigEnvironment();
    $this->db ??= $this->produceDatabase();
  }
  
  /**
   * Instantiates and returns a Twig Environment for our use in this app.
   *
   * @return Environment
   */
  private function produceTwigEnvironment(): Environment
  {
    return new Environment(
      new FilesystemLoader($this->templateFolder),
      [
        'cache'            => $this->templateFolder . '/cache',
        'strict_variables' => true,
        'auto_reload'      => true,
      ]
    );
  }
  
  /**
   * Instantiates and returns a lazy-connecting MySQL database object for our
   * use within this app.
   *
   * @return Database
   * @throws DatabaseException
   */
  private function produceDatabase(): Database
  {
    Dotenv::createImmutable($this->projectFolder)->load();
    return new Database($_ENV['DB'], $_ENV['USER'], $_ENV['PASS']);
  }
  
  /**
   * Given the name of a twig template file and the context that should be used
   * when compiling it, render the template and emit the results.
   *
   * @param string $twig
   * @param array  $context
   *
   * @return void
   */
  public function render(string $twig, array $context = []): void
  {
    try {
      
      // in addition to the information that is sent here in the $context
      // array, there are a few things that we want to add ourselves.  these
      // are, in essence, contextual data that we need everywhere, not just on
      // a particular page.  once we merge in most of the information, we add
      // one more item:  a dump of the $context array itself.  this is so we
      // can see it in the DOM and, one day, we'll get rid of that.
      
      $context = array_merge($context, [
        'cssVersion' => filemtime($this->projectFolder . '/assets/styles.css'),
        'username'   => $this->session->getUsername(),
        'session'    => $this->session->getSession(),
      ]);
      
      $context['context'] = print_r($context, true);
      echo $this->twig->load($twig)->render($context);
    } catch (TwigException $e) {
      self::catcher($e);
    }
  }
}
