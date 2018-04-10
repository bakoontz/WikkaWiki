# WikkaWiki Reveal Handler plugin

[![RevealJS](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/Revealjs.jpg)](https://github.com/hakimel/reveal.js/)
[![WikkaWiki](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/wizard.gif)](http://wikkawiki.org/HomePage)

## What is this?

This is a a framework for easily creating beautiful presentations using HTML and markdown on Wikkawiki.

![Fisr slide](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/reveal_fist.png)

Easy steps:

1. Put this repo on "/plugins/handlers/reveal" directory.
2. Edit the Wakka.class.php for allow the reveal handler.

## Why?

Is a very easy way to create and follow presentations with reveal.js.

## How?

This plugin works like any handler on WikkaWiki, supose that slide.md contains a presentation on Markdown format:

1. With a markdown Handler (without markdown handler this view must be ugly, if you dont have yet take a look on [Wikka-md-handler](https://github.com/oemunoz/Wikka-md-handler)).

For example:

```
 wikka.php?wakka=slide.md
```

[![Markdown parser](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/mmymdoc_parser.png)](https://github.com/oemunoz/Wikka-md-handler)

1. With a document that finish on ".md".

For example:

```
 wikka.php?wakka=slide.md/edit
```

![Edit Markdown](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/reveal_markdown.png)

### How install it?

For this handler is good to get [Wikka-md-handler](https://github.com/oemunoz/Wikka-md-handler), but is not a requeriment.

#### Install the handler:

The first is like a simple Handler, this meaning that you have to add "/reveal" to the end of the url. In any case you have to install the handler for both opions:

Drop this repo on your "/plugins/handlers/reveal" directory.

Directory Estructure:

```bash
cd plugins/handlers/
mkdir reveal
git clone https://github.com/oemunoz/Wikka-reveal-handler.git reveal/
```

![Directory estructure](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/paths.png)

Edit the follow code near to the end of "libs/Wakka.class.php", backup your original file and the new must be like:

```php
<?php ....
elseif($this->GetHandler() == 'reveal')
{
  print($this->Handler($this->GetHandler()));
}
.... ?>
```

Now, If you completed this, create a new document like this:

~~~~
wikka.php?wakka=slides.md/edit
~~~~

~~~~language-markdown
# Reveal.js
### HTML Presentations Made Easy
![Example Pic](https://github.com/iush/iush.github.io/raw/master/images/bio-photo.jpg)

Created by [Hakim El Hattab][hakim]
----
# First
====
# Column 1, Slide 1
----
# Column 1, Slide 2
----
# Column 1, Slide 3
====
# Middle
====
# Column 2, Slide 1
----
# Column 2, Slide 2
----
# Column 2, Slide 3

----

# Last
# THE END
### BY Hakim El Hattab / hakim.se

[hakim]: http://hakim.se
~~~~

And try to acces with:
~~~~
wikka.php?wakka=slide.md/reveal
~~~~

![Fisr slide](https://github.com/oemunoz/Wikka-reveal-handler/raw/master/images/reveal_fist.png)

## FAQs and TODOs

- We can change the background?

> R: For now, is not, but on the follow release we gonna to allow to upload files with this in mind.

- [ ] TODO: Create uploads directory.
- [ ] TODO: Auto path images on the upload directory.

# Powered by:
- [WikkaWiki](http://wikkawiki.org/HomePage) is a flexible, standards-compliant and lightweight wiki engine written in PHP, which uses MySQL to store pages.
- [RevealJS](https://github.com/hakimel/reveal.js/) is a framework for easily creating beautiful presentations using HTML.
