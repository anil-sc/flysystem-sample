<?php

require_once 'vendor/autoload.php';

use \League\Flysystem\Filesystem;
use \League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use \League\Flysystem\PhpseclibV2\SftpAdapter;
use \League\Flysystem\UnixVisibility\PortableVisibilityConverter;
// Upload files.
$files = ["doc-front.png", "doc-side.png"];
// SFTP connection information.
$host = '';//host information
$port = 22;
$username = ''; // user name
$password = null;
$privateKey = ""; // private key
$passphrase = null;
$useagent = false;
$timeout = 10;
$maxtries = 1;
$root = ''; // root path
// Connect to server via SFTP.
try{
  $sftpConnectionProvider = new SftpConnectionProvider($host, $username, $password, $privateKey, $passphrase, $port, $useagent, $timeout, $maxtries);
  $connection = $sftpConnectionProvider->provideConnection();
  if(!empty($connection)) {
    $sftp = new Filesystem(new SftpAdapter(
      $sftpConnectionProvider,
      $root,
      PortableVisibilityConverter::fromArray([
        'file' => ['public' => 0640, 'private' => 0604],
        'dir' => ['public' => 0740, 'private' => 7604]
      ])
    ));

    //Put the file on the server.
    foreach($files as $filepath) {
      try {
        $sftp->write(basename($filepath), file_get_contents($filepath));
        echo "Upload $filepath to $host" . PHP_EOL;
      } catch(\Throwable $e) {
        echo $e->getMessage() . PHP_EOL;
      }
    }
  }
}catch (\Throwable $e) {
  echo "Error: ". $e->getMessage() . PHP_EOL;
}



