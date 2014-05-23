#SamsonPHP module for automatic module and code generation for [SamsonPHP](http://samsonphp.com) framework

> This module helps with various routine tasks and focused on their automatic completion

## Automatic Local module generation
For quick creating local module with name ```contacts``` you must visit url ```[domain]/skeleton/generate/contacts```
System will automatically create
 * ```app/view/contacts/index.php``` view file
 * ```app/controller/contacts.php``` controller file
 * ```css/contacts.less``` less file
 * ```js/contacts.coffee``` coffee file

###Module generator Configuration
Available one configurable parameters for tuning:
 * ```boolean $createCoffee``` If true .coffee file will be generated otherwise .js

##Automatic .less file generation from html
For automatic creating .less file from html view ```contacts/index``` you must visit url ```[domain]/skeleton/less/contacts/index```
System will automatically create ```css/contacts_index.less``` file with less selectors tree

###Less generator Configuration
Available two configurable parameters for tuning less generator:
 * ```array $lessIgnore``` Collection of tags to ignore
 * ```boolean $lessDebug``` True to output selector paths as comments in less file


##Example configuration class for this module:
```
class SkeletonConfig extends \samson\core\Config
{
    public $__module = 'skeleton';

    public $createCoffee = true;

    public $lessIgnore = array('html','body',...);

    public $lessDebug = true;
}
```


Developed by [SamsonOS](http://samsonos.com/)