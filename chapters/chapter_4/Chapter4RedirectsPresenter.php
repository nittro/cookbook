<?php


namespace App\Presenters;

use Nittro\Bridges\NittroUI\Presenter;


class Chapter4RedirectsPresenter extends Presenter
{
    public function handleInfo()
    {
        $this->flashMessage('This is an info message', 'info');
        $this->disallowAjax()->redirect('this');
    }

    public function handleWarning()
    {
        $this->flashMessage('This is a warning message', 'warning');
        $this->disallowAjax()->redirect('this');
    }

    public function handleError()
    {
        $this->flashMessage('This is an error message', 'error');
        $this->disallowAjax()->redirect('this');
    }
}