<?php
namespace App\Models;

use Exception;
use PDO;
use PDOException;

require_once(__DIR__ . '/../Models/GeneralFunctions.php');

abstract class AbstractConnection
{
  private bool $isConnected = false;
  private PDO $connection;

  abstract protected function save(string $query): ?bool;

  public function __construct()
  {

  }

  public function __destruct()
  {
    if ($this->isConnected) {
      $this->Disconnect();
    }
  }

  public function Connect(): void
  {
    $this->isConnected = true;
    try {
      GeneralFunctions::loadEnv(
        ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'],
        ['DB_PORT']
      );
      if (array_search($_ENV['DB_CONNECTION'], PDO::getAvailableDrivers()) !== false) {
        $this->connection = new PDO(
          ($_ENV['DB_CONNECTION'] != "sqlsrv") ? 
          "{$_ENV['DB_CONNECTION']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset={$_ENV['DB_CHAR_SET']}" :
          "{$_ENV['DB_CONNECTION']}:Server={$_ENV['DB_HOST']},{$_ENV['DB_PORT']};database={$_ENV['DB_DATABASE']}",
          $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'],
          array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
        );
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection->setAttribute(PDO::ATTR_PERSISTENT, true);
      } else {
        throw new Exception('Driver de BD no soportado por el servidor');
      }
    } catch (PDOException | Exception $e) {
      $this->isConnected = false;
      print "¡Error!: " . $e->getMessage() . "<br/>";
    }
  }

  public function Disconnect(): void
  {
    unset($this->connection);
    $this->isConnected = false;
  }

  public function isConnected(): bool
  {
    return $this->isConnected;
  }

  public function getRow(string $query, array $params = [])
  {
    try {
      if (!empty($query)) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
      }
      throw new Exception("Consulta vacía o errónea");
    } catch (PDOException | Exception $e) {
      return $e->getMessage();
    }
  }

  public function getRows(string $query, array $params = []): array|string
  {
    try {
      if (!empty($query)) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
      }
      throw new Exception("Consulta vacía o errónea");
    } catch (PDOException | Exception $e) {
      return $e->getMessage();
    }
  }

  public function executeQuery(string $query, array $params = []): ?bool
  {
    try {
      if (!empty($query)) {
        $stmt = $this->connection->prepare($query);
        foreach ($params as $key => $value) {
          $stmt->bindValue($key, $value);
        }
        $this->getStringQuery($query, $params);
        return $stmt->execute();
      }
      throw new Exception("Consulta vacía o errónea");
    } catch (PDOException | Exception $e) {
      return false;
    }
  }

  public function getStringQuery(string $query, array $params): array|string|null
  {
    $keys = array();
    $values = $params;

    foreach ($params as $key => $value) {
      if (is_string($key)) {
        $keys[] = '/' . $key . '/';
      } else {
        $keys[] = '/[?]/';
      }
      if (is_string($value))
        $values[$key] = "'" . $value . "'";

      if (is_array($value))
        $values[$key] = "'" . implode("','", $value) . "'";

      if (is_null($value))
        $values[$key] = 'NULL';
    }
    $query = preg_replace($keys, array_values($values), $query);
    return $query;
  }

}