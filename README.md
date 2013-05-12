ThumbsUp
--------

A PHP script to crop jpeg, png, gif images to set dimensions.

Usage
-----

Once you've got everything set up, you can create thumbnails from local images by using:

<code>MattLicense\Thumbsup::crop('/path/to/img')</code>

By default, this will create a 250x250 thumbnail from the top left corner of the image. You can also create different sized thumbnails, from a different point on the image using:

<code>MattLicense\Thumbsup::crop('/path/to/img', array('width' => 300, 'height' => 150), array('x' => 100, 'y' => 50))</code>

This will create a 300x150 image starting 100px from the left and 50px from the top of the image.

You can also add HTML attributes to the image:

<code>MattLicense\Thumbsup::crop('/path/to/img', array('width' => 300, 'height' => 150), array('x' => 100, 'y' => 50), array('class' => 'img-class', 'alt' => 'cropped image'))</code>

Licence
-------

I really don't care what you do with this code. Feel free to edit as you like.