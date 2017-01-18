<?php

namespace App\Presenters;

use Nette;

// if i use bp extend the bp

class Chapter2TraitPresenter extends Nette\Application\UI\Presenter
{
	use \Nittro\Bridges\NittroUI\PresenterUtils;

	// 1
	protected function startup()
	{
		parent::startup();
		$this->setRedrawDefault($this->getSignal() === null);
	}

	// 2
	protected function afterRender()
	{
		parent::afterRender();
		$this->redrawDefault();
	}

}
