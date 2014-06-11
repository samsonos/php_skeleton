<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 26.05.14 at 16:01
 */
 namespace samsonos\php\skeleton;

/**
 * Class for manipulating
 * @author Vitaly Egorov <egorov@samsonos.com>
 * @copyright 2013 SamsonOS
 * @version
 */
class Tree
{
    /** Source html tree */
    public $html = '';

    /** @var Node[] Collection of LESS nodes */
    protected $nodes = array();

    /** @var \DOMNode Pointer to current dom element */
    protected $dom;

    /** @var Node Pointer to current less element */
    protected $less;

    protected $path;

    /** @var array Collection of ignored DOM nodes */
    public static $ignoredNodes = array(
        'head',
        'meta',
        'script',
        'link',
        'title',
        'br'
    );

    /**
     * Create less node tree from file
     * @param string $path Path to file for analyzing
     */
    public function __construct($path = null)
    {
        // If file exists
        if (file_exists($path)) {

            // Read it
            $this->html = file_get_contents($path);

        } else if(isset($path)) {
            return e('Cannot read view file[##]', E_SAMSON_CORE_ERROR, $path);
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
     * Inner recursive output LESS generator
     *
     * @param Node   $node
     * @param string $output
     * @param int    $level
     */
    protected function _toLESS(array $node, & $output = '', $level = 0)
    {
        foreach ($node as $key => $child) {

            $output .= "\n".$this->spacer($level).$key.' {';

            $this->_toLESS($child, $output, $level+1);

            $output .= "\n".$this->spacer($level).'}';
        }
    }

    /**
     * Generate LESS code from current LESS Node tree
     *
     * @param string $html HTML code to parse
     *
     * @return string Generated LESS code from tree
     */
    public function toLESS($html = null)
    {
        // Set new HTML code if passed
        if(isset($html)) {
            $this->html = trim($html);
        }

        $output = '';

        // If HTML is not empty
        if (isset($this->html{0})) {
            // Remove all PHP code from view
            $this->html = preg_replace('/<\?php.*?\?>/', '', $this->html);

            // Parse HTML
            $this->dom = new \DOMDocument();
            $this->dom->loadHTML($this->html);

            // Create empty top LESS Node
            $this->less = new Node($this->dom);

            // Generate LESS Node tree
            $this->handleNode($this->dom, $this->less, $this->path);

            // Generate recursively LESS code
            $this->_toLESS($this->path, $output);

        } else {
            $output = 'Nothing to convert =(';
        }

        return $output;
    }

    /**
     * Handle current DOM node and transform it to LESS node
     * @param \DOMNode $node Pointer to current analyzed DOM node
     * @param Node     $parent  Pointer to parent LESS Node
     */
    protected function handleNode(\DOMNode & $node, Node & $parent = null, & $path = array(), $level = true)
    {


        // Get all current level valid DOM nodes
        /** @var \DOMNode[] $group */
        $children = $this->getValidChildren($node);

        foreach ($children as $tag => $child) {
            $childNode = new Node($child, $parent);

            if(sizeof($childNode->class) == 0 ) {

                if(!isset($path[$child->nodeName])) {
                    $path[$child->nodeName] = array();
                }

                $this->handleNode($child, $parent, $path[$child->nodeName]);

            } else {
                foreach($childNode->class as $class) {

                    $class = '&.'.$class;

                    if(!isset($path[$child->nodeName][$class])) {
                        $path[$child->nodeName][$class] = array();
                    }

                    $this->handleNode($child, $parent, $path[$child->nodeName][$class], false);
                }
            }
        }
    }

    /**
     * Gather only valid DOM node children nodes, ignore text nodes and
     * special ignored nodes
     *
     * @see Tree::$ignoredNodes
     *
     * @param \DOMNode $node    Pointer to DOM node for analyzing
     *
     * @return \DOMNode[] Collection of valid children DOM nodes grouped by node name as keys
     */
    protected function & getValidChildren(\DOMNode & $node)
    {
        $nodes = array();

        // Collect normal HTML nodes and those who in not ignored at this level
        foreach($node->childNodes as $child) {
            // Work only with DOMElements
            if($child->nodeType == 1 && !in_array($child->nodeName, self::$ignoredNodes)) {

               /* // Group node by html tag name
                if (!isset($nodes[$child->nodeName])) {
                    $nodes[$child->nodeName] = array();
                }*/

                // Add node to a group
                $nodes/*[$child->nodeName]*/[] = $child;
            }
        }

        return $nodes;
    }
}
 