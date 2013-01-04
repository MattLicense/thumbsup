ThumbsUp
--------

A Laravel bundle to crop jpeg, png, gif images to set dimensions.

Install
-------

Using the Artisan CLI, you can use the following command:

<code>php artisan bundle:install thumbsup</code>

Then you'll need to register the bundle in <code>application/bundles.php</code>

<code>'thumbsup' => array('auto' => true)</code>

And add the alias to <code>application/config/application.php</code> (you don't have to, but it looks nicer in your code)

<code>'Thumbsup' => 'Thumbsup\\\Thumbsup',</code>

Usage
-----

Once you've got everything set up, you can create thumbnails from local images by using:

<code>Thumbsup::crop('/path/to/img')</code>

By default, this will create a 250x250 thumbnail from the top left corner of the image. You can also create different sized thumbnails, from a different point on the image using:

<code>Thumbsup::crop('path/to/img', 300, 150, 100, 50)</code>

This will create a 300x150 image starting 100px from the left and 50px from the top of the image.

<b>Note:</b> The path you specify should be from your base domain. E.g. <code>/path/to/img</code> should be <code>http://www.yourdomain.com/path/to/img</code>

All thumbnails will be saved in /img/thumbs by default (if this folder doesn't exist, it will be created).

Licence
-------

I really don't care what you do with this code. Feel free to edit as you like.