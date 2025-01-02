<?php

namespace Dashifen\SR6\CombatLog;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Error\Error as TwigException;
use Dashifen\SR6\CombatLog\Traits\DebuggingTrait;

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
   * @param Environment|null $twig
   */
  public function __construct(private ?Environment $twig = null)
  {
    $this->projectFolder = dirname(__DIR__);
    $this->templateFolder = $this->projectFolder . '/assets/twigs';
    $this->dataFolder = $this->projectFolder . '/data';
    
    if ($twig === null) {
      $this->twig = new Environment(
        new FilesystemLoader($this->projectFolder . '/assets/twigs'),
        [
          'cache'            => $this->projectFolder . '/assets/twigs/cache',
          'strict_variables' => true,
          'auto_reload'      => true,
        ]
      );
    }
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
  public function render(string $twig, array $context): void
  {
    try {
      echo $this->twig->load($twig)->render($context);
    } catch (TwigException $e) {
      self::catcher($e);
    }
  }
}
