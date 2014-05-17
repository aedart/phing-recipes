<?php

require_once 'phing/Task.php';
require_once 'phing/Target.php';
//require_once 'phing/system/util/Properties.php';
require_once 'phing/system/io/PhingFile.php';
include_once 'phing/parser/ProjectConfigurator.php';
include_once 'phing/tasks/system/PhingTask.php';

/**
 * @todo AvailableTargetList
 *
 * @author Alin Eugen Deac
 */
class AvailableTargetList extends Task{
    
    public function __construct() {
	echo __CLASS__ . ' invoked' . PHP_EOL . PHP_EOL;
    }
    
    public function main() {
	// Getting the current project's available targets
	// is a simple as the following line of code.
	// Sadly, the list doesn't give you any information
	// about the origin of the targets (from what build file)
	// $targetsList = $this->project->getTargets();
		
	$targetsList = $this->project->getTargets();
	foreach ($targetsList as $key => $value){
	    
	    if(!empty($key)){
		echo 'key: ' . $key . PHP_EOL;
		echo 'Key type: ' . gettype($key) . PHP_EOL;

		$target = $value;
		echo 'Name: ' . $target->getName() . PHP_EOL;
		echo 'Desc: ' . $target->getDescription() . PHP_EOL;
		echo 'isHidden: ' . $target->isHidden() . PHP_EOL;
		
		$currentProject = $target->getProject();
		echo 'Project: ' . $currentProject->getName() . PHP_EOL;
		echo 'Project desc: ' . $currentProject->getName() . PHP_EOL;
		
//		$parser = $currentProject->getReference('phing.parsing.context');
//		$imporstStack = $parser->getImportStack();
//		foreach($imporstStack as $k => $v){
//		    echo '  ' . $k . '   ' . $v . PHP_EOL;
//		}
		
		
		echo PHP_EOL.PHP_EOL;
	    }
	}
	
	// useless...
//	$ref =  $this->project->getReferences();
//	foreach($ref as $k => $v){
//	    echo '  k: ' . $k . PHP_EOL;
//	}
	
	// Imporst stack - all the build scripts loaded into this project
	echo PHP_EOL . PHP_EOL . 'Import Stack: ' . PHP_EOL;
	$parser = $this->getProject()->getReference('phing.parsing.context');
	$imporstStack = $parser->getImportStack();
	foreach($imporstStack as $k => $v){
	    echo '  ' . $k . '   ' . $v . PHP_EOL;
	}
		
	// Imporst stack - all the build scripts loaded into this project
	echo PHP_EOL . PHP_EOL . 'Import Stack (loading props): ' . PHP_EOL;
	$parser = $this->getProject()->getReference('phing.parsing.context');
	$imporstStack = $parser->getImportStack();
	foreach($imporstStack as $k => $v){
	    echo '  ' . $k . '   ' . $v . PHP_EOL;
	    
	    // Incorrect usage of PhingFIle
	    $file = new PhingFile($v);
	    $file = new PhingFile($file->getCanonicalPath());
	    echo 'Absolute file: ' . $file->getAbsoluteFile() . PHP_EOL;
	    echo 'Absolute path: ' . $file->getAbsolutePath() . PHP_EOL;
	    echo 'Canonical File: ' . $file->getCanonicalFile() . PHP_EOL;
	    echo 'Canonical Path: ' . $file->getCanonicalPath() . PHP_EOL;
	    echo PHP_EOL . PHP_EOL;
	    
	    // ERROR - cannot open any of the files!?
	    $newProject = new Project();
	    $newProject->setSystemProperties();
	    $newProject->setUserProperty("phing.file", $this->phingFile);
	    
	    // @todo: Look at project Config - study it, and parse the fucking
	    // xml-file your self, without starting to invoke other imports!!!
	    ProjectConfigurator::configureProject($currentProject, $file);
	    
	    echo 'Project name: ' . $newProject->getName() . PHP_EOL;
	    
	    $t = $newProject->getTargets();
	    foreach ($t as $tK => $tV){
		echo '	    ' . $tK . PHP_EOL;
	    }
	}
    }

}
