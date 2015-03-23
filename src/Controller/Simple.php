<?php

namespace Drupal\conneg_test\Controller;

use Symfony\Component\HttpFoundation\Request;

class Simple {

  public function listing(Request $request = NULL) {
    debug($request);
    return [
      '#markup' => 'here',
    ];
  }
}
