<?php
namespace App\Controllers;

use App\Models\GeneralFunctions;
use App\Models\User;

class UsersController
{
  private array $user;

  public function __construct(array $data)
  {
    $this->user = array();
    $this->user['id'] = $data['id'] ?? null;
    $this->user['name'] = $data['name'] ?? '';
    $this->user['last_name'] = $data['last_name'] ?? '';
    $this->user['email'] = $data['email'] ?? '';
    $this->user['password'] = $data['password'] ?? '';
  }

  public function create(): \Exception|string|array
  {
    try {
      $new_user = new User($this->user);
      if ($new_user->insert()) {
        return GeneralFunctions::generateToken($new_user);
      }
      return json_encode(array('error' => 'User not added'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function update(): \Exception|string
  {
    try {
      $new_user = new User($this->user);
      if ($new_user->update()) {
        return json_encode(array('message' => "User modified"));
      }
      return json_encode(array('error' => 'User not modified'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function delete($id): \Exception|string
  {
    try {
      $this->user['id'] = $id;
      $new_user = new User($this->user);
      if ($new_user->delete()) {
        return json_encode(array('message' => 'User deleted'));
      }
      return json_encode(array('error' => 'User not deleted'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  static public function searchForId($id): null|string
  {
    try {
      return json_encode(User::getById($id));
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  static public function getAll(): array|\Exception|string
  {
    try {
      return json_encode(User::getAll());
    } catch (\Exception $e) {
      return $e;
    }
  }
}