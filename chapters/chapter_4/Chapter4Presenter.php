<?php


namespace App\Presenters;

use Nittro\Bridges\NittroUI\Presenter;


class Chapter4Presenter extends Presenter
{
    public function handleInfo()
    {
        $this->flashMessage('This is an info message', 'info');
        $this->redrawControl('content');
    }

    public function handleWarning()
    {
        $this->flashMessage('This is a warning message', 'warning');
        $this->redrawControl('content');
    }

    public function handleError()
    {
        $this->flashMessage('This is an error message', 'error');
        $this->redrawControl('content');
    }
}