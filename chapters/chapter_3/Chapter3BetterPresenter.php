<?php

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nittro\Bridges\NittroUI\Presenter;


class Chapter3BetterPresenter extends Presenter
{

    public function renderDefault($subscribed = false)
    {
        if (!isset($this->template->subscribed)) {
            $this->template->subscribed = (bool) $subscribed;
        }
    }

    public function doSubscribe(Form $form, array $values)
    {
        // Something like $this->model->subscribeNewsletter($values['email']);

        // Redirect if non-AJAX...
        $this->postGet('this', ['subscribed' => true]);
        // ... otherwise continue rendering.
        $this->redrawControl('subscriptionForm');
        $this->template->subscribed = true;
    }

    public function createComponentSubscriptionForm()
    {
        $form = new Form();

        $form->addEmail('email', 'Your e-mail:')
            ->setRequired();

        $form->addSubmit('subscribe', 'Subscribe');

        $form->onSuccess[] = [$this, 'doSubscribe'];

        return $form;
    }
}
