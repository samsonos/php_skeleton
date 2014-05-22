#SamsonPHP module for automatic module and code generation for [SamsonPHP](http://samsonphp.com) framework

> Module automatically scans for supported image types in web-application and it's modules resources and perform
> compression using external tools.
> Also module stores special metadata about compressed images and their timestamps to avoid dublicate compression

## Automatic Local module generation
For quick creating local module with name ```contacts``` you must visit url ```[domain]/skeleton/generate/contacts```
System will automatically create
 * ```app/view/contacts/index.php``` view file
 * ```app/controller/contacts.php``` controller file
 * ```css/contacts.less``` less file
 * ```js/contacts.coffee``` coffee file

## Automatic .less file generation from html
For automatic creating .less file from html view ```contacts/index``` you must visit url ```[domain]/skeleton/less/contacts/index```
System will automatically create ```css/contacts_index.less``` file with less selectors tree

Developed by [SamsonOS](http://samsonos.com/)