<?php
namespace teik\Theme\Model;

use DateInterval;
use WC_Product;
use DateTime;

class ProductExpiration
{
  private $product = 0;

  private $purchaseTimestamp = null;

  private $purchaseDate = null;

  private $purchaseDateTime = null;

  private $expirationDateTime = null;
  private $expirationDate = null;
  private $expirationTimestamp = null;

  /**
   * @param int $product
   * @param int $purchaseTimestamp
   */
  public function __construct($product, $purchaseTimestamp)
  {
    $this->setProduct($product);
    $this->setPurchaseDateTime($purchaseTimestamp);
    $this->setPurchaseTimestamp();
    $this->setPurchaseDate();

    $this->setExpirationDateTime();
    $this->setExpirationTimestamp();
    $this->setExpirationDate();
  }

  public function setProduct($product)
  {
    $this->product = new WC_Product($product);
  }

  public function getProduct()
  {
    return $this->product;
  }

  public function setPurchaseDateTime($timestamp)
  {
    $this->purchaseDateTime = DateTime::createFromFormat('U', $timestamp);
  }

  public function getPurchaseDateTime() : DateTime
  {
    return $this->purchaseDateTime;
  }

  public function setPurchaseTimestamp()
  {
    $this->purchaseTimestamp = $this->getPurchaseDateTime()->getTimestamp();
  }

  public function getPurchaseTimestamp()
  {
    return $this->purchaseTimestamp;
  }

  public function setPurchaseDate()
  {
    $this->purchaseDate = $this->formatDate($this->getPurchaseTimestamp());
  }

  public function getPurchaseDate()
  {
    return $this->purchaseDate;
  }

  public function setExpirationDateTime()
  {
    $duration = get_field('program_duration', $this->getProduct()->get_id());
    if($duration) {
      $interval = new DateInterval('P'.$duration.'D');
      $purchaseDate = clone $this->getPurchaseDateTime();
      $this->expirationDateTime = $purchaseDate->add($interval);
    } else {
      $this->expirationDateTime = false;
    }
  }

  public function getExpirationDateTime()
  {
    return $this->expirationDateTime;
  }

  public function setExpirationTimestamp()
  {
    if($this->getExpirationDateTime()) {
      $this->expirationTimestamp = $this->getExpirationDateTime()->getTimestamp();
    } else {
      $this->expirationTimestamp = 0;
    }
  }

  public function getExpirationTimestamp()
  {
    return $this->expirationTimestamp;
  }

  public function setExpirationDate()
  {
    if($this->getExpirationDateTime() instanceof DateTime) {
      $this->expirationDate = $this->formatDate($this->getExpirationTimestamp());
    } else {
      $this->expirationDate = false;
    }
  }

  public function getExpirationDate()
  {
    return $this->expirationDate;
  }

  public function prepareToSave()
  {
    return [
      'product_id' => $this->getProduct()->get_id(),
      'purchase_timestamp' => $this->getPurchaseTimestamp(),
      'expiration_timestamp' => $this->getExpirationTimestamp(),
    ];
  }

  private function formatDate($timestamp)
  {
    $date_format = get_option('date_format');
    return date_i18n($date_format, $timestamp);
  }
}