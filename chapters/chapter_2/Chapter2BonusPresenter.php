<?php

namespace App\Presenters;

use Nette;

// if i use bp extend the bp

class Chapter2BonusPresenter extends \Nittro\Bridges\NittroUI\Presenter
{
	protected function startup()
	{
		parent::startup();
		$this->setDefaultSnippets(['content', 'menu']);
	}

}
