<?php
/**
 * start.php
 * Starting the ThumbsUp bundle
 * @author  Matt License
 * Date     January 2013
 */

Autoloader::map(array(
    'Thumbsup\Thumbsup' => path('bundle').'/thumbsup/thumbsup.php'
));