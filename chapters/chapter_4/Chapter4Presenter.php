<?php


namespace App\Presenters;

use App\Components\Chapter4CartControl;
use Nittro\Bridges\NittroUI\Presenter;


class Chapter4Presenter extends Presenter
{

    public function handleInfo()
    {
        $this->flashMessage('This is an info message', 'info');
        $this->postGet('this');
        $this->redrawControl('clock');
    }

    public function handleWarning()
    {
        $this->flashMessage('This is a warning message', 'warning');
        $this->postGet('this');
        $this->redrawControl('clock');
    }

    public function handleError()
    {
        $this->flashMessage('This is an error message', 'error');
        $this->postGet('this');
        $this->redrawControl('clock');
    }


    public function handleAddToCart($amount) {
        $this->getComponent('cart')->add($amount);
    }

    public function createComponentCart() {
        return new Chapter4CartControl();
    }
}