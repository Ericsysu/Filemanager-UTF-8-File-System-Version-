<?php
include_once dirname(__FILE__) . '/init.php';
include_once dirname(__FILE__) . '/utils.php';

//get dir from post
$directory = realpath(PGRFileManagerConfig::$rootDir);
$relativePath = ''; 

//check if dir exist
if (!is_dir($directory)) PGRFileManagerUtils::sendError("Can't find root directory");

//check for extra function to do
if (isset($_POST['fun']) && PGRFileManagerConfig::$allowEdit) {
    $fun = $_POST['fun'];
    
    if (($fun === 'deleteDir') && isset($_POST['dirname'])) {
        $dirname = $_POST['dirname'];
        
        $dir = realpath($directory . $dirname);
        
        //check if dir is not a rootdir        
        if ($dir === $directory) die();
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();        
        
        if(is_dir($dir)) PGRFileManagerUtils::deleteDirectory($dir);
        
        echo json_encode(array(
    		'res'     => 'OK',
        ));

        exit(0);        
    } else if (($fun === 'addDir') && isset($_POST['dirname']) && isset($_POST['newDirname'])) {
        $dirname = urldecode($_POST['dirname']);
        $newDirname = urldecode($_POST['newDirname']);
        
        //allowed chars
        if(preg_match("/^[.\x{4e00}-\x{9fa5}A-Za-z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$/u", $newDirname) === 0) die();
        
        $dirnameLength = strlen($newDirname);
        if($dirnameLength === 0) die();
        if($dirnameLength > 200) die();
                
        $dir = realpath($directory . $dirname);
        
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();        
        
        if(is_dir($dir . '/' . $newDirname)) die();
        if(is_dir($dir)) mkdir($dir . '/' . $newDirname);
    } else if (($fun === 'renameDir') && (isset($_POST['dirname'])) && (isset($_POST['newDirname']))) {
        
        $dirname = urldecode($_POST['dirname']);
        $newDirname = urldecode($_POST['newDirname']);
        
        //allowed chars
        if(preg_match("/^[.\x{4e00}-\x{9fa5}A-Za-z0-9_ !@#$%^&()+={}\\[\\]\\',~`-]+$/u", $newDirname) === 0) die();
        
        $dirnameLength = strlen($newDirname);
        if($dirnameLength === 0) die();
        if($dirnameLength > 200) die();
        
        $dir = realpath($directory . $dirname);
        //$dir = $directory . $dirname;
        
        //check if dir is not a rootdir        
        if ($dir === $directory) die();
        //check if dir is in rootdir
        if (strpos($dir, $directory) !== 0) die();
        
        if(is_dir($dir . '/../' . $newDirname)) die();
        
        if(is_dir($dir)) rename($dir, $dir . '/../' . $newDirname);
    } else if (($fun === 'moveDir') && (isset($_POST['dir'])) && (isset($_POST['dirname'])) && (isset($_POST['toDir']))) {
        $dir = realpath(PGRFileManagerConfig::$rootDir . $_POST['dir']);
        $targetDir = realpath(PGRFileManagerConfig::$rootDir . $_POST['toDir']);
        $dirname = basename($_POST['dirname']);
        //check if dir is in rootdir
        if(strpos($dir, $directory) !== 0) die();
        if(strpos($targetDir, $directory) !== 0) die();
        if($dir === $targetDir) die();
        if(strpos($targetDir . '/', $dir . '/') === 0) die();
        
        if(is_dir($targetDir . '/' . $dirname)) die();
                
        if(is_dir($dir)) rename($dir, $targetDir . '/' . $dirname);
    }   
}

if (isset($_POST['fetchDir']) && ($_POST['fetchDir'])) {
    $dirname = $_POST['fetchDir'];
        
    $dir = realpath($directory . $dirname);
        
    //check if dir is not a rootdir        
    if ($dir === $directory) die();
    //check if dir is in rootdir
    if (strpos($dir, $directory) !== 0) die();        
        
    $directory = $dir;
    $relativePath = $dirname;
}

$folders = array();
$depth = 0;
//group folders
function getFolders($dir, $relativePath)
{
    global $folders;
    global $depth;   
    
    foreach (scandir($dir) as $elem) {
        if (($elem === '.') || ($elem === '..')) continue;
        $dirpath = $dir . '/' . $elem;
        if (is_dir($dirpath)) {
            $folder = array();
            $folder['dirname'] = $elem;
            $folder['shortname'] = (strlen($elem) > 24) ? substr($elem, 0, 24) . '...' : $elem;
            $folder['relativePath'] = $relativePath . '/' . $elem;
            $folder['depth'] = $depth; 
            $folders[] = $folder; 
            
            if ($depth < 1) {
                $depth++;
                getFolders($dirpath, $folder['relativePath']);
                $depth--;
            } else break;
        }
    } 
}

getFolders($directory, $relativePath);

echo PGRFileManagerUtils::ch_json_encode(array(
    'res'     => 'OK',
    'folders' => $folders
));

exit(0);