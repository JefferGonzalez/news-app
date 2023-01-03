<?php

namespace App\Interfaces;

use JsonSerializable;

interface Model extends JsonSerializable
{
  function insert();
  function update();
  function delete();
  static function getById(int $id);
  static function getAll();
  static function search(string $query);
}