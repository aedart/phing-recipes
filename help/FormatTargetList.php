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
	$output = '';
	
	foreach($this->_list as $key => $project){
	    // Projects
	    $output .= $this->formatProject($project);
	    
	    // The default target
	    
	    // The available targets
	    foreach($project['targets'] as $k => $target){
		//if(!$target['isHidden']){
		    $output .= $this->formatTarget($target);
		//}
	    }
	    
	    $output .= PHP_EOL .PHP_EOL;
	}
	
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
	$output .= ' ' . $project['name'] . ': ';
	
	// Desc
	$output .= '' . $project['description'] . PHP_EOL;
	
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
     * @return string
     */
    protected function formatTarget(array $target){
	// Heading
	$output = '';
	
	// Name and description
	//$output .= ' ' . $target['name'] . '' . $target['description'] . PHP_EOL;
	
	//$output .= ' ' . $target['name'] . PHP_EOL;
	//$output .= '	' . $target['description'] . PHP_EOL . PHP_EOL;
	
	$output .= ' ' . $target['name'] . '          | ' . $target['description'] . PHP_EOL . PHP_EOL;
	
	$z = '';
	for($i = 0; $i < 45; $i++){
	    $z .= $i;
	}
	
	$output .= $z . PHP_EOL . PHP_EOL;
	
	$output .= 'z count: ' . strlen($z) . ' ' . PHP_EOL;
	
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
