### Using the Nette bridge

Most of the AJAX support that Nittro needs on the server side
 is already built into Nette, but there are still a few common
 tasks that you need to do in order to enable it. In this chapter
 we'll look at the `nittro/nette-bridges` package and how it
 helps you take care of these tasks.

One common task you need to do in every request is decide
 which snippets to invalidate. Most of the time this will be
 a fixed number of snippets which wrap things like the
 site's content, a navigation menu and so on, but at times
 you may want to invalidate a different set of snippets,
 for example a newsletter subscription box after the user
 has used it to subscribe to your site's newsletter.

The Nette bridge provides a trait that simplifies some of these
 common tasks. You can use this trait in your own base presenter
 class, or you can simply extend the provided base presenter
 class instead of `Nette\Application\UI\Presenter`.


#### What you need

This chapter assumes that you have a basic working installation
 of Nittro in your project as described in [[Chapter 1|Cookbook:chapter 1]],
 as well as Composer available within your `PATH`.

#### What you'll get

At the end of this chapter you should have a much finer
 control over which snippets get invalidated, as well as
 access to a couple of useful presenter methods 
 which should make your everyday life in the Nittro
 world a bit easier.

#### Install the Nette bridge using Composer

Fire up a terminal window and navigate to your project directory.
 Launch the following command:

```bash
composer install nittro/nette-bridges
```

#### Register the Nittro Latte macros

### TODO move to a later chapter

Add this to the `latte` section of your `config.neon`:

```neon
latte:
    macros:
        - Nittro\Bridges\NittroLatte\NittroMacros
```

This will register the Nittro Latte macros `{snippetId}`, `{param}` 
 and `n:dynamic`. These macros will be useful when working with
 some of the Nittro components we'll be discussing in the Basic
 section of the Cookbook and later when we introduce dynamic snippet
 support.

#### Create or update your base presenter

If you don't have a base presenter class in your project
 yet it's time to create one. Just create an abstract class
 called BasePresenter in the App\Presenters namespace and
 make all your other presenters extend this class instead
 of `Nette\Application\UI\Presenter`. Make the BasePresenter
 class extend the `Nittro\Bridges\NittroUI\Presenter` class.

If you already have a base presenter class which directly extends
 the `Nette\Application\UI\Presenter` class, as is the case
 with e.g. the Nette Sandbox base presenter, you can simply
 make your base presenter class extend the
 `Nittro\Bridges\NittroUI\Presenter` class instead.
 If your base presenter needs to extend some other class
 you can use the `Nittro\Bridges\NittroUI\PresenterUtils`
 trait in your base presenter, but to enable the built-in
 snippet invalidation utilities you'll need to add a couple
 of lines to the `startup()` and `afterRender()` methods.
 We'll get to that in a bit, for now let's assume you're
 just extending the provided base presenter class.

If you followed the previous chapter, your base presenter
 should now have an `afterRender()` method which calls
 something like `$this->redrawControl('content')`. The
 Nittro base presenter already does something like that
 for you, so replace the `redrawControl()` call with
 `parent::afterRender()` if it isn't already present in
 your `afterRender()` method. In any case remove the
 `redrawControl()` call.

If you are using the Nittro base presenter, your website
 should now work exactly the same way it did at the end
 of the previous chapter. If you are using just the `PresenterUtils`
 trait, you'll need to do two more things: First, call
 `$this->setRedrawDefault($this->getSignal() === null)`
 in the `startup()` method of your base presenter. Furthermore,
 back in the `afterRender()` method of your base presenter class
 add a call to `$this->redrawDefault()`. This will achieve
 the same behaviour as that of the Nittro base presenter.
 Check again in your browser to verify that everything
 works the same as it did at the end of the previous chapter.

What both of these implementations do is they keep a list
 of snippet names which are invalidated in the `afterRender()`
 method on every request, unless you have previously invalidated
 something by hand. The other exception is when your application
 receives a signal, be it a signal to the presenter itself or to
 a component - when the application is handling a signal it is
 your responsibility to decide which snippets (if any) to redraw.
 This second exception is actually the result of the `setRedrawDefault()`
 call you added to the `startup()` method in the previous
 paragraph.

#### Bonus round

Let's make your site do a tiny little more now that you have
 the tools: wrap the menu in your layout template in a snippet
 (name it e.g. `menu`) and add some logic to highlight the
 current item. Add a `startup()` method to your base presenter
 if it isn't there yet and within it call `$this->setDefaultSnippets(['content', 'menu'])`
 (replacing the snippet names with the ones you use in your
 application of course). Now the active item in the menu 
 should be updated alongside the page's content on AJAX requests.

#### That's it!

If you've got everything up and running, you may now proceed to

#### Next: [[AJAX|Cookbook:chapter 3]]