<?php

require_once 'phing/Task.php';
require_once 'phing/Target.php';
//require_once 'phing/system/util/Properties.php';
require_once 'phing/system/io/PhingFile.php';
include_once 'phing/parser/ProjectConfigurator.php';

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
	    
	    $file = new PhingFile($v);
	    $currentProject = new Project();
	    ProjectConfigurator::configureProject($currentProject, $file);
	    $t = $currentProject->getTargets();
	    foreach ($t as $tK => $tV){
		echo '	    ' . $tK . PHP_EOL;
	    }
	}
    }

}
