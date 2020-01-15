<?php
namespace teik\Theme\Factory;

use SplFileObject;
use teik\Theme\Model\Video;

abstract class VideoFactory
{
  public static function create($file) : Video
  {
    if(is_array($file)) {
      $video = self::createFromArray($file);
    }
    if(is_int($file)) {
      $video = self::createFromID($file);
    }
    return $video;
  }

  public static function getFileObject($id)
  {
    $file = get_attached_file($id);
    return new SplFileObject($file);
  }

  private static function createFromArray(array $file) : Video
  {
    if(isset($file['ID'])) {
      $fileID = $file['ID'];
    } elseif (isset($file['id'])) {
      $fileID = $file['id'];
    } else {
      wp_die('Niepoprawny identyfikator filmu');
    }
    $file = self::getMetadata($fileID);
    return new Video($fileID, $file);
  }

  private static function createFromID(int $fileID) : Video
  {
    $file = self::getMetadata($fileID);
    return new Video($fileID, $file);
  }

  private static function getMetadata(int $file)
  {
    $file = wp_get_attachment_metadata( $file );
    return $file;
  }
}