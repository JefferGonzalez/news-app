<?php
namespace App\Models;

require(__DIR__ . '/../../vendor/autoload.php');

use Dotenv\Dotenv;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use Exception;
use Firebase\JWT\JWT;

final class GeneralFunctions
{
  /**
   * @param array $requiredVars
   * @param array $integerVars
   */
  static function loadEnv(array $requiredVars = [], array $integerVars = []): bool
  {
    try {
      $factory = new DotenvFactory([new EnvConstAdapter(), new ServerConstAdapter()]);
      $dotenv = Dotenv::create(__DIR__ . "/../../", null, $factory);
      $dotenv->load();
      $dotenv->required($requiredVars)->notEmpty();
      $dotenv->required($integerVars)->isInteger();
      return true;
    } catch (Exception $re) {
      throw new \RuntimeException($re->getMessage());
    }
  }

  static function generateToken($user): bool|string
  {
    $time = time();
    $payload = [
      'data' => $user->getId(),
      'iat' => $time,
      'exp' => $time + 60 * 60 * 24
    ];
    GeneralFunctions::loadEnv(['SECRET_KEY']);
    $jwt = JWT::encode($payload, $_ENV['SECRET_KEY'], 'HS256');
    return json_encode(array('token' => $jwt, 'data' => $user->jsonSerialize()));
  }
}