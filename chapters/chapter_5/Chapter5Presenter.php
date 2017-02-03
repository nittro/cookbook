<?php


namespace App\Presenters;

use Nette\Application\UI\Form;
use Nittro\Bridges\NittroUI\Presenter;


class Chapter5Presenter extends Presenter
{

    public function doRegister(Form $form, array $values)
    {
        if (!preg_match('/\.edu$/', $values['email'])) {
            $form['email']->addError('Sorry, only .EDU e-mail addresses allowed at this time');
            $this->redrawControl('content');
            return;
        }

        if (mt_rand(0, 10) < 8) {
            $form->addError('Sorry, it\'s just not your lucky day');
            $this->redrawControl('content');
            return;
        }

        $this->flashMessage('Hello, ' . $values['email'] . '! Thanks for registering!');
        $this->postGet('this');
        $this->redrawControl('content');
    }

    public function createComponentRegistrationForm() {
        $form = new Form();

        $form->addEmail('email', 'E-mail:')
            ->setRequired();

        $form->addPassword('password', 'Password:')
            ->setRequired()
            ->addRule(Form::MIN_LENGTH, 'Please specify at least 6 characters', 6);

        $form->addPassword('password2', 'Password again:')
            ->setRequired()
            ->addRule(Form::EQUAL, 'Passwords do not match', $form['password']);

        $form->addSubmit('register', 'Register');

        $form->onSuccess[] = [$this, 'doRegister'];

        return $form;
    }
}