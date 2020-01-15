<?php
namespace teik\Theme\Controllers;

use teik\Theme\REST\UserRoute;
use teik\Theme\Traits\Singleton;

class RestAPIController extends AbstractController
{
  use Singleton;

  public function hook()
  {
    add_action('rest_api_init', [$this, 'addRoutes']);
  }

  public function addRoutes()
  {
    $userRoute = new UserRoute;
    $userRoute->register_routes();
  }
}