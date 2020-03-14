<?php
    // $org_img = 'upload/featurettequit400x400.png';
    // $destinationPath = "upload_tmp";
    $fileNameThumb = "/tmp/abc.png";
    // $img = new Imagick(realpath($org_img));

    function cropImageFunc($imagePath, $startX, $startY, $width, $height, $output) {
        // global $fileNameThumb;
        $img = new Imagick(realpath($imagePath));
        $img -> cropImage($width, $height, $startX, $startY);
        // if(isset($fileNameThumb)) {
            $img -> writeImage($output);
        //}
    }

    cropImageFunc('upload/featurettequit400x400.png', 100, 100, 100,100, '/tmp/abc.png');
    $timestamp = time();
    $month = substr($timestamp, 0, 6);
    print_r($month);
?>
