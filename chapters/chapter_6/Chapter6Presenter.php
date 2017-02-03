<?php


namespace App\Presenters;

use Nette\Application\UI\Form;
use Nittro\Bridges\NittroUI\Presenter;


class Chapter6Presenter extends Presenter {


    public function doAddToCart(Form $form, array $values) {
        // add item to cart etc.

        $this->postGet('cart');
        $this->setView('cart');
        $this->redrawControl('content');
        $this->template->quantity = $values['quantity'];
    }

    public function createComponentOrderForm() {
        $form = new Form();

        $form->addText('quantity', 'Quantity:')
            ->setType('number')
            ->setDefaultValue(1)
            ->addRule(Form::INTEGER, 'Please enter a number')
            ->addRule(Form::MIN, 'Please enter a number greater than zero', 1)
            ->setRequired();

        $form->addSubmit('add', 'Add to Cart');

        $form->onSuccess[] = [$this, 'doAddToCart'];

        return $form;
    }
}