<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 21.05.14 at 10:51
 */
namespace samsonos\php\skeleton;

/**
 * Class for interacting with SamsonPHP
 * @author Vitaly Egorov <egorov@samsonos.com>
 * @copyright 2014 SamsonOS
 * @version 1.0.0
 */
class Skeleton extends \samson\core\ExternalModule
{
    /** @var string Module identifier */
    public $id = 'skeleton';

    /** Flag for debugging less */
    public $lessDebug = false;

    /** Collection of tag names to ignore within less building */
    public $lessIgnore = array(
        'head',
        'meta',
        'script',
        'link',
        'title',
        'br'
    );

    /** Collection of created less selectors */
    protected $lessSelectors = array();

    /**
     * Create recursevely directory if it does not exists
     * @param string $path path to directory
     */
    protected function createDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }
    }

    /**
     * Generate spaces for specific code level
     * @param integer $level Current code nesting level
     *
     * @return string Spaces string
     */
    protected function spacer($level)
    {
        $result = '';
        for($i = 0; $i < $level; $i++) {
            $result .= '  ';
        }
        return $result;
    }

    /**
     * Reccurently build less file
     * @param \DOMNode $node
     * @param string $less Less file contents
     */
    protected function lessBuilder($node, & $less = '', $level = 0, $path = '')
    {
        /**@var $child \DOMDocument */
        $child = null;
        foreach($node->childNodes as $child) {
            // Work only with DOMElements
            if($child->nodeType == 1 && !in_array($child->nodeName, $this->lessIgnore)) {
                //elapsed($child->nodeName);
                $class = '';
                $id = '';
                $name = '';
                /**@var $attribute \DOMNode */
                foreach ($child->attributes as $attribute) {
                    if($attribute->name == 'class') {
                        $class = trim($attribute->nodeValue);
                    }
                    if($attribute->name == 'id') {
                        $id = trim($attribute->nodeValue);
                    }
                    if($attribute->name == 'name') {
                        $name = trim($attribute->nodeValue);
                    }
                }

                // Define less selector
                $selector = '';
                if (isset($id{0})) {
                    $selector = '#'.$id;
                } else if (isset($class{0})) {
                    $selector = '.'.$class;
                } else if (isset($name{0})) {
                    $selector = $child->nodeName.'[name='.$name.']';
                } else {
                    $selector = $child->nodeName;
                }


                elapsed($selector.'--'.$class.' '.$id.' '.$name);

                // Analyze if node has valid children
                $isComplex = false;
                if ($child->hasChildNodes()) {
                    foreach($child->childNodes as $child2) {
                        // Work only with DOMElements
                        if($child2->nodeType == 1 && !in_array($child2->nodeName, $this->lessIgnore)) {
                            $isComplex = true;
                            break;
                        }
                    }
                }

                if ($isComplex) {

                    // Build path selector
                    $_path = $path.'->'.$selector;

                    // Don't go same path again
                    if (!in_array($_path, $this->lessSelectors)) {

                        // Save selector
                        $this->lessSelectors[] = $_path;

                        // Save less
                        if ($this->lessDebug) {
                            $less .= "\n".$this->spacer($level).'/*'.$_path.'*/';
                        }

                        $less .= "\n".$this->spacer($level).$selector.' {';

                        // Go deeper in recursion
                        $this->lessBuilder($child, $less, $level+1, $_path);

                        // Close selector
                        $less .= "\n".$this->spacer($level).'}';
                    }
                } else { // single element
                    $less .= "\n".$this->spacer($level).$selector.' {  }';
                }

            }
        }
    }

    /**
     * Controller for building .less skeleton from html tree
     * @param string $path View path
     */
    public function __less($path)
    {
        s()->async(true);

        $less = '';

        // Find path to view
        $_path = m('local')->findView($path);

        if (file_exists($_path)) {

            // Read it
            $html = file_get_contents($_path);

            // Remove all PHP code from view
            $html = preg_replace('/<\?php.*?\?>/', '', $html);

            // Parse HTML
            $doc = new \DOMDocument();
            $doc->loadHTML($html);

            $this->lessBuilder($doc, $less);

            file_put_contents('css/test.less2', $less);

        } else {
            e('View file not found(##)', E_SAMSON_CORE_ERROR, $path);
        }
    }

    /**
     * Controller for generating local module
     * @param string $name Local module name
     */
    public function __generate($name)
    {
        if( isset($name{0}) ) {

            // Create view folder for module
            $view = __SAMSON_VIEW_PATH.$name;
            $this->createDir($view);
            $view .= '/index.php';
            if(!file_exists($view)) {
                // Create view file
                file_put_contents($view,
'<!-- View['.$name.'] was automatically generated by SamsonPHP/Skeleton application on '.date('d.m.y H:i').'-->

'
                );
            }

            // If controller not exists
            $controller = __SAMSON_CONTOROLLER_PATH;
            $this->createDir($controller);
            $controller .= $name.'.php';
            if(!file_exists($controller)) {
                // Create controller file
                file_put_contents($controller,
'<?php /** Controller['.$name.'] was automatically generated by SamsonPHP/Skeleton application on '.date('d.m.y H:i').' */
function '.$name.'__HANDLER()
{
    // Your code here
    m()->view("'.$name.'/index")->title("'.$name.'");
}
'
                );
            }

            // Create less file
            $less = 'css';
            $this->createDir($less);
            $less .= '/'.$name.'.less';
            if(!file_exists($less)) {
                // Create less file
                file_put_contents($less,
'/* LESS['.$name.'] was automatically generated by SamsonPHP/Skeleton application on '.date('d.m.y H:i').' */
#'.$name.'{

}'
                );
            }

            // Create less file
            $js = 'js';
            $this->createDir($js);
            $js .= '/'.$name.'.coffee';
            if(!file_exists($js)) {
                // Create coffee file
                file_put_contents($js,
'# COFFEE['.$name.'] was automatically generated by SamsonPHP/Skeleton application on '.date('d.m.y H:i').'

'
                );
            }

            elapsed('Local module '.$name.' was successfully created');
        }

        $this->html(' ');
    }
}

