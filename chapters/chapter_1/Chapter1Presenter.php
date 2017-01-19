<?php

namespace App\Presenters;

use Nette;


class Chapter1Presenter extends Nette\Application\UI\Presenter
{
	// if i use bp write it to bp
	protected function afterRender() {
		$this->redrawControl('content');
	}
}
