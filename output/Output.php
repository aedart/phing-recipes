<?php

require_once 'phing/Task.php';

/**
 * Output is more or less a mini-version of Phing's EchoTask,
 * with the exception that this task doesn't use the logging
 * or OutputSteam.
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
     * @var string
     */
    private $_msg = "";
    
    public function main(){
	print($this->_msg . PHP_EOL);
    }
    
    /**
     * Setter for the attr 'message'
     * 
     * @param string $msg
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
        $this->setMessage($msg);
    }
}
