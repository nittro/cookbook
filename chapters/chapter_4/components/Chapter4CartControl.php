<?php


namespace App\Components;

use Nette\Application\UI\Control;


class Chapter4CartControl extends Control {

    private $total = 0;

    public function add($amount) {
        $this->total += $amount;
        $this->redrawControl();
        $this->flashMessage('Added ' . $amount . ',-');
    }

    public function render() {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/cart.latte');
        $template->total = $this->total;
        $template->render();
    }

}