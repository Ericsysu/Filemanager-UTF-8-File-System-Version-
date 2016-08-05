<?php
include_once dirname(__FILE__) . '/init.php';

//get dir from GET
if (isset($_GET['dir'])) {
    $directory = realpath(PGRFileManagerConfig::$rootDir . $_GET['dir']);
} else die();

//check if dir exist
//$directory = iconv("utf-8","gb2312",$directory);
if (!is_dir($directory)) die();

//check if dir is in rootdir
if (strpos($directory, realpath(PGRFileManagerConfig::$rootDir)) === false) die();

//$_GET['filename'] = iconv("utf-8","gb2312",$_GET['filename']);
if (!isset($_GET['filename'])) die();

$filename = realpath($directory . '/' . $_GET['filename']);
//check if file is in dir
if(dirname($filename) !== $directory) die();

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename,"."),1));

if( $filename == "" ) 
{
  echo "<html><title></title><body>ERROR: download file NOT SPECIFIED</body></html>";
  exit;
} elseif ( ! file_exists( $filename ) ) 
{
  echo "<html><title></title><body>ERROR: File not found</body></html>";
  exit;
};
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "exe": $ctype="application/octet-stream"; break;
  case "zip": $ctype="application/zip"; break;
  case "doc": $ctype="application/msword"; break;
  case "xls": $ctype="application/vnd.ms-excel"; break;
  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}

$fp=fopen($filename,"r");
$filesize = filesize($filename);
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: $ctype");
header("Accept-Ranges: bytes");
header("Accept-Length: $filesize");
// change, added quotes to allow spaces in filenames
header("Content-Disposition: attachment; filename=\"".$_GET['filename']."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
readfile("$filename");

$buffer=1024;

while(!feof($fp)){
  $file_data=fread($fp,$buffer);
  
  echo $file_data;
}

fclose($fp);

exit();