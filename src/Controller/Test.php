<?php

/**
 * @file
 * Contains \Drupal\conneg_test\Controller\Test.
 */

namespace Drupal\conneg_test\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Test {
  public function simple() {
    return new JsonResponse(['some' => 'data']);
  }

  public function html() {
    return [
      '#markup' => 'here',
    ];
  }

  public function format(Request $request) {
    switch ($request->getRequestFormat()) {
      case 'json':
        return new JsonResponse(['some' => 'data']);

      case 'xml':
        return new Response('<xml></xml>', Response::HTTP_OK, ['Content-Type' => 'application/xml']);
      default:
        var_dump($request->getRequestFormat());
    }
  }

  public function variable($plugin_id) {
    return $plugin_id;
  }
}
