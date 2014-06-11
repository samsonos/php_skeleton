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
    protected function _toLESS(Node $node, & $output = '', $level = 0)
    {
        foreach ($node->children as $child) {

            $output .= "\n".$this->spacer($level).$child->selector.' {';

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
            $this->handleNode($this->dom, $this->less);

            // Generate recursively LESS code
            $this->_toLESS($this->less, $output);

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
    protected function handleNode(\DOMNode & $node, Node & $parent = null)
    {
        // Get all current level valid DOM nodes
        /** @var \DOMNode[] $group */
        foreach($this->getValidChildren($node) as $tag => $group) {
            // If this tag meets more then one time
            if(sizeof($group) > 1){
                // Create group LESS node instance
                $groupNode = new Node($group[0], $parent);

                // Added created node as child
                $parent->children[] = $groupNode;

                // Iterate grouped DOM nodes
                foreach($group as $child) {

                    // Create LESS node instance
                    $lessNode = new Node($child, $groupNode);

                    // Iterate node classes to find mismatch with group parent
                    for($i = 0; $i < sizeof($lessNode->class); $i++) {
                        trace('Comparing nodes: '.$lessNode->class[$i].'-'.$groupNode->class[$i]);
                        if($lessNode->class[$i] != $groupNode->class[$i]) {
                            $lessNode->selector = '.'.$lessNode->class[$i];
                            break;
                        }
                    }

                    // Ignore equal selector as parent
                    if ($lessNode->selector != $groupNode->selector) {

                        // Add special LESS parent marker
                        $lessNode->selector = '&'.$lessNode->selector;

                        // Added created node as child
                        $groupNode->children[] = $lessNode;

                        // Go deeper in recursion with new LESS node as parent
                        $this->handleNode($child, $lessNode);

                    } else { // Go deeper in recursion with current group node as parent
                        $this->handleNode($child, $groupNode);
                    }
                }

                // If this node has multiple classes
                if(sizeof($groupNode->class) > 1) {
                    // Iterate all other classes except first one as it node already created
                    for($i = 1; $i < sizeof($groupNode->class); $i++) {

                        // Create LESS node instance
                        $lessNode = new Node($group[0], $groupNode);

                        // Add special LESS parent marker
                        $lessNode->selector = '&.'.$groupNode->class[$i];

                        // Added created node as child
                        $groupNode->children[] = $lessNode;

                        // Go deeper in recursion with new LESS node as parent
                        $this->handleNode($group[0], $lessNode);
                    }
                }

            } else {
                // Iterate grouped DOM nodes
                foreach($group as $child) {

                    // Create LESS node instance
                    $lessNode = new Node($child, $parent);

                    // Added created node as child
                    $parent->children[$lessNode->selector] = $lessNode;

                    // Go deeper in recursion
                    $this->handleNode($child, $lessNode);
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

                // Group node by html tag name
                if (!isset($nodes[$child->nodeName])) {
                    $nodes[$child->nodeName] = array();
                }

                // Add node to a group
                $nodes[$child->nodeName][] = $child;
            }
        }

        return $nodes;
    }
}
 