<?php

/**
 * @todo AvailableTargetList
 *
 * @author Alin Eugen Deac
 */
class AvailableTargetList extends Task{
    
    /**
     * Contains the available targets
     * 
     * @var array
     */
    protected $targets = [];
    
    /**
     * The property to which the available
     * targets must be set to
     * 
     * @var string
     */
    protected $property;
    
    /**
     * Constructor
     */
    public function __construct() {
	// Getting the current project's available targets
	// is a simple as the following line of code.
	// Sadly, the list doesn't give you any information
	// about the origin of the targets (from what build file)
	// $targetsList = $this->project->getTargets();	
	
	// NB: It is not possible to perform the getAvailableTargetList()
	// at this point, since not all properties have been set for
	// either this tasks project, or the task it self.
    }
    
    /**
     * Returns the available targets inside this project,
     * based on the import stack
     * 
     * @return array
     */
    protected function getAvailableTargetList(){
	// The target list
	$list = [];
	
	// Get the import stack of this current project
	$parser = $this->getProject()->getReference('phing.parsing.context');
	$imporstStack = $parser->getImportStack();
	
	// Loop through all the imported build files
	// and build a new data structure of their targets
	foreach ($imporstStack as $key => $filePath){
	    // NOTE: The code below is more or less a copy from
	    // ProjectConfigurator - without invoking any target
	    // methods/main().
	    
	    // Create a new PhingFile
	    $file = new PhingFile($filePath);
	    $file = new PhingFile($file->getCanonicalPath()); // Don't know why this is needed, but wrong path is used, if not!?

	    // Create a new tmp Phing Project
	    $newProject = new Project();
	    $newProject->setSystemProperties();
	    $newProject->setUserProperty("phing.file", $file);
	    
	    // Parse
	    $this->_parse($newProject, $file);
	    
	    // Add the given tmp project, and its targets to the list
	    $list[] = $this->parseProjectToArray($newProject);
	}
	
	// return the list
	return $list;
    }
    
    /**
     * Parse the given project / build file
     * 
     * @param Project $project
     * @param PhingFile $file
     */
    private function _parse(Project $project, PhingFile $file){
	    // New XML Context, used for parsing / configuration
	    $ctx = new PhingXMLContext($project);
            $project->addReference("phing.parsing.context", $ctx);
	    $ctx->addImport($file);
	    $ctx->setCurrentTargets(array());
	    
	    // push action onto global stack
	    $ctx->startConfigure($this);

	    $reader = new BufferedReader(new FileReader($file));
	    $parser = new ExpatParser($reader);
	    $parser->parserSetOption(XML_OPTION_CASE_FOLDING,0);
	    $parser->setHandler(new RootHandler($parser, new ProjectConfigurator($project, $file), $ctx));
	    $parser->parse();
	    $reader->close();

	    // mark parse phase as completed
	    $this->isParsing = false;

	    // pop this action from the global stack
	    $ctx->endConfigure();
    }

    /**
     * Parses the given project to an array, containing
     * the project name, description and its available
     * targets (which are also parsed to an array)
     * 
     * @param Project $project
     * @return array
     */
    protected function parseProjectToArray(Project $project){
	$projectArr = array(
	    'name'	    =>  $project->getName(),
	    'description'   =>	$project->getDescription(),
	    'buildFile'	    =>	$project->getProperty('phing.file')->getPath(),
	    'defaultTarget' =>	$project->getDefaultTarget(),
	    'targets'	    =>	[]
	);
	
	// Get project targets
	$targets = $project->getTargets();
	
	// Sort the targets according to their name
	ksort($targets);
	
	// Parse the targets to an array
	foreach ($targets as $key => $target){
	    // Don't add the first target, because its
	    // an empty-system-target Phing uses...
	    if(!empty($key)){
		// Add the target to the list
		$projectArr['targets'][] = $this->parseTargetsToArray($target);
	    }
	}
	
	// Return the proejct array
	return $projectArr;
    }

    /**
     * Parse the given target to an array and return it
     * 
     * @param Target $target
     * @return array
     */
    protected function parseTargetsToArray(Target $target){
	$targetArr = array(
	    'name'	    =>	$target->getName(),
	    'description'   =>	$target->getDescription(),
	    'isHidden'	    =>	$target->isHidden()
	    
	    // NOTE: Its possible to add more information here,
	    // should it be needed
		
	);
	
	return $targetArr;
    }
    
    public function main() {
	// Check if a property name has been set
	if($this->property === null){
	    throw new BuildException("You must specify a property name that must hold the available targets", $this->getLocation());
	}
	
	// Set the available targets
	$this->targets = $this->getAvailableTargetList();
	
	// Store the available targets into the property,
	// which has been provided
	$this->project->setProperty($this->property, $this->targets);
	
	// Set the log
	$this->log("Available Targets List stored inside property: " . $this->property, Project::MSG_VERBOSE);
	
	// Echo test
//	foreach($this->targets as $key => $project){
//	    echo 'Name: ' . $project['name'] . PHP_EOL;
//	    echo 'Desc: ' . $project['description'] . PHP_EOL;
//	    echo 'Default Target: ' . $project['defaultTarget'] . PHP_EOL;
//	    echo 'File: ' . $project['buildFile'] . PHP_EOL;
//	    print_r($project['targets']) . PHP_EOL;
//	    echo PHP_EOL . PHP_EOL;
//	}
    }

    /**
     * Set the name of the property that must contain
     * the available targets
     * 
     * @param string $name
     */
    public function setProperty($name){
	$this->property = (string) $name;
    }
    
}
