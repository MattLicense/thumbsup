<?php
/**
 * thumbsup.php
 * A small PHP script for cropping gif, jpeg and png images
 * @author Matt License (http://www.mattlicense.co.uk)
 */

namespace MattLicense;

class ThumbsUp
{
    static protected $maxSize = 1048576;

    static protected $location = "/img/thumbs";

    static protected $mimeTypes = array(
        'image/png'     => array('create' => 'imagecreatefrompng', 'generate' => 'imagepng', 'ext' => 'png'),
        'image/jpeg'    => array('create' => 'imagecreatefromjpeg', 'generate' => 'imagejpeg', 'ext' => 'jpeg'),
        'image/gif'     => array('create' => 'imagecreatefromgif', 'generate' => 'imagegif', 'ext' => 'gif'),
    );

    static public function crop($imgSrc,
                                array $dimensions = array('width' => 250, 'height' => 250),
                                array $crop = array('x' => 0, 'y' => 0),
                                array $attributes = array())
    {
        // check the image parameters
        if(!file_exists($imgSrc))
            throw new IllegalArgumentException("Image {$imgSrc} not found.");

        if(filesize($imgSrc) > static::$maxSize)
            throw new IllegalArgumentException('File is bigger than ' . static::$maxSize/1024 . 'Kb');

        if(!(array_key_exists('width', $dimensions) && array_key_exists('height', $dimensions))
            throw new IllegalArgumentException("The dimensions array must have both height and width keys");

        if(!(array_key_exists('x', $crop) && array_key_exists('y', $crop)))
            throw new IllegalArgumentException("The crop array must have both x and y keys");

        $x = $crop['x']; $y = $crop['y'];
        $width = $dimensions['width'];
        $height = $dimensions['height'];

        // get the base image dimensions and check that the thumbnail will be within dimensions
        list($srcWidth, $srcHeight) = getimagesize($imgSrc);
        if(($x + $width) > $srcWidth)
            throw new \Exception('Thumbnail over-extends image width by ' . ($x + $width - $srcWidth) . ' pixels');
        if(($y + $height) > $srcHeight)
            throw new \Exception('Thumbnail over-extends image height by ' . ($y + $height - $srcHeight) . ' pixels');

        // get the mime type
        $finfo = new \finfo();
        $fileType = $finfo->file($imgSrc, FILEINFO_MIME_TYPE);

        // check the file type is valid
        if(!array_key_exists($fileType, static::$mimeTypes))
            throw new Exception("Mime type {$fileType} is not supported.");

        // get the function to create the thumbnail
        $createImageFrom    = static::$mimeTypes[$fileType]['create'];
        $imageFrom          = static::$mimeTypes[$fileType]['generate'];
        $mimeExtension      = static::$mimeTypes[$fileType]['ext'];

        // generate a name
        $saveName = md5($imgSrc . $x . $y . $width . $height) . '.' . $mimeExtension;

        // if the save folder doesn't exist, create it with the
        if(!is_dir(realpath(static::$location))) mkdir(realpath(static::$location));

        // file location
        $savePath = realpath(static::$location) . DIRECTORY_SEPARATOR . $saveName;

        // make sure we've haven't made this thumbnail before
        if(!file_exists($savePath)) {
            // create the image
            $src = $createImageFrom($imgSrc);
            $dest = imagecreatetruecolor($width, $height);

            imagecopy($dest, $src, 0, 0, $x, $y, $width, $height);

            // copy the image to the output buffer and destroy the image to free up resources
            ob_start();
            $imageFrom($dest);
            $output = ob_get_clean();
            imagedestroy($dest);

            // save the buffer to file
            file_put_contents($savePath, $output);
        }

        // explode the attributes
        $att = '';
        foreach($attributes as $key => $val) $att .= $key .'="'.$val.'" ';

        return '<img src="' . $savePath . '" '.trim($att).'>';
    }
}