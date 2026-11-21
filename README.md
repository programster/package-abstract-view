Abstract View
========

[![Tests](https://github.com/programster/package-abstract-view/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/programster/package-abstract-view/actions/workflows/tests.yml)

## Contents
- [About](#about)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [When a view throws](#when-a-view-throws)
- [Design principles](#design-principles)
- [Documentation](docs/README.md)
- [Upgrading](docs/upgrading.md)
- [Testing](docs/testing.md)
- [License](#license)

## About
This is a simple object to make it easy to build object-oriented views. The views implement
the [Stringable interface](https://www.php.net/manual/en/class.stringable.php), so they can
easily be passed around and used just as strings.

## Requirements
PHP 8.2, 8.3, 8.4, 8.5 or 8.6.

## Installation
```bash
composer require programster/abstract-view
```

## Quick start
Views are treated as objects, and you can use objects within objects. For example,
you could have a view for your HTML shell like so:

```php
<?php
use Programster\AbstractView\AbstractView;

class ViewHtmlShell extends AbstractView
{
    private readonly AbstractView $m_body;
    private readonly string $m_title;


    public function __construct(AbstractView $body, string $title = "My Site")
    {
        $this->m_body = $body;
        $this->m_title = $title;
    }


    protected function renderContent() : void
    {
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($this->m_title); ?></title>
  </head>
  <body>
    <?= $this->m_body; ?>
  </body>
</html>
<?php
    }
}
```

... and then a view for a "partial" that represents the body of the page like so:

```php
<?php
use Programster\AbstractView\AbstractView;

class ViewWelcomeMessage extends AbstractView
{
    protected function renderContent() : void
    {
?>
<h1>Hello World!</h1>
<p>Welcome to my site!</p>
<?php
    }
}
```

Then you can "build" the page by stitching the views together like so:

```php
<?php
class HomeController
{
    public function showHomePage() : void
    {
        $homePageBody = new ViewWelcomeMessage();
        $page = new ViewHtmlShell($homePageBody, "Welcome!");
        print $page; // print or add the page to a response object.
    }
}

(new HomeController())->showHomePage();
```

You could take this further by passing data to the body view, which may consist
of other "partial" views.

You pass data to the views through the use of the constructor. Thus, the views
know exactly what data they have to work with, and the controller knows exactly what data a view
needs to be provided.

## When a view throws
If a view throws while rendering, whatever it had output so far is discarded and the exception is
passed on, so you can safely catch it and render an error page without half of the failed page
appearing in it.

## Design principles
- A view gets everything it needs through its constructor.
- Rendering returns a string, and never prints, so the caller decides what happens to the output.
- A view that fails leaves nothing behind: no partial output, and no open output buffers.

## License
[MIT](LICENSE)
