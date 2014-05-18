<?php

require_once 'phing/Task.php';

/**
 * Output is a mini-version of Phing's EchoTask, with the
 * exception that this task doesn't use the logging or OutputSteam.
 *
 * Use this task when you are sure, that you don't need to
 * make use of logging levels, etc.
 * 
 * @author Alin Eugen Deac
 */
class Output extends Task{
    
    /**
     * The messsage to be printed in the
     * given console window
     * 
     * OR - can also be an object, in which
     * case the _varDump should be set to
     * true
     * 
     * @var string
     */
    private $_msg = "";
    
    /**
     * If true, this task will print
     * using PHP's var_export, instead
     * of just printing the message
     * 
     * @var mixed
     */
    private $_varDump = false;
    
    /**
     * Name of a property that should be
     * var dumped
     * 
     * @var string
     */
    private $_propertyName;
    
    public function main(){
	// Normal output
	print((string) $this->_msg . PHP_EOL);
	
	// Check if a property name has been provided
	if(!empty($this->_propertyName)){
	    // We use the getProperties, to avoid eventual
	    // parsing issues with the property, should it be
	    // used in another context
	    $prop = $this->project->getProperties()[$this->_propertyName];
	    if(is_null($prop)){
		throw new BuildException("Property '" . $this->_propertyName . "' could not be found. Cannot var dump given property", $this->getLocation());
	    }
	    
	    // Determine if the property should be var dumped or
	    // just printed out as it is (string conversion attempt)
	    if($this->_varDump){
		var_dump($prop);
	    } else {
		print((string) $prop . PHP_EOL);
	    }
	}
    }
    
    /**
     * Flag - if a given property should be var dumped
     * 
     * @param boolean $dump
     */
    public function setVarDump($dump){
	$this->_varDump = (bool) $dump;
    }
    
    /**
     * name of a property that should be var dumped
     * 
     * @see setVarDump
     * 
     * @param string $name
     */
    public function setPropertyName($name){
	$this->_propertyName = (string) $name;
    }
    
    /**
     * Setter for the attr 'message'
     * 
     * NOTE: message can now actually be some other property
     * 
     * @param srting $msg
     */
    public function setMessage($msg) {
        $this->_msg = (string) $msg;
    }
    
    /**
     * Alias for attr 'message'
     * Setter for the attr 'msg'
     * 
     * @param string $msg
     * 
     * @see Output->setMessage
     */
    public function setMsg($msg) {
        $this->setMessage($msg);
    }
    
    /**
     * Support for the <output>Message</output> syntax.
     * @param string $msg
     * 
     * @see EchoTask
     */
    function addText($msg){	
	// Trim message
	$m = trim($msg);
	
	// Replace tabs and spaces
	$m = preg_replace('/\t\s+/', '', $m);
	
	// Finally, set the message
        $this->setMessage($m);
    }
}
