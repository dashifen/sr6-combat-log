<?php

namespace Dashifen\SR6\CombatLog\Actions;

abstract class AbstractAjaxAction extends AbstractPrivateAction
{
  /**
   * Emits JSON data and exists to end an AJAX action.
   *
   * @param mixed $data
   *
   * @return never
   */
  protected function emitJson(mixed $data): never
  {
    header('Content-type: application/json');
    echo json_encode($data);
    exit;
  }
}
