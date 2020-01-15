<?php
namespace teik\Theme\Controllers;

use teik\Theme\Model\ProductExpiration;
use Timber\User;
use teik\Theme\Traits\Singleton;
use WC_Order;

class OrderController extends AbstractController
{
  use Singleton;
  
  public function hook()
  {
    add_action( 'woocommerce_order_status_completed', [$this, 'onComplete'], 10, 1);
    add_action( 'woocommerce_order_status_processing', [$this, 'onProcessing'], 10, 1);
  }

  /**
   * @param int $orderID
   */
  public function onComplete($orderID)
  {
    $order = new WC_Order($orderID);
    // ob_start();
    $user = new User($order->get_customer_id());

    $user_programs = $user->meta('_owned_programs');
    if(empty($user_programs)) {
      $user_programs = array();
    }

    $items = $order->get_items();
    foreach ($items as $key => $item) {
      $expiration = new ProductExpiration($item->get_product_id(), $order->get_date_completed('edit')->getOffsetTimestamp());
      $user_programs[$item->get_product_id()] = $expiration->prepareToSave();
    }

    $user->update('_owned_programs', $user_programs);
  }

  /**
   * @param int $orderID
   */
  public function onProcessing($orderID)
  {
    $order = new WC_Order($orderID);
    $order->update_status('completed', __('SYSTEM: Automatyczna zmiana statusu'));
  }
}