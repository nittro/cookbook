### Basic Nittro installation

This chapter will take you through a basic installation of Nittro 
 from a ZIP archive which you can download from the Nittro website.
 As with all the chapters in the Basic section of the Cookbook
 we're not going to touch any actual JavaScript code here. You're
 not going to need Bower, Gulp or anything else in order to get
 through this chapter.

#### What you need

This chapter assumes that you have a simple website with one main
 block named `content` in your layout template. Furthermore, for
 convenience it is suggested that you use an abstract base presenter
 class that all your presenters extend. The [Nette Web Project](https://github.com/nette/web-project)
 is a good starting point if you don't have any suitable project
 in the works at the moment.

#### What you'll get

At the end of this chapter your site should be entirely AJAX-driven -
 all links and forms should load using AJAX automatically and the browser
 history should reflect user navigation.

#### Getting Nittro

Head over to [nittro.org/download](https://www.nittro.org/download)
 and download the Nittro Basic package. Extract the downloaded ZIP
 archive wherever you fancy and place the files `nittro.min.js` and
 `nittro.min.css` somewhere in the document root of your site, e.g.
 `web/js/nittro.min.js` and `web/css/nittro.min.css`, respectively.

#### Link Nittro in your layout template

Place the following two lines of code inside the `<head>` section 
 of your `@layout.latte` template:

>> file chapter_1/templates/@layout.latte from nittro-full.css... to ...nittro-full.js

#### Update your server-side code to enable AJAX

Still in your `@layout.latte` template you'll need to wrap your
 main content block with a snippet, for example like this:

>> template chapter_1/templates/@layout.latte, snippet content

Now open your base presenter class and add the `afterRender()` method
 if it's not already present. Add the following code to the method's body:

>> class App\Presenters\Chapter1Presenter, method afterRender

#### That's it!

Check your website in your favourite browser. If all goes well,
 every time you click a link or submit a form the request should
 be handled by AJAX, the main content block should receive the
 appropriate content and the browser history should be updated,
 meaning that the current address in the address bar should
 update accordingly and the Back / Forward buttons should
 do exactly what you'd guess they would.

#### Next: [[Using the Nette bridge|Cookbook:chapter 2]]