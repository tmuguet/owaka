<?php

/**
 * Context for the pre-processor
 * @author Thomas Muguet <t.muguet@thomasmuguet.info>
 */
class PreProcessorContext
{

    /**
     * Definitions
     * @var array 
     */
    protected $definitions = array();

    /**
     * Macros
     * @var array 
     */
    protected $macros = array();
    private $macrodir        = NULL;
    private $hasParsedMacros = FALSE;
    
    private $codedir = NULL;
    private $hasParsedCode = FALSE;
    
    public function __construct()
    {
        $this->definitions['TRUE'] = TRUE;
        $this->definitions['FALSE'] = FALSE;
    }

    public function addDefinition($name, $value)
    {
        $this->definitions[$name] = $value;
    }

    public function hasDefinition($name)
    {
        return array_key_exists($name, $this->definitions);
    }

    public function getDefinition($name)
    {
        return $this->definitions[$name];
    }

    public function setMacroDir($macrodir)
    {
        $this->macrodir = $macrodir;
    }

    public function hasMacro($name)
    {
        if (!$this->hasParsedMacros) {
            $this->parseMacros();
        }
        return array_key_exists($name, $this->macros);
    }

    public function getMacro($name)
    {
        if (!$this->hasParsedMacros) {
            $this->parseMacros();
        }
        return $this->macros[$name];
    }

    private function parseMacros()
    {
        if ($this->macrodir === NULL || !is_dir($this->macrodir)) {
            throw new Exception("Cannot find macro directory : " . $this->macrodir);
        }

        $files = array_diff(scandir($this->macrodir), array('..', '.'));
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (array_key_exists($name, $this->macros)) {
                throw new Exception("Macro $name is already defined");
            }
            $macroDef = file_get_contents($this->macrodir . DIRECTORY_SEPARATOR . $file);
            $args     = explode(',',
                                substr($macroDef, 0, strpos($macroDef, "\n")));
            $content  = substr($macroDef, strpos($macroDef, "\n") + 1);

            $macro = new PreProcessorMacro();
            foreach ($args as $arg) {
                $macro->addVariable($arg);
            }
            $macro->setContent($content);

            $this->macros[$name]   = $macro;
        }
        $this->hasParsedMacros = TRUE;
    }

    public function setCodeDir($codedir)
    {
        $this->codedir = $codedir;
    }
    
    public function loadCode() {
        if (!$this->hasParsedCode) {
            $this->parseCode();
        }
    }

    private function parseCode()
    {
        if ($this->codedir === NULL || !is_dir($this->codedir)) {
            throw new Exception("Cannot find code directory : " . $this->codedir);
        }

        $files = array_diff(scandir($this->codedir), array('..', '.'));
        foreach ($files as $file) {
            require_once $this->codedir . DIRECTORY_SEPARATOR . $file;
        }
        $this->hasParsedCode = TRUE;
    }
}
