# WikkaWiki Markdown Handler plugin

[![Markdown](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/Markdown.png)](http://markdown.cebe.cc)
[![WikkaWiki](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/wizard.gif)](http://wikkawiki.org/HomePage)
[![Prism](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/prism-syntaxhighlighter.png)](http://prismjs.com/)

## What is this?

Yes, this is a markdown parser for [WikkaWiki](http://wikkawiki.org/HomePage), the magic is from the [Cebe Markdown parser](http://markdown.cebe.cc/).

![Parse Markdown](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/mmymdoc_parse.png)

Easy steeps:

1. Put this repo on "/plugins/handlers/md" directory.

2. Add the ".md" support to your Wakka.class.php file, this like on the example located on this repo "examples/Wakka.class.php.example".

The detalled installed is explain on the next points:

## Why?

By defect WikkaWiki has their own formatting rules (these rules work like a charm), but on this days is very common to write all cain of documentation on Markdown, Markdown is the prefered formatting code for all cain of projects. I dont have a problem to know both ways of formatting, but is a lot of work traduce from one to the other, this plugin works on this point.

## How?

This plugin works in two ways (both if you wish), supose that mydoc and mydoc.md contains a document on Markdown format:

* With a Handler md.

For example:

```
 wikka.php?wakka=mmymdoc/md
```

* With a document that finish on ".md".

For example:

```
 wikka.php?wakka=mmymdoc.md
```

### How install it?

This plugins has two behaviors:

#### Install the handler:

The first is like a simple Handler, this meaning that you have to add "/md" to the end of the url. In any case you have to install the handler for both opions:

Drop this repo on your "**/plugins/handlers/md**" directory.

Directory Estructure:

```bash
cd plugins/handlers/
mkdir md
git clone https://github.com/oemunoz/Wikka-md-handler.git md/
```

![Directory estructure](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/md_handler.png)

Now, If you completed this, create a new document like this:

```
wikka.php?wakka=mmymdoc/edit
```

~~~~markdown
# First level

First Header  | Second Header
------------- | -------------
Content Cell  | Content Cell
Content Cell  | Content Cell

## Second level.

Text before list:
 * item 1,
 * item 2,
 * item 3.

Text after list.

- test
- test
   - test
   - test
- test
~~~~

![Edit Markdown](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/mmymdoc_edit.png)

> Note that the current Geshi buttons are for the Wikka format, Im am working on use the [yii2-markdown](https://github.com/kartik-v/yii2-markdown), for the edit controls. But Markdown is not a dificult Format to learn.

And try to acces with:

```
wikka.php?wakka=mmymdoc/md
```

#### Configure for automatic access:

If you are like me, you want to access to the document direct without adding "/md", then you have to add the support for the ".md" extencion on your wikis documents.

Edit the follow code near to the end of "libs/Wakka.class.php", backup your original file and the new must be like:

```php
<?php
....
elseif( $this->GetHandler() == 'show' && pathinfo($this->GetPageTag(), PATHINFO_EXTENSION) == 'md' && $this->page['body'] != '' )
{
  // Hugly handler but util.
  $this->Handler($this->handler = 'md');
  echo $this->Header();
  echo $this->Handler($this->GetHandler());
  echo $this->Footer();
}
....
?>
```
On any case I leave a copy of my Wakka.class.php on the project/expamples/ directory.

From now you can create mmymdoc.md and the system gonna to use the Markdown parser automaticly. (you can access to the edit page with dobleclik like the normal way.)

```
 wikka.php?wakka=mmymdoc.md
```

![Parse Markdown](https://github.com/oemunoz/Wikka-md-handler/raw/master/images/mmymdoc_parse.png)

#### What about highlighting:

The system uses prism.js for the highlighting, when you are on GitHub flavour (is the default on this parser) uses "php" for the title of the code for example, but if you use MarkdownExtra the title is "languaje-php".

With prims.js you can build your own highlighting, is very easy take a look at [Prism](http://prismjs.com).

## FAQs and TODOs

- The previus Wiki markup documents are supoorted?

> R: Yes the cebe parse only process the ".md" documents or when you use the "/md" handler, the rest of the wikka works like usually.

- The handler have another flavors of markdown?

> R: Yes the cebe parse have anothers flavors (traditional, GitHub, extra). By default is GitHub flavor, but you can change this on pd.php file. Check for the cebe documentation, and the [markdown GitHub]( https://help.github.com/articles/github-flavored-markdown) for the especific formated.

- The plugins works on with this handler?

> R: For now, is not, plugins like "files" and "Category" etc, are not availables with the md plugins. (I need some time for check if it is posible and/or necesary).

- [x] TODO: Ask for add the css and js on the header.php by default. Now the css and js are on the handler directory we dont need to copy.
- [ ] TODO: Check for the common div and html objets css styles, I only check the table css style.
- [ ] TODO: Check for the TOC Generator.
- [ ] TODO: Review how to use the [yii2-markdown](https://github.com/kartik-v/yii2-markdown).
- [ ] TODO: Review for Atom plugin.
- [ ] TODO: Check if the checklist option is available. :-(
- [ ] TODO: Review the CamelCase link options.

# Powered by:
- [WikkaWiki](http://wikkawiki.org/HomePage) is a flexible, standards-compliant and lightweight wiki engine written in PHP, which uses MySQL to store pages.
- [Cebe Markdown parser](http://markdown.cebe.cc) a super fast, highly extensible markdown parser for PHP.
- [Prism](http://prismjs.com) is lightweight, extensible syntax hghlighter, built with modern web standart in mind.
