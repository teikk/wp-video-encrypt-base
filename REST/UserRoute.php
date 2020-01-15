<?php
namespace teik\Theme\REST;

use Timber\User;

class UserRoute extends \WP_REST_Controller
{
  /**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace;
	/**
	 * Rest base for the current object.
	 *
	 * @var string
	 */
  protected $base;

  public function __construct()
  {
    $this->namespace = 'ndz/v1';
    $this->base = 'user-events';
  }
  public function register_routes()
  {
    register_rest_route( $this->namespace, '/'.$this->base, array(
      'methods' => 'GET',
      'callback' => [$this, 'get_items'],
      'permission_callback' => [$this, 'get_items_permissions_check'],
    ) );
  }

  public function get_items($request)
  {
    $user = new User();
    $events = $user->meta('_user_events');
    return new \WP_REST_Response($events, 200);
  }

  public function get_items_permissions_check( $request ) {
    if(!is_user_logged_in()) {
      return false;
    }
    return true;
  }
}