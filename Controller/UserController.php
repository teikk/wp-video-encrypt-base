<?php
namespace teik\Theme\Controllers;

use teik\Theme\Traits\Singleton;
use Timber\User;

class UserController extends AbstractController
{
  use Singleton;

  /**
   * Meta keys for user data
   */
  private $ownedProductsKey = '_owned_programs';
  private $userEventsKey = '_user_events';

  private function __construct() {
    $this->someData = 'HELLO '.__CLASS__;
  }
  public function hook()
  {
    add_action('wp',[$this, 'checkProducts']);
    add_action('insert_user_meta', [$this, 'defaultMeta'], 10, 3);
    add_action('wp_ajax_save_events', [$this, 'saveEvents']);
  }

  public function checkProducts()
  {
    $user = new User();
    if($user->ID) {
      foreach ($user->meta($this->ownedProductsKey) as $productID => $data) {
        if(!$data['expiration_timestamp']) continue;

        $now = $this->getCurrentTimestamp();

        if($data['expiration_timestamp'] < $now) {
          $this->removeProductAccess($user, $productID);
        }
      }
    }
  }

  private function getCurrentTimestamp()
  {
    return current_time( 'U', true );
  }

  /**
   * @param Timber\User $user
   * @param int $productID
   */
  public function removeProductAccess(User $user, $productID) {
    $ownedProducts = $user->meta($this->ownedProductsKey);
    unset($ownedProducts[$productID]);
    $user->update($this->ownedProductsKey, $ownedProducts);
  }

  /**
   * Adds default user meta
   *
   * @param array $meta Default user meta
   * @param WP_User $user User object
   */
  public function defaultMeta($meta, $user, $update)
  {
    if(!$update) {
      $meta[$this->userEventsKey] = array();
      $meta[$this->ownedProductsKey] = array();
    }
    return $meta;
  }

  public function saveEvents()
  {
    $user = new User();
    $userEvents = $user->meta($this->userEventsKey);
    if(empty($userEvents)) {
      $userEvents = [];
    }
    $event = [
      'title'     => $_REQUEST['title'],
      'timestamp' => $_REQUEST['date']
    ];
    $userEvents[]= $event;
    $user->update($this->userEventsKey, $userEvents );

    wp_send_json_success([
      'message' => 'Zapisano event',
      'status'  => 'complete'
    ]);
  }
}