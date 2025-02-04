Abstract View
========

This is a simple object to make it easy to build object-oriented views. The views implement
the [Stringable interface](https://www.php.net/manual/en/class.stringable.php), so they can 
easily be passed around and used just as strings.

## Example Usage

Include the package by running:

```bash
composer require programster/abstract-view
```

Views are treated as objects, and you can use objects within objects. For example,
you could have a view for your [HTML shell](https://www.toptal.com/developers/htmlshell) 
like so:

```php
<?php
use Programster\AbstractView\AbstractView;

class ViewHtmlShell extends AbstractView
{
    public function __construct(
        private readonly string $body,
        private readonly string $title = "My Site"
    ) 
    {
    }

    protected function renderContent()
    {
?>



<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $this->title; ?></title>
    <meta name="description" content="description"/>
    <meta name="author" content="author" />
    <meta name="keywords" content="keywords" />
    <link rel="stylesheet" href="./stylesheet.css" type="text/css" />
    <style type="text/css">.body { width: auto; }</style>
  </head>
  <body>
    <?= $this->body; ?>
  </body>
</html>


<?php
    }
}
?>
```

... and then a view for a "partial" that represents the body of the page like so:

```php
<?php
use Programster\AbstractView\AbstractView;

class ViewWelcomeMessage extends AbstractView
{
    protected function renderContent()
    {
?>


<h1>Hello World!</h1>
<p>Welcome to my site!</p>


<?php
    }
}
?>
```

Then you cat "build" the page by stitching the views together like so:

```php
<?php
class HomeController
{
    public function showHomePage()
    {
        $homePageBody = new ViewWelcomeMessage();
        $page = new ViewHtmlShell($homePageBody, "Welcome!");
        print $page; // print or add the page to a response object.
    }
}
```
You could take this further by passing data to the body view, which may consist
of other "partial" views.

You pass data to the views through the use of the constructor. Thus, the views 
know exactly what data they have to work with, and the controller knows exactly what data a view 
needs to be provided.

