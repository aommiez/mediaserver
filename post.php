<?php

if (empty($_POST['img'])) {
    echo "no img upload";
    exit;
}
$base64data = $_POST['img'];
$objectid = null;
$max_original = 2048;
$imgdata = base64_decode($base64data);
$mimetype = getImageMimeType($imgdata);

if ($mimetype == 'jpg' || $mimetype == 'png' || $mimetype == 'jpeg') {
    if ($mimetype == "jpeg") {
        $mimetype = "jpg";
    }
} else {
    echo "file type error";
    exit();
}

try {
    // open connection to MongoDB server
    $conn = new Mongo('localhost');

    // access database
    $db = $conn->mediadb;

    // access collection
    $collection = $db->items;
    $ar = ['base64data'=> $base64data, 'type'=>$mimetype];
    $collection->insert($ar);
    //var_dump($ar);
    $objectid = $ar["_id"]->{'$id'};
    // disconnect from server
    $conn->close();
} catch (MongoConnectionException $e) {
    die('Error connecting to MongoDB server');
} catch (MongoException $e) {
    die('Error: ' . $e->getMessage());
}

$dir40 = "40/";
$dir80 = "80/";
$dir160 = "160/";
$dir320 = "320/";
$dir640 = "640/";
$dir1280 = "1280/";
$dir2048 = "2048/";
$diroriginal = "original/";

$ss40 = null;
$ss80 = null;
$ss160 = null;
$ss320 = null;
$ss640 = null;
$ss1280 = null;
$ss2048 = null;
$ssoriginal = null;


$imgname = $objectid.$mimetype;



$img = new Imagick();
$img->readimageblob($imgdata);
$img->setimageformat($mimetype);
$imageprops = $img->getImageGeometry();
$img_orginal_width = $imageprops['width'];
$img_orginal_height = $imageprops['height'];
$image_properties = $img->getImageProperties();

// copy to origninal
if ($img->writeImages($diroriginal.$imgname.".".$mimetype, true)) {
    chmod($diroriginal.$imgname.".".$mimetype,0777);
    $ssoriginal = true;
}

// copy to dir 2048
if ($imageprops['width'] >= 2048 ) {
    $img->scaleImage(2048,0);
    $img->writeImages($dir2048.$imgname.".".$mimetype, true);
    if ($img->writeImages($dir2048.$imgname.".".$mimetype, true)) {
        chmod($dir2048.$imgname.".".$mimetype,0777);
        $ss2048 = true;
    }
}

// copy to dir 1280
if ($imageprops['width'] >= 1280 ) {
    $img->scaleImage(1280,0);
    if ($img->writeImages($dir1280.$imgname.".".$mimetype, true)) {
        chmod($dir1280.$imgname.".".$mimetype,0777);
        $ss1280 = true;
    }
}

// copy to dir 640
if ($imageprops['width'] >= 640 ) {
    $img->scaleImage(640,0);
    if ($img->writeImages($dir640.$imgname.".".$mimetype, true)) {
        chmod($dir640.$imgname.".".$mimetype,0777);
        $ss640 = true;
    }
}

// copy to dir 320
if ($imageprops['width'] >= 320 ) {
    $img->scaleImage(320,0);
    if ($img->writeImages($dir320.$imgname.".".$mimetype, true)) {
        chmod($dir320.$imgname.".".$mimetype,0777);
        $ss320 = true;
    }
}

// copy to dir 160
if ($imageprops['width'] >= 160 ) {
    $img->scaleImage(160,0);
    if ($img->writeImages($dir160.$imgname.".".$mimetype, true)) {
        chmod($dir160.$imgname.".".$mimetype,0777);
        $ss160 = true;
    }
}


// copy to dir 80
if ($imageprops['width'] >= 80 ) {
    $img->scaleImage(80,0);
    if ($img->writeImages($dir80.$imgname.".".$mimetype, true)) {
        chmod($dir80.$imgname.".".$mimetype,0777);
        $ss80 = true;
    }
}

// copy to dir 40
if ($imageprops['width'] >= 40 ) {
    $img->scaleImage(40,0);
    if ($img->writeImages($dir40.$imgname.".".$mimetype, true)) {
        chmod($dir40.$imgname.".".$mimetype,0777);
        $ss40 = true;
    }
}

try {
    // open connection to MongoDB server
    $conn = new Mongo('localhost');

    // access database
    $db = $conn->mediadb;

    // access collection
    $collection = $db->items;
    //var_dump($ar);
    $objectid = $ar["_id"]->{'$id'};
    // disconnect from server
    $ar = array('original_size' => array('original_width' => $img_orginal_width , 'original_height' => $img_orginal_height) , 'size_check' => array('original' => $ssoriginal ,'40' => $ss40, '80' => $ss80, '160' => $ss160 , '320' =>$ss320 , '640'=>$ss640 , '1280' => $ss1280 , '2048' => $ss2048));
    $collection->update(array('_id' => new MongoId($objectid)),array('$set' => $ar));
    $conn->close();
} catch (MongoConnectionException $e) {
    die('Error connecting to MongoDB server');
} catch (MongoException $e) {
    die('Error: ' . $e->getMessage());
}


$arr = array('objectid' => $objectid.$mimetype, 'type' => $mimetype, 'original_size' => array('original_width' => $img_orginal_width , 'original_height' => $img_orginal_height) , 'size_check' => array('original' => $ssoriginal ,'40' => $ss40, '80' => $ss80, '160' => $ss160 , '320' =>$ss320 , '640'=>$ss640 , '1280' => $ss1280 , '2048' => $ss2048));
header('Content-Type: application/json');
echo json_encode($arr);


function getBytesFromHexString($hexdata)
{
    for($count = 0; $count < strlen($hexdata); $count+=2)
        $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

    return implode($bytes);
}

function getImageMimeType($imagedata)
{
    $imagemimetypes = array(
        "jpeg" => "FFD8",
        "png" => "89504E470D0A1A0A",
        "gif" => "474946",
        "bmp" => "424D",
        "tiff" => "4949",
        "tiff" => "4D4D"
    );

    foreach ($imagemimetypes as $mime => $hexbytes)
    {
        $bytes = getBytesFromHexString($hexbytes);
        if (substr($imagedata, 0, strlen($bytes)) == $bytes)
            return $mime;
    }

    return NULL;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}