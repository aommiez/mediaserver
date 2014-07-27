<?php

if (empty($_GET['custom'])) {
    if (empty($_GET['id'])) {
        echo "required parameter id";
        exit();
    } else if (empty($_GET['size'])) {
        //echo "required parameter size";
        $type = substr($_GET['id'], -3);
        header('Content-Type: image/'.$type.'');
        echo file_get_contents("original/".$_GET['id'].".".$type);
        exit();
    } else if (!empty($_GET['id']) && !empty($_GET['size'])) {
        $type = substr($_GET['id'], -3);
        /*
        if ($_GET['size'] != 40 or $_GET['size'] != 80 or $_GET['size'] != 160 or $_GET['size'] != 320 or $_GET['size'] != 640 or $_GET['size'] != 1280 or $_GET['size'] != 2048 ) {
            echo "size not found ! 40 , 80 , 160 , 320 , 640 , 1280 , 2048 , original";
            exit();
        }*/
        header('Content-Type: image/'.$type.'');
        echo file_get_contents($_GET['size']."/".$_GET['id'].".".$type);
        exit();
    }
} else {
    if (empty($_GET['id'])) {
        echo "required parameter id";
        exit();
    } else if (empty($_GET['width'])) {
        echo "required parameter width";
        exit();
    } else if (!empty($_GET['id']) && !empty($_GET['custom']) && !empty($_GET['width']) && empty($_GET['height']) ) {
        $type = substr($_GET['id'], -3);
        $im = new imagick("original/".$_GET['id'].".".$type);
        $im->setimageformat($type);
        $im->scaleimage($_GET['width'],0);
        header('Content-type: image/'.$type.'');
        echo $im;
    } else if (!empty($_GET['id']) && !empty($_GET['custom']) && !empty($_GET['width']) && !empty($_GET['height']) )  {
        $w = 0;
        if ($_GET['width'] == "auto") {
            $w = 0;
        } else {
            $w = $_GET['width'];
        }
        $type = substr($_GET['id'], -3);
        $im = new imagick("original/".$_GET['id'].".".$type);
        $im->setimageformat($type);
        $im->scaleimage($w,$_GET['height']);
        header('Content-type: image/'.$type.'');
        echo $im;
    }
}
?>
