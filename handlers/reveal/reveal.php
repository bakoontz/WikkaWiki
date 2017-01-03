<?php
$site_base = WIKKA_BASE_URL;
// Make robust the way to find the css and the js. The handler can be on many directorys.
$relative_path = explode( ',', $this->GetConfigValue('handler_path'));
$my_handler_path = "plugins/handlersx";
// Check for the css and the js files. If exist overwrite the black/withe web.
foreach ($relative_path as $key => $value) {
	if ( is_file($relative_path[$key].'/reveal/css/reveal.css') &&
	     is_file($relative_path[$key].'/reveal/css/theme/black.css') &&
	     is_file($relative_path[$key].'/reveal/lib/css/zenburn.css') &&
	     is_file($relative_path[$key].'/reveal/css/print/pdf.css') &&
	     is_file($relative_path[$key].'/reveal/css/print/paper.css') &&
	     is_file($relative_path[$key].'/reveal/lib/js/head.min.js') &&
	     is_file($relative_path[$key].'/reveal/js/reveal.js') &&
	     is_file($relative_path[$key].'/reveal/plugin/markdown/marked.js') &&
	     is_file($relative_path[$key].'/reveal/plugin/notes/notes.js') &&
	     is_file($relative_path[$key].'/reveal/plugin/highlight/highlight.js')
		 ) {
			 		$my_handler_path = $relative_path[$key];
			 }
	}
/*

}
       }}
*/
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<title>reveal.js</title>

		<link rel="stylesheet" href="<?php echo $my_handler_path ?>/reveal/css/reveal.css">
		<link rel="stylesheet" href="<?php echo $my_handler_path ?>/reveal/css/theme/black.css">

		<!-- Theme used for syntax highlighting of code -->
		<link rel="stylesheet" href="<?php echo $site_base.$my_handler_path ?>/reveal/lib/css/zenburn.css">

		<!-- Printing and PDF exports -->
		<script>
			var link = document.createElement( 'link' );
			link.rel = 'stylesheet';
			link.type = 'text/css';
			link.href = window.location.search.match( /print-pdf/gi ) ? '<?php echo $my_handler_path ?>/reveal/css/print/pdf.css' : '<?php echo $my_handler_path ?>/reveal/css/print/paper.css';
			document.getElementsByTagName( 'head' )[0].appendChild( link );
		</script>
	</head>
	<body>
		<div class="reveal">
			<div class="slides">
				<section data-markdown="<?php echo $site_base ?>wikka.php?wakka=<?php echo $this->GetPageTag() ?>/raw"
				 data-separator="^----"
				 data-separator-vertical="^===="
				 data-transition="slide-in fade-out"
				 data-autoslide="5000"
         data-separator-notes="^Note:"
         data-charset="iso-8859-15">
        </section>
			</div>
		</div>

		<script src="<?php echo $my_handler_path ?>/reveal/lib/js/head.min.js"></script>
		<script src="<?php echo $my_handler_path ?>/reveal/js/reveal.js"></script>

		<script>
			// More info https://github.com/hakimel/reveal.js#configuration
			Reveal.initialize({
				history: true,

				// More info https://github.com/hakimel/reveal.js#dependencies
				dependencies: [
					{ src: '<?php echo $my_handler_path ?>/reveal/plugin/markdown/marked.js' },
					{ src: '<?php echo $my_handler_path ?>/reveal/plugin/markdown/markdown.js' },
					{ src: '<?php echo $my_handler_path ?>/reveal/plugin/notes/notes.js', async: true },
					{ src: '<?php echo $my_handler_path ?>/reveal/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } }
				]
			});
		</script>
	</body>
</html>
