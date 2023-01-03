<?php

namespace App\Models;

use App\Interfaces\Model;
use Exception;

require_once('AbstractConnection.php');
require_once(__DIR__ . '/../Interfaces/Model.php');

class News extends AbstractConnection implements Model
{
  private ?int $id;
  private string $title;
  private string $description;
  private string $date;
  private ?int $userId;

  private ?User $user;

  public function __construct(array $news = [])
  {
    parent::__construct();
    $this->setId($news['id'] ?? null);
    $this->setTitle($news['title'] ?? '');
    $this->setDescription($news['description'] ?? '');
    $this->setDate((empty($news['date'])) ? date("Y-m-d H:i:s") : $news['date']);
    $this->setUserId($news['user_id'] ?? null);
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
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription(string $description): void
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDate(): string
  {
    return $this->date;
  }

  /**
   * @param string $date
   */
  public function setDate(string $date): void
  {
    $this->date = $date;
  }

  /**
   * @return int|null
   */
  public function getUserId(): ?int
  {
    return $this->userId;
  }

  /**
   * @param int|null $userId
   */
  public function setUserId(?int $userId): void
  {
    $this->userId = $userId;
  }

  /**
   * @return User|null
   */
  public function getUser(): ?User
  {
    if (!empty($this->userId)) {
      $this->user = User::getById($this->userId);
      return $this->user;
    }
    return null;
  }


  protected function save(string $query): ?bool
  {
    $params = [
      ':id' => $this->getId(),
      ':title' => $this->getTitle(),
      ':description' => $this->getDescription(),
      ':date' => $this->getDate(),
      ':user_id' => $this->getUserId()
    ];
    $this->Connect();
    $result = $this->executeQuery($query, $params);
    $this->Disconnect();
    return $result;
  }

  public function insert(): ?bool
  {
    $query = "INSERT INTO news (id,title,description,date,user_id) VALUES (:id,:title,:description,:date,:user_id)";
    return $this->save($query);
  }

  public function update(): ?bool
  {
    $query = "UPDATE news SET title = :title , description = :description , date = :date, user_id = :user_id WHERE id = :id";
    return $this->save($query);
  }

  public function delete(): ?bool
  {
    $query = "DELETE FROM news WHERE id = :id";
    $this->Connect();
    $result = $this->executeQuery($query, array(":id" => $this->getId()));
    $this->Disconnect();
    return $result;
  }

  public static function search(string $query): array|string
  {
    try {
      $array = array();
      $news = new News();
      $news->Connect();
      $getRows = $news->getRows($query);
      $news->Disconnect();
      if (!empty($getRows)) {
        foreach ($getRows as $valor) {
          $News = new News($valor);
          array_push($array, $News);
          unset($News);
        }
        return array('data' => $array);
      } else {
        return array('message' => 'No records found');
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public static function getById(int $id): News|null|array
  {
    if ($id > 0) {
      $news = new News();
      $news->Connect();
      $getRow = $news->getRow("SELECT * FROM news WHERE id =?", array($id));
      $news->Disconnect();
      return ($getRow) ? new News($getRow) : null;
    } else {
      return array('message' => 'Id not valid');
    }
  }

  public static function getAll(): array|string
  {
    return News::search("SELECT * FROM news");
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->getId(),
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'date' => $this->getDate(),
      'author' => strtok($this->getUser()->getName(), ' ') . ' ' . strtok($this->getUser()->getLastName(), ' '),
    ];
  }

}