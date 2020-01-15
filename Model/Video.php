<?php
namespace teik\Theme\Model;

use SplFileObject;
use teik\Theme\Encryptor;

class Video
{
  private $id = 0;
  private $mime_type = '';
  private $length = 0;
  private $length_formatted = '0:00';
  private $filesize = 0;
  private $url = '';
  private $path = '';

  public function __construct(int $id, array $metadata)
  {
    $this->id = $id;
    $this->mime_type = $metadata['mime_type'];
    $this->length = $metadata['length'];
    $this->length_formatted = $metadata['length_formatted'];
    $this->filesize = $metadata['filesize'];
    $this->path = get_attached_file($id);
    $this->createURL();
  }

  /**
   * Get video ID
   * @return int
   */
  public function getID()
  {
    return $this->id;
  }

  private function createURL()
  {
    $base = get_permalink();
    $url = add_query_arg( [
      'watch' => $this->getEncryptedID(),
    ], $base );
    $this->url = $url;
  }

  public function getURL()
  {
    return $this->url;
  }

  public function getEncryptedID()
  {
    return Encryptor::encrypt($this->id);
  }

  /**
   * Get mime type
   * @return string
   */
  public function getMimeType()
  {
    return $this->mime_type;
  }

  /**
   * Get video length
   *
   * @return int|string
   */
  public function getLength($raw = true)
  {
    if($raw) {
      return $this->length;
    }
    return $this->length_formatted;
  }

  /**
   * Get file size
   *
   * @return int
   */
  public function getFileSize()
  {
    return $this->filesize;
  }

  public function getPath()
  {
    return $this->path;
  }

  public function getFileObject()
  {
    return new SplFileObject($this->getPath());
  }

  public function setHeaders()
  {
    header("Content-Type: ". $this->getMimeType());
    header('Content-disposition: inline');
    header('Connection: close');
  }

  public function streamVideo()
  {
    $file = $this->getFileObject();
    if($file->isReadable()) {
      $this->setHeaders();
      header('Content-Length:'.$file->getSize());
      echo $file->fread($file->getSize());
    }
  }
}