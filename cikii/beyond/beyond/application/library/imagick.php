<?php
    // $org_img = 'upload/featurettequit400x400.png';
    // $destinationPath = "upload_tmp";
    // $fileNameThumb = "abc.png";
    // $img = new Imagick(realpath($org_img));

    function cropImage($imagePath, $startX, $startY, $width, $height, $fileNameThumb) {
        // global $fileNameThumb;
        $img = new Imagick(realpath($imagePath));
        $img -> cropImage($width, $height, $startX, $startY);
        if(isset($fileNameThumb)) {
            $img -> writeImage($fileNameThumb);
        }
    }
?>
