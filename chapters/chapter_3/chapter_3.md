### AJAX

In this chapter we'll tackle the most common scenarios you'll encounter when dealing
 with an AJAX-enabled application. We'll talk about redirects, forms and browser history.
 This is one of the few chapters which deals mostly with server-side code - even though
 Nittro is a client-side framework and Nette has great AJAX support built in, there
 is a couple of points to be made about how your server side code should behave
 to achieve true greatness in an AJAX-enabled world.

#### What you need

As usual this chapter builds on code from the previous chapters, so it is assumed
 that you have a working Nette application with Nittro linked in your layout template
 and that you have a base presenter class which either extends `Nittro\Bridges\NittroUI\Presenter`
 or uses the `Nittro\Bridges\NittroUI\PresenterUtils` trait.

#### What you'll get

... tbd

#### Part 1: (...)

Let's try to build a simple e-mail subscription form into our site. Who knows? Maybe
 we'll discover something along the way.

The usual way of building a simple e-mail subscription form with Nette would probably
 involve a factory method for the form which might look something like this:

>> class App\Presenters\Chapter3Presenter, method createComponentSubscriptionForm

Also, unless you wanted your application to break horribly whenever someone submits
 the form, there would be an implementation of the success handler:

>> class App\Presenters\Chapter3Presenter, method doSubscribe

And finally, because you're nice, you'd want to let the user know that they did
 subscribe successfully when they submit the form:

>> class App\Presenters\Chapter3Presenter, method renderDefault

The template for the default action might look like this:

>> template chapter_3/templates/Chapter3.default.latte

##### Have you noticed?

We're doing nothing special here - this is exactly how you'd implement a subscription
 form in a regular Nette application with no AJAX involved. And even like this
 it will work out of the box with Nittro - the form is submitted using AJAX and even
 the redirect works as expected (which here means that it gets loaded using AJAX and
 that the user ends up at the correct address). There's a couple of things that we can
 improve though. Let's take a look at them:

1. When you submit the subscription form, the whole page gets updated (more precisely
   the whole "content" snippet). Notice the time in the main part of the page.
2. When you click Back in the browser, you're taken back to the same page you're already
   at - which is weird, since it didn't look like you left that page in the first place.
   Also the subscription form is now empty and gives no indication of ever having been
   submitted, which suggests that by clicking Back in the browser you somehow "undid"
   you subscription.

#### Part 2: Do Better!

The first problem can be easily fixed: let's just wrap the subscription form in a
 snippet and redraw that snippet in the success handler!

But wait, what about the redirect? If we remove it and somebody submits the form without
 AJAX, they'll be able to submit the form over and over again by reloading the page.

So what, you may think, let's redirect the user if the request wasn't submitted using
 AJAX and redraw the subscription form otherwise! And you would be quite right. In fact,
 this is a scenario that happens so often in AJAX-enabled applications that Nittro has
 a special behaviour for it. Instead of always having to type up something like
 `if ($this->isAjax()) { ...` you can simply use the `$this->postGet()` method. It's
 part of the `PresenterUtils` trait, so we already have it handy. It has the same
 parameters as the presenter's `redirect()` method, but it's behaviour is a little different:

1. If the request _wasn't_ sent using AJAX, do a normal redirect using `$this->redirect()`
   passing in the provided parameters.
2. If the request _was_ sent using AJAX, add some metadata to the payload _and return_.

This method is useful for two reasons: if you're handling a non-AJAX requests, it effectively
 stops any further code from executing, just like a regular call to `$this->redirect()` would;
 and if you're handling an AJAX request, `$this->postGet()` will add the URL to which you
 _would have_ redirected in a non-AJAX request to the payload and Nittro will then push _that_
 URL to the browser history stack instead of the one that actually handled the request.

So back to our current example: let's replace the `redirect()` in the success handler with a
 `postGet()` and redraw the subscription form snippet if `postGet()` doesn't redirect:

>> class App\Presenters\Chapter3BetterPresenter, method doSubscribe

Also, you'll probably need to update the `renderDefault()` method a little bit:

>> class App\Presenters\Chapter3BetterPresenter, method renderDefault

Okay, now only the subscription form's snippet gets updated when the form is submitted
 and the user ends up at the correct address even though no actual redirecting happened.
 That's nice, but if the user presses the back button in their browser, they still
 get sent to the original homepage with the empty form. What can we do about that?

That one is actually even simpler. So much so that it's not even a one-liner. Just
 add the `data-history="false"` attribute to the form and Nittro won't push it onto
 the history stack when submitted. The whole updated template looks like this:

>> template chapter_3/templates/Chapter3Better.default.latte
