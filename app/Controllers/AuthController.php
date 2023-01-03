<?php

namespace App\Controllers;

use App\Models\GeneralFunctions;
use App\Models\User;

require(__DIR__ . '/../Models/GeneralFunctions.php');

class AuthController
{
  private array $user;

  public function __construct(array $data)
  {
    $this->user = array();
    $this->user['email'] = $data['email'] ?? '';
    $this->user['password'] = $data['password'] ?? '';
  }

  public function login()
  {
    $user = new User($this->user);
    $user = $user->login();
    if (is_array($user)) {
      return json_encode($user);
    }
    return GeneralFunctions::generateToken($user);
  }

}