<?php
namespace AnsPressToBuddyPressLoader\Interfaces\iAPtoBPLoader;

interface iAPtoBPLoader
{	
	public function install();
	
	public function uninstall();
	
	public function getBPCategory();
	
	public function getAPCategory();
	
	public function getQuestionsAPByCategory();
	
	public function getAnswersAP();
}