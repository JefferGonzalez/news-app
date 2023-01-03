<?php

namespace App\Controllers;
require(__DIR__ . '/../../vendor/autoload.php');

header("Access-Control-Allow-Origin: http://localhost:5173");

if (!empty($_GET['controller'])) {
  $controller = 'App\\Controllers\\' . ucfirst($_GET['controller']) . 'Controller';
  if (class_exists($controller)) {
    $tmpController = new $controller($_POST);
    if (!empty($_GET['action']) and method_exists($tmpController, $_GET['action'])) {
      header('Content-type: application/json; charset=utf-8');
      switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
          if ($_GET['action'] === 'getAll') {
            echo $tmpController->{$_GET['action']}();
            break;
          } else if (!empty($_GET['id'])) {
            echo $tmpController->{$_GET['action']}($_GET['id']);
            break;
          }
          header('HTTP/1.1 404 Not found parameters');
          break;
        case 'POST':
          echo $tmpController->{$_GET['action']}();
          break;
        case 'DELETE':
          if (!empty($_GET['id'])) {
            header('HTTP/1.1 204 No Content');
            echo $tmpController->{$_GET['action']}($_GET['id']);
            break;
          }
          header('HTTP/1.1 404 Not found parameters');
          break;
        default:
          header('HTTP/1.1 405 Method not allowed');
          header('Allow: GET, POST, DELETE');
          break;
      }
    } else {
      header('HTTP/1.1 404 Not found parameters');
    }
  } else {
    header('HTTP/1.1 400 Bad Request');
  }
} else {
  header('HTTP/1.1 202 Request without parameters');
}