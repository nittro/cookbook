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

At the conclusion of this chapter you should have an understanding of Nittro's AJAX 
 behaviour in your application - and more importantly _why_ and _how_ you can change
 it in certain scenarios. We'll also talk about animations that (can) happen when
 Nittro updates the page using AJAX.

#### Part 1: Building the basics

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

#### To sum it up

As you can see, there's not much that needs to be done on the server side when you're
 integrating your application with Nittro. Mostly it depends on how much you want
 to actually _enhance_ your application compared to how it already works without AJAX,
 but even if you go for more enhancement, it usually involves very little work apart
 from wrapping things with snippets, redrawing them appropriately and occasionally
 swapping `$presenter->redirect()` for `$presenter->postGet()` or disabling a link
 or form from pushing a new state to the browser's history with `data-history="false"`.

#### Further notes

Sometimes the default behaviour to load all local URLs using AJAX isn't appropriate.
 Typical examples are links to downloadable content, or in some cases login forms
 and other scenarios where you want to reload the whole page. For forms and links
 which you want to exclude from AJAX handling the solution is to add the
 `data-ajax="false"` attribute. When you want to force a redirect on the server
 side to result in a full-page reload you can just add `$presenter->payload->allowAjax = false;`
 before the redirect, or more succintly `$presenter->disallowAjax()`, courtesy
 of the `PresenterUtils` trait. The `disallowAjax()` method returns the presenter,
 so method chaining is possible.

#### Part 3: Animations

One thing you might have noticed already is that when stuff gets loaded using AJAX,
 the visual feedback of the site updating might be a little underwhelming. That's why
 most websites which employ AJAX use some sort of animation or show a spinner to
 indicate that content is being updated. This is also very easy to do with Nittro.
 Whenever a request to update the page using AJAX is being dispatched Nittro looks
 for elements to animate. By default Nittro selects all elements in the page that 
 have the `nittro-transition-auto` class but you can override this for any link
 or form using the `data-transition` attribute. The value of the attribute should
 be what in the Nittro world is called a Simple Selector - it's almost like a regular
 CSS selector, except it's a little limited in that you can only use an `#id .class`
 combination or just a single `.class`. You can use multiple Simple Selectors separated
 by commas just like you would for example in a jQuery selector though so this limitation
 shouldn't really be to hard to comply with.

 Nittro applies a sequence of classes to any elements found for a given page transaction
 that allow you to execute complex animation schemes. We're going to go to greater detail
 about the mechanics of this later in the advanced section; for now we're going to present
 a couple of transitions that are built into Nittro. There are currently four of those:
 Fade, Dim, Slide and Bar. What they look like is best described by pointing you to the
 [[live example|Chapter3Transitions:]]; the way you use them is by attaching the appropriate
 `nittro-transition-*` class to the animated element or elements. Don't forget to add
 the `nittro-transition-auto` class to any elements that you want to animate by default.
