<?php


namespace App\Presenters;

use Nittro\Bridges\NittroUI\Presenter;


class Chapter6ConfirmPresenter extends Presenter
{
    public function handleDelete()
    {
        $this->flashMessage('Article deleted.', 'warning');
        $this->postGet('this');
        $this->redrawControl('article');
        $this->template->deleted = true;
    }
}