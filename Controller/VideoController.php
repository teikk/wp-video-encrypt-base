<?php
namespace teik\Theme\Controllers;

use teik\Theme\Factory\VideoFactory;
use teik\Theme\Traits\Singleton;
use Timber\Twig_Function;
use Timber\Timber;
use teik\Theme\Encryptor;
use Timber\User;

class VideoController extends AbstractController
{
  use Singleton;

  public function hook()
  {
    add_action('init', [$this, 'onInit']);
    add_action('init', [$this,'getVideo']);

    add_filter('timber/twig', array($this,'extendTwig'), 10, 1);
  }

  public function onInit()
  {
    session_start();
    if(is_admin()) {
      return;
    }
    if(!defined('DOING_AJAX')) {
      if(!isset($_GET['watch'])) {
        session_regenerate_id();
        Encryptor::init();
      }
    }
  }

  public function getVideo()
  {
    if(isset($_GET['watch'])) {
      $id = urldecode($_GET['watch']);
      Encryptor::prepareDecrypt();
      $decryptedID = Encryptor::decrypt($id);
      if($decryptedID) {
        unset($_SESSION['enc_key']);
        unset($_SESSION['enc_iv']);
        $decryptedID = (int) $decryptedID;
        $video = VideoFactory::create($decryptedID);
        $video->streamVideo();
      } else {
        header("HTTP/1.1 401 Unauthorized");
        exit();
      }
      exit();
    }
  }

  public function extendTwig($twig)
  {
    $twig->addFunction( new Twig_Function('Video', function($file){
      return VideoFactory::create($file);
    }));
    return $twig;
  }
}