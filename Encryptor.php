<?php
namespace teik\Theme;

class Encryptor
{
  protected static $method = 'aes-128-cbc';
  protected static $key = null;
  protected static $iv = null;

  public static function init()
  {
    self::prepareEncrypt();
  }

  public static function prepareEncrypt()
  {
    self::$key = self::getKey();
    $_SESSION['enc_key'] = self::$key;
    self::$iv = self::getIV();
    $_SESSION['enc_iv'] = self::$iv;
  }

  public static function prepareDecrypt()
  {
    if(isset($_SESSION['enc_key'])){
      self::$key = $_SESSION['enc_key'];
    }
    if(isset($_SESSION['enc_iv'])) {
      self::$iv = $_SESSION['enc_iv'];
    }
  }

  public static function encrypt($data, $url_safe = true)
  {
    $enc = openssl_encrypt($data, self::getMethod(), self::$key, 0, self::$iv);

    if($url_safe) {
      $enc = urlencode($enc);
    }
    return $enc;
  }

  public static function decrypt($data)
  {
    $key = '';
    $iv = '';
    if(isset($_SESSION['enc_key']) && isset($_SESSION['enc_iv'])) {
      $key = $_SESSION['enc_key'];
      $iv = $_SESSION['enc_iv'];
    }
    return openssl_decrypt($data, self::getMethod(), $key, 0, $iv);
  }

  private static function getMethod()
  {
    return self::$method;
  }

  private static function getKey()
  {
    if(isset(self::$key)) {
      return self::$key;
    }
    return session_id();
  }

  private static function getIV()
  {
    if(isset(self::$iv)) {
      return self::$iv;
    }
    return self::generateToken();
  }

  private static function generateToken($length = 16)
  {
    $token = base64_encode(md5(uniqid(), true));
    return strtr(substr($token, 0, $length), '+/', '-_');
  }
}