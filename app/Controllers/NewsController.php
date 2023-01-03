<?php
namespace App\Controllers;

use App\Models\News;

class NewsController
{
  private array $news;

  public function __construct(array $data)
  {
    $this->news = array();
    $this->news['id'] = $data['id'] ?? null;
    $this->news['title'] = $data['title'] ?? '';
    $this->news['description'] = $data['description'] ?? '';
    $this->news['date'] = $data['date'] ?? '';
    $this->news['user_id'] = $data['user_id'] ?? null;
  }

  public function create(): \Exception|string|array
  {
    try {
      $new_news = new News($this->news);
      if ($new_news->insert()) {
        return json_encode(array('message' => 'News created successfully'));
      }
      return json_encode(array('error' => 'News not added'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function update(): \Exception|string
  {
    try {
      $new_news = new News($this->news);
      if ($new_news->update()) {
        return json_encode(array('message' => "News modified"));
      }
      return json_encode(array('error' => 'News not modified'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function delete($id): \Exception|string
  {
    try {
      $this->news['id'] = $id;
      $new_news = new News($this->news);
      if ($new_news->delete()) {
        return json_encode(array('message' => 'News deleted'));
      }
      return json_encode(array('error' => 'News not deleted'));
    } catch (\Exception $e) {
      return $e;
    }
  }

  static public function searchForId($id): array|null|string
  {
    try {
      return json_encode(News::getById($id));
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  static public function getAll(): array|\Exception|string
  {
    try {
      return json_encode(News::getAll());
    } catch (\Exception $e) {
      return $e;
    }
  }
}