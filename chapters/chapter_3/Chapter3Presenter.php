<?php

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nittro\Bridges\NittroUI\Presenter;


class Chapter3Presenter extends Presenter
{

    public function renderDefault($subscribed = false)
    {
        $this->template->subscribed = (bool) $subscribed;
    }

    public function doSubscribe(Form $form, array $values)
    {
        // Something like $this->model->subscribeNewsletter($values['email']);

        // Redirect to prevent resubmitting the form by reloading the page
        $this->redirect('this', ['subscribed' => true]);
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
