<?php

namespace App\Models;

use App\Interfaces\Model;
use Exception;

require_once('AbstractConnection.php');
require_once(__DIR__ . '/../Interfaces/Model.php');

class User extends AbstractConnection implements Model
{
  private ?int $id;
  private string $name;
  private string $lastName;
  private string $email;
  private string $password;

  private ?array $news;

  const HASH = PASSWORD_DEFAULT;
  const COST = 10;

  public function __construct(array $user = [])
  {
    parent::__construct();
    $this->setId($user['id'] ?? null);
    $this->setName($user['name'] ?? '');
    $this->setLastName($user['last_name'] ?? '');
    $this->setEmail($user['email'] ?? '');
    $this->setPassword($user['password'] ?? '');
  }

  function __destruct()
  {
    if ($this->isConnected()) {
      $this->Disconnect();
    }
  }

  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * @param int|null $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName): void
  {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * @param string $password
   */
  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  /**
   * @return array|null
   */
  public function getNews(): ?array
  {
    $this->news = News::search("SELECT * FROM news WHERE user_id = $this->id");
    return $this->news;
  }

  public function login(): User|array
  {
    $user = User::search("SELECT * FROM users WHERE email = '$this->email'");
    if (!empty($user)) {
      if (array_key_exists('message', $user) and $user['message'] === 'No records found') {
        return array('error' => 'User or password incorrect');
      }
      $password = $user['data'][0]->getPassword();
      if (password_verify($this->password, $password)) {
        return $user['data'][0];
      }
    }
    return array('error' => 'User or password incorrect');
  }

  protected function save(string $query): ?bool
  {
    if ($this->password != null) {
      $pos = strpos($this->password, '$2y$10$');
      if ($pos === false) {
        $hashPassword = password_hash($this->password, self::HASH, ['cost' => self::COST]);
      } else {
        $hashPassword = $this->password;
      }
    } else {
      $hashPassword = null;
    }

    $params = [
      ':id' => $this->getId(),
      ':name' => $this->getName(),
      ':last_name' => $this->getLastName(),
      ':email' => $this->getEmail(),
      ':password' => $hashPassword
    ];
    $this->Connect();
    $result = $this->executeQuery($query, $params);
    $this->Disconnect();
    return $result;
  }

  public function insert(): ?bool
  {
    $query = "INSERT INTO users (id,name,last_name,email,password) VALUES (:id,:name,:last_name,:email, :password)";
    return $this->save($query);
  }

  public function update(): ?bool
  {
    $query = "UPDATE users SET name = :name , last_name = :last_name , email = :email, password = :password WHERE id = :id";
    return $this->save($query);
  }

  public function delete(): ?bool
  {
    $query = "DELETE FROM users WHERE id = :id";
    $this->Connect();
    $result = $this->executeQuery($query, array(":id" => $this->getId()));
    $this->Disconnect();
    return $result;
  }

  public static function search(string $query): array|string
  {
    try {
      $array = array();
      $user = new User();
      $user->Connect();
      $getRows = $user->getRows($query);
      $user->Disconnect();
      if (!empty($getRows)) {
        foreach ($getRows as $valor) {
          $User = new User($valor);
          array_push($array, $User);
          unset($User);
        }
        return array('data' => $array);
      } else {
        return array('message' => 'No records found');
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public static function getById(int $id): User|null|array
  {
    if ($id > 0) {
      $user = new User();
      $user->Connect();
      $getRow = $user->getRow("SELECT * FROM users WHERE id =?", array($id));
      $user->Disconnect();
      return ($getRow) ? new User($getRow) : null;
    } else {
      return array('message' => 'Id not valid');
    }
  }

  public static function getAll(): array|string
  {
    return User::search("SELECT * FROM users");
  }

  public function jsonSerialize(): mixed
  {
    return [
      'username' => strtok($this->getName(), ' ') . ' ' . strtok($this->getLastName(), ' '),
      'email' => $this->getEmail(),
      'news' => ($this->id) ? $this->getNews() : [],
    ];
  }

}