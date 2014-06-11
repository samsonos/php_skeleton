<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 26.05.14 at 16:00
 */
 namespace samsonos\php\skeleton;

/**
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 * @copyright 2013 SamsonOS
 * @version 
 */
class Node 
{
    /** @var string HTML tag name */
    public $tag;

    /** @var string[] CSS class names collection */
    public $class = array();

    /** @var string HTML identifier */
    public $id;

    /** @var string HTML name attribute value */
    public $name;

    /** @var  string LESS selector for node */
    public $selector;

    /** @var \DOMNode pointer to DOM node */
    public $dom;

    /** @var Node pointer to parent node */
    public $parent;

    /** @var Node[] Collection of nested nodes */
    public $children = array();

    /**
     * Create LESS Node
     *
     * @param \DOMNode $node        Pointer to DOM node
     * @param Node     $parent      Pointer to parent LESS Node
     * @param string   $selector    Forced LESS node selector
     */
    public function __construct(\DOMNode & $node, Node & $parent = null, $selector = null)
    {
        // Store pointer to DOM node
        $this->dom = & $node;

        // Store html tag
        $this->tag = $node->nodeName;

        // Pointer to parent LESS Node
        $this->parent = & $parent;

        // Fill all available node parameters
        if (isset($node->attributes)) {
            /**@var $attribute \DOMNode */
            foreach ($node->attributes as $attribute) {
                $value = trim($attribute->nodeValue);
                if($attribute->name == 'class' && strlen($value)) {
                    $this->class[] = $value;
                } else if($attribute->name == 'id' && strlen($value)) {
                    $this->id = $value;
                } else if($attribute->name == 'name' && strlen($value)) {
                    $this->name = $value;
                }
            }
        }

        $this->selector = $selector;
        if(!isset($selector)) {
            // Choose default LESS selector for node
            $this->selector = $this->tag;
            if (isset($this->class[0])) {
                $this->selector = '.'.$this->class[0];
            }
        }
    }

    /**
     * @return string Object string representation
     */
    public function __toString()
    {
        return $this->tag.'['.$this->selector.']';
    }
}
 