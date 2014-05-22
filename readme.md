#SamsonPHP module for automatic optimizing of web-application images for [SamsonPHP](http://samsonphp.com) framework

> Module automatically scans for supported image types in web-application and it's modules resources and perform
> compression using external tools.
> Also module stores special metadata about compressed images and their timestamps to avoid dublicate compression

## Lossless .JPEG images compression
Module uses [jpegOptim](http://freecode.com/projects/jpegoptim).
To install jpegOptim under Ubuntu use: ```sudo apt-get install jpegoptim```


## Lossless .PNG images compression
Module uses [optiPNG](http://optipng.sourceforge.net/).
To install optiPNG under Ubuntu use: ```sudo apt-get install optipng```

Developed by [SamsonOS](http://samsonos.com/)