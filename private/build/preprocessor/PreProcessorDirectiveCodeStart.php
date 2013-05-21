<?php

/**
 * Code start directive
 * 
 * @author Thomas Muguet <t.muguet@thomasmuguet.info>
 */
class PreProcessorDirectiveCodeStart extends PreProcessorDirective
{

    /**
     * Initializes a new code start
     * @param PreProcessorDirective $parent Parent directive
     */
    public function __construct(PreProcessorDirective &$parent)
    {
        parent::__construct($parent);
    }

    /**
     * Processes this block
     * 
     * @param PreProcessorContext $context Context
     * @return array Array of strings
     */
    public function process(PreProcessorContext &$context)
    {
        $context->loadCode();
        
        $content = array();
        foreach ($this->subblocks as $subblock) {
            $content = array_merge($content, $subblock->process($context));
        }
        $return = eval(implode("\n", $content));
        if ($return === FALSE) {
            throw new Exception("Error in code: ".implode("\n", $content));
        }
        
        return explode("\n", $return);
    }
}