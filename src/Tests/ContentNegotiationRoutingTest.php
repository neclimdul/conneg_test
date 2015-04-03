<?php

/**
 * @file
 * Contains \Drupal\conneg_test\Tests\ContentNegotiationRoutingTest.
 */

namespace Drupal\conneg_test\Tests;

use Drupal\simpletest\KernelTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests content negotiation routing variations.
 *
 * @group conneg
 */
class ContentNegotiationRoutingTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system', 'conneg_test'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    \Drupal::unsetContainer();
    parent::setUp();

    $this->installSchema('system', ['router', 'url_alias']);
    \Drupal::service('router.builder')->rebuild();
  }

  function testContentRouting() {
    $tests = [
      // ['path', 'accept', 'content-type'],

      // Case 1 - Extension is part of the route path. Constant Content-type.
      ['conneg/simple.json', '', 'application/json'],
      ['conneg/simple.json', 'application/xml', 'application/json'],
      ['conneg/simple.json', 'application/json', 'application/json'],

      // Case 2 - No extension. Constant Content-type.
//      ['conneg/html', 'application/xml', NULL],
      ['conneg/html', '', 'text/html'],
      ['conneg/html', 'text/xml', 'text/html'],
      ['conneg/html', 'text/html', 'text/html'],

      // Case 3 - Dynamic extension. Linked Content-type.
      ['conneg/html.json', '', 'application/json'],
      ['conneg/html.json', 'application/xml', 'application/json'],
      ['conneg/html.json', 'application/json', 'application/json'],

      ['conneg/html.xml', '', 'application/xml'],
      ['conneg/html.xml', 'application/json', 'application/xml'],
      ['conneg/html.xml', 'application/xml', 'application/xml'],
      // @TODO a Drupal protocol?

      // Case 4 - Alias with extension pointing to no extension/constant content-type.
      // @TODO how to test aliasing? Can we just assume aliasing does its thing?

      // Case 5 - Alias with extension pointing to dynamic extension/linked content-type.
      // @TODO how to test aliasing? Can we just assume aliasing does its thing?

      // Case 6 - Alias with extension pointing to dynamic extension/linked content-type.
      // @TODO how to test aliasing? Can we just assume aliasing does its thing?
      // this might not even work :(

      // Case 7 - Path with a variable. Variable contains a period.
      ['conneg/plugin/plugin.id', '', 'text/html'],
      ['conneg/plugin/plugin.id', 'text/xml', 'text/html'],
      ['conneg/plugin/plugin.id', 'text/html', 'text/html'],

      // Case 8 -
//      ['conneg/negotiate', '', 'text/html'], 406?
      ['conneg/negotiate', 'application/json', 'application/json'],
      ['conneg/negotiate', 'application/xml', 'application/xml'],
    ];

    foreach ($tests as $test) {
      $message = "Testing path:$test[0] Accept:$test[1] Content-type:$test[2]";
      $request = Request::create($test[0]);
      $request->headers->set('Accept', $test[1]);

      /** @var \Symfony\Component\HttpKernel\HttpKernelInterface $kernel */
      // TODO - conneg isn't running  :(
      $kernel = \Drupal::getContainer()->get('http_kernel');
      $response = $kernel->handle($request);

      $this->assertTrue(TRUE, $message); // verbose message since simpletest doesn't let us provide a message and see the error.
      $this->assertEqual($response->getStatusCode(), Response::HTTP_OK);
      $this->assertEqual($response->headers->get('Content-type'), $test[2]);
      // @TODO support these.
//      $this->assertEqual($response->headers->get('Cache-Control'), 'public');
//      $this->assertEqual($response->headers->get('Vary'), 'Cookie');
    }
  }

  function testContentRouting406() {
    $tests = [
      // ['path', 'accept', 'content-type'],
      // @TODO this isn't supported yet.
      ['conneg/html.dne', 'vnd/dne', NULL],
      ['conneg/negotiate', 'vnd/dne', NULL],
    ];

    foreach ($tests as $test) {
      $message = "Testing path:$test[0] Accept:$test[1] Content-type:$test[2]";
      $request = Request::create($test[0]);
      $request->headers->set('Accept', $test[1]);

      /** @var \Symfony\Component\HttpKernel\HttpKernelInterface $kernel */
      // TODO - conneg isn't running  :(
      $kernel = \Drupal::getContainer()->get('http_kernel');
      $response = $kernel->handle($request);

      $this->assertTrue(TRUE, $message); // verbose message since simpletest doesn't let us provide a message and see the error.
      $this->assertEqual($response->getStatusCode(), Response::HTTP_METHOD_NOT_ALLOWED);
      $this->assertEqual($response->headers->get('Content-type'), $test[2]);
    }
  }
}
