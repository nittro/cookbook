<?php

namespace App\Presenters;

use Nittro\Bridges\NittroUI\Presenter;


class Chapter3TransitionsPresenter extends Presenter
{

    public function handleFade()
    {
        $this->redrawControl('fade');
        $this->template->fade = true;
    }

    public function handleDim()
    {
        $this->redrawControl('dim');
        $this->template->dim = true;
    }

    public function handleSlide()
    {
        $this->redrawControl('slide');
        $this->template->slide = true;
    }

    public function handleBar()
    {
        $this->redrawControl('bar');
        $this->template->bar = true;
    }

    public function renderDefault()
    {
    }
}
