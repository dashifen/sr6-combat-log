<?php

namespace Dashifen\SR6\CombatLog\Actions;

use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractReferenceAction extends AbstractPrivateAction
{
  /**
   * Extracts JSON data and returns it as an associative array of headers and
   * the data referenced by those headers.
   *
   * @param string $reference
   *
   * @return array
   */
  protected function extractData(string $reference): array
  {
    $data = $this->combatLog->dataFolder . "/$reference.json";
    $data = json_decode(file_get_contents($data), associative: true);
    return ['headers' => array_keys($data[0] ?? []), 'data' => $data];
  }
}
