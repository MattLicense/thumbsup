<?php
namespace Thumbsup;
/**
 * thumbsup.php
 * ThumbsUp. A Laravel bundle to crop images to a set dimension
 * @author  Matt License (matt@mattlicense.co.uk)
 * Date     January 2013
 */

use Laravel\File;

class Thumbsup {

    /**
     * @var array       $fileTypes
     * Allowed file types pointing to php imaging functions and file extensions
     * @default png|jpg|gif
     */
    static $mimeTypes = array(
        'image/png'     => array('create' => 'imagecreatefrompng', 'generate' => 'imagepng', 'ext' => 'png'),
        'image/jpeg'    => array('create' => 'imagecreatefromjpeg', 'generate' => 'imagejpeg', 'ext' => 'jpeg'),
        'image/gif'     => array('create' => 'imagecreatefromgif', 'generate' => 'imagegif', 'ext' => 'gif'),
    );

    /**
     * @var integer     $maxSize
     * Maximum file size in bytes
     * @default 1Mb (1048576b)
     */
    static $maxSize = 1048576;

    /**
     * @var string      $saveFolder
     * Folder in which the thumbnails will be saved
     * @default 'thumbs'
     */
    static $saveFolder = 'thumbs';

    /**
     * crop()
     * Crop a base image to a fixed size (@default 250x250), from fixed coordinates (@default [0,0] - top left corner)
     * @param string    $imgSrc     location of the base image
     * @param integer   $x          x coordinate to start the crop      @default 0
     * @param integer   $y          y coordinate to start the crop      @default 0
     * @param integer   $width      cropped width                       @default 250px
     * @param integer   $height     cropped height                      @default 250px
     * @return string|false
     * @throws Exception
     */
    public static function crop($imgSrc, $width = 250, $height = 250, $x = 0, $y = 0) {
        //
        // make sure that the image points towards the public folder
        $imgSrc = $_SERVER['DOCUMENT_ROOT'] . $imgSrc;

        //
        // check that the file exists
        if(!file_exists($imgSrc))
            throw new \Exception('File ' . $imgSrc . ' not found');

        //
        // check that the file is less than the maximum
        if(filesize($imgSrc) > static::$maxSize)
            throw new \Exception('File is bigger than ' . static::$maxSize/1024 . 'Kb');

        //
        // get the base image dimensions and check that the thumbnail will be within dimensions
        list($srcWidth, $srcHeight) = getimagesize($imgSrc);
        if(($x + $width) > $srcWidth)
            throw new \Exception('Thumbnail over-extends image width by ' . ($x + $width - $srcWidth) . ' pixels');
        if(($y + $height) > $srcHeight)
            throw new \Exception('Thumbnail over-extends image height by ' . ($y + $height - $srcHeight) . ' pixels');

        //
        // get the mime type
        $finfo = new \finfo();
        $fileType = $finfo->file($imgSrc, FILEINFO_MIME_TYPE);

        //
        // check the file type is valid
        if(!array_key_exists($fileType, static::$mimeTypes))
            throw new \Exception('Mime type '.$fileType.' not supported by Thumbsup bundle.');

        //
        // get the function to create the thumbnail
        $createImageFrom    = static::$mimeTypes[$fileType]['create'];
        $imageFrom          = static::$mimeTypes[$fileType]['generate'];
        $mimeExtension      = static::$mimeTypes[$fileType]['ext'];

        //
        // create the image
        $src = $createImageFrom($imgSrc);
        $dest = imagecreatetruecolor($width, $height);

        imagecopy($dest, $src, 0, 0, $x, $y, $width, $height);

        //
        // save the image to /public/img/thumbs (by default)
        $saveTo = 'img' . DS . static::$saveFolder;

        //
        // if the folder doesn't exist, create it with the
        if(!is_dir(path('public') . $saveTo))
            mkdir(path('public') . $saveTo);

        //
        // file location
        $saveName = $saveTo . DS . md5($imgSrc . $x . $y) . '.' . $mimeExtension;

        //
        // copy the image to the output buffer and destroy the image to free up resources
        ob_start();
        $imageFrom($dest);
        $output = ob_get_clean();
        imagedestroy($dest);

        //
        // save the buffer and return the img tag
        \File::put(path('public') . $saveName, $output) ? $saveName : false;
        return '<img src="' . $saveName . '">';
    }
}