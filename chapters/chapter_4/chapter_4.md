### Flash Messages

Flash Messages are a very simple yet powerful feature of Nette and Nittro
naturally provides mechanisms to support them in your AJAX-driven website.
While you _can_ just wrap your flash messages in snippets, you would then
need to manually redraw these snippets on every request and since every
component in Nette has its own flash session this could get old really fast.

#### What you need



#### What you'll get



#### Basic integration

Do you remember this code from the Sandbox layout template?

```latte
<div n:foreach="$flashes as $flash" n:class="alert, 'alert-' . $flash->type">{$flash->message}</div>
```

As it is, this will take care of your flash messages when rendering responses
 to non-AJAX requests. And while you could just wrap this in a snippet and
 be done with it, you'd then have to remember to redraw the snippet every time
 you add a flash message. And more importantly, Nette already bundles all
 flash messages in the payload automatically when responding to AJAX requests,
 so you'd in fact be sending the same data twice in the response. That just
 won't do.

Nittro has a service built in that can take care of your flash messages.
 As it happens, you may not even need to do anything to make use of it - it
 works automatically out of the box, as you can see in the first [[live example|Chapter4:]]
 for this chapter.

The catch is that this makes the user experience different when flash messages
 are included in an AJAX response and displayed by Nittro rather than rendered inline
 within the template. This isn't necessarily problematic due to the few users who
 have JavaScript turned off entirely (indeed those users would actually end
 up with a consistent UX because flashes would _always_ get rendered in the
 template for them) but rather due to the fact that sometimes you _need_ to do a
 full-page reload _and_ display a flash message (e.g. after logging a user in).

You can achieve this easily enough by making use of the `n:flashes` Latte
 macro that comes bundled with the Nette Bridges package. First of all, you
 need to register the Nittro macros in your `config.neon`:

```neon
latte:
    macros:
        - Nittro\Bridges\NittroLatte\NittroMacros
```

There are other macros provided by the package, but we'll get to those in
 some of the later chapters. Now that you have the `n:flashes` macro available,
 let's modify your template:

```latte
<ul n:flashes></ul>
```

This will take care of rendering your flashes much the same way the original
 code did, but also add a couple of attributes to the container `<ul>` element.
 Namely, the element will get an `id` that corresponds to the flash session of
 the component rendering it (e.g. `flashes` for the presenter) and the
 `data-flash-inline="true"` data attribute, which tells Nittro to insert
 flash messages as inline elements within the container (why that's important
 will become apparent shortly). Also the messages themselves will receive the classes
 `nittro-flash`, `nittro-flash-inline` and `nittro-flash-<type>`. The element
 used for the messages will be a `<li>` if the container is either `<ul>` or `<ol>`,
 otherwise `<p>` is used.

When Nittro cannot find an appropriate target element for a given flash message
 the message will be displayed as a "global" message. This is actually the behaviour
 that you have seen in the first part of this chapter - the narrow strip floating
 at the top edge of the screen was a global flash message.

Also note that Nittro automatically dismisses flash messages after a timeout.
 Nittro is trying to be smart about it: a flash message will only be dismissed
 after the user has interacted with the page since the message was displayed and
 the timeout after that first interaction is computed from the message length
 (minimum is 5 seconds). Messages rendered on the server will be dismissed as well
 (provided that you use the `n:flashes` macro or manually add the `nittro-flash`
 class to the message elements).

#### Floating flash messages

Now, remember that `data-flash-inline` attribute mentioned earlier? That's because
 the _default_ behaviour of flash messages is to be displayed as floating bubbles
 with a small arrow _next to_ the target element, rather than as its descendants.
 You can leverage that behaviour using another Latte macro: `n:flashTarget`. This
 macro will add the same `id` to the element it's applied to as `n:flashes` would,
 but it won't actually _display_ flash messages directly in the template. Additionally,
 you can provide a single value as the macro's argument to specify your preferred
 placement of the message. The values that Nittro recognises out of the box are
 `above`, `below`, `leftOf` and `rightOf`. If you don't specify any value, Nittro
 will use the first option that fits inside the available space between the target
 element and the viewport's edges, in clockwise order starting from `top`. If Nittro
 cannot fit the message in any of the possible locations (for example because the
 target element is scrolled completely outside of the viewport) the message will
 be displayed globally instead.

Apart from specifying a _preferred_ placement for the message (which only means that
 the specified location will be tried first, but if the message wouldn't fit, the
 other options will still be tried) you can also append an exclamation mark after the
 placement keyword to restrict the message placement either to the specified location
 or global if the message wouldn't fit. By specifying _two_ exclamation marks you can
 force Nittro to _always_ use the specified location (but beware that your messages
 may then go unnoticed by the user due to being displayed outside of the viewport).