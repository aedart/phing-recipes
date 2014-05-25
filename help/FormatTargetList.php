<?php


/**
 * Format Target List
 *
 * Helper task that formats and outputs a list
 * of available targets
 * 
 * NOTE: This task is very specific for the help.xml build-script,
 * therefore, it might not be useful in any other context!
 * 
 * @see list/AvailableTargetList
 * 
 * @author Alin
 */
class FormatTargetList extends Task{
    
    /**
     * Name of the property that holds
     * the available targets
     * 
     * @var string
     */
    private $_propertyName;
    
    /**
     * The list of available targets
     * 
     * @var array
     */
    private $_list;
    
    public function main() {
	// Check if property name set
	if($this->_propertyName === null){
	     throw new BuildException("You must specify the property name that contains the available targets", $this->getLocation());
	}
	
	// Get the list - if available
	$list = $this->project->getProperties()[$this->_propertyName];
	if(is_null($list)){
	    throw new BuildException("Property '" . $this->_propertyName . "' could not be found. Cannot format available targets", $this->getLocation());
	}
	
	// Set this task's list
	$this->_list = $list;
	
	// Format and output the list
	echo $this->formatList();
    }

    /**
     * Set the name of the property that holds the
     * given list of available targets
     * 
     * @param string $name
     */
    public function setListPropertyName($name){
	$this->_propertyName = (string) $name;
    }
    
    /**
     * Formats the entire list, foreach available target
     * 
     * @return string
     */
    protected function formatList(){
	// The output string
	$output = '';
	
	// Loop through all projects in the list
	foreach($this->_list as $key => $project){
	    // Format the project
	    $output .= $this->formatProject($project);
	    
	    // The default target
	    $defaultTarget = $project['defaultTarget'];
	    
	    // The available targets
	    foreach($project['targets'] as $k => $target){
		// Is default flag
		$isDefault = false;
		
		// Is hidden flag
		$isHidden = false;
		
		// Determine if this target is the default or not
		if(!is_null($defaultTarget) && $defaultTarget['name'] == $target['name']){
		    // Means that this should be output as the default
		    $isDefault = true;
		}
		
		// Determine if this is a hidden target
		if($target['isHidden']){
		    $isHidden = true;
		}
		
		// Format the target
		$targetOutput = $this->formatTarget($target, $isDefault, $isHidden);
		
		// Append target to output
		$output .= $targetOutput;
	    }
	    
	    // Append double end-of-line
	    $output .= PHP_EOL .PHP_EOL;
	}
	
	// Finally, return the the output
	return $output;
    }
    
    /**
     * Formats a project
     * 
     * @param array $project
     * @return string
     */
    protected function formatProject(array $project){
	// Heading
	$output = $this->getLine();
	
	// Name
	$name = ' ' . $project['name'] . ':';
	
	// Desc
	$desc = ' ' . $project['description'];
	
	// Append the name and description to the output
	$output .= $this->wordWrap($name . $desc) . PHP_EOL;
	
	// Build file path
	$output .= ' (' . $project['buildFile'] . ')' . PHP_EOL;
	
	// End heading
	$output .= $this->getLine();	
	
	return $output;
    }
    
    /**
     * Formats a target
     * 
     * @param array $target
     * @param boolean $isDefaultTarget		If true, this target will be displayed with a DEFAULT flag
     * @param boolean $isHidden			If true, this target will be displayed with a HIDDEN flag
     * @return string
     */
    protected function formatTarget(array $target, $isDefaultTarget = false, $isHidden = false){
	// Output
	$output = '';
	
	// Default flag
	$defaultFlag = '';
	if($isDefaultTarget){
	    $defaultFlag = ' [Default]';
	}
	
	// Hidden flag
	$hiddenFlag = '';
	if($isHidden){
	    $hiddenFlag = ' (hidden)';
	}
	
	// Format the target name. NOTE: since creating tabs can be
	// problematic, in terms of getting it right, we are just
	// appending empty chars to the name string, until a max
	// amount has been reached. This way, the identation between
	// the target name and description, should be the same, unless
	// the name is above the max limit.
	// 
	// Also the default flag and hidden flag are added here.
	// If the target is default nad or hidden, then it will
	// increase the name string length
	$nameStr = ' ' . $target['name'] . $defaultFlag . $hiddenFlag;
	
	$nameLenghtMax = 30;
	$nameStrLength = strlen($nameStr);
	$nameLengthDiff = $nameLenghtMax - $nameStrLength;
	if($nameLengthDiff > 0){
	    $nameStr .= str_repeat(' ', $nameLengthDiff);
	}
		
	// Format description
	$descStr = ' ' . $target['description'];
		
	// Append name and description to the output
	$output = $this->wordWrap($nameStr . $descStr, $nameLenghtMax) . PHP_EOL;
	
	return $output;
    }
    
    /**
     * Custom word wrap method, which can add identation to each new line 
     * 
     * @param string $msg
     * @param integer $indentAmountPerNewLine		[OPTIONAL][Default 0 (zero)] Amount of whitespace to ident new lines with
     * @return string
     */
    protected function wordWrap($msg, $indentAmountPerNewLine = 0){
	// Default output the message
	$output = $msg;
	
	// Max amount of chars in a line (at least in windows cmd!)
	// 80 chars actually, but a end-of-line would also count
	// as at least one char!
	$maxCharsInLine = 79;
	
	// Check if message is above limit
	if(strlen($msg) > $maxCharsInLine){
	    // Use PHP native word-wrap method
	    $newMsg = wordwrap($msg, $maxCharsInLine, PHP_EOL, true);
	    
	    // Split the msg by the delimitor, into an array of strings
	    $newMsgArr = split(PHP_EOL, $newMsg);
	    
	    // Append the first line to the output (and remove it from the arr)
	    // The end-od-line is very important here - or the new lines might
	    // not get indented correctly
	    $output = array_shift($newMsgArr) . PHP_EOL;
	    
	    // Join the evt. remaining lines with an empty space
	    $remainingLines = implode(' ', $newMsgArr);
	    
	    // Add identation to the remaining lines (if any needed)
	    $indentedLines = str_repeat(' ', $indentAmountPerNewLine) . ' ' . $remainingLines;
	    
	    // Add these lines to the output - however, perform a recursive call
	    // to this method, to ensure that the max chars havn't been reached.
	    // also, identation will den be perserved
	    $output .= $this->wordWrap($indentedLines, $indentAmountPerNewLine);
	}
	
	// Return the output
	return $output;
    }
    
    /**
     * Returns a string line to be displayed in a console
     * 
     * NOTE: A line (at least for windows console) consists
     * of 80 chars.
     *      
     * @return string
     */
    protected function getLine(){
	// Create the line
	$lineStr = str_repeat('- - - - - ', 8);
	
	// Remove the last char of the line and add end-of-line
	$lineStr = substr($lineStr, 0, -1) . PHP_EOL;
	
	// Return line
	return $lineStr;
    }
}
