<?php
/**
* Colors a given text.
*
* You can specifiy either one of htmls defined names or hex-values
* (with the former one takinge precedency). Same for the background-color.
*
* @package    Actions
* @name    Color
*
* @author    ?, probably Hendrik Mans
* @author    {@link http://wikkawiki.org/NilsLindenberg NilsLindenberg} (modifications)
* @author    {@link http://wikkawiki.org/DotMG DotMG} (modification - RGB syntax)
*
* @version    1.3
* @since    ?
*
* @input    string $text mandatory: the text which should be colored.    
* @input    string $c optional: (html)name or hex-value of the color for the text;
* @input    string $hex optional: alias for $c
*           kept for backwards-compatibility;
* @input    string fg optional: alias for $c
*           introduced to fit with $bg
* @input    string $bg optional: (html)name or hex-value for the backgroundcolor;
* @output   colored text
*
* @constraint    at least one valid parameter for $c (or $hex or $fg) or $bg is required
*
* @documentation  {@link http://wikkawiki.org/PatternValidColorInfo}
* @documentation  {@link http://wikkawiki.org/ColorActionInfoFR}
*
* @todo     make it part of the formatter instead of using an action
*/

// *** Constant section ***
define('ERROR_NO_TEXT_GIVEN','There is no text to highlight!');
define('ERROR_NO_COLOR_SPECIFIED', 'Sorry, but you did not specify a color for highlighting!');

// ***Internal function to test if syntax is valid
if (!function_exists('color_syntax_is_valid'))
{
	function color_syntax_is_valid($syntax)
	{
		//Todo: To be more strict, ensure that when using rgb(r, g, b) syntax, integer values for r, g, and b are less than 256, or if % is used, those values are not greater than 100%
		define('PATTERN_VALID_COLOR', '/^(?>#(?>[\da-f]{3}){1,2}|rgb\(\s*\d+((?>\.\d*)?%)?\s*(?>,\s*\d+(?(1)(\.\d*)?%)\s*){2}\))$/i');
		$htlm_color_names = array('aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue',
		'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflower', 'cornsilk', 'crimson', 'cyan',
		'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid',
		'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue',
		'dimgray', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray',
		'green', 'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen',
		'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon',
		'lightseagreen', 'lightskyblue', 'lightslategray', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'maroon',
		'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen',
		'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive',
		'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff',
		'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen',
		'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato',
		'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen', 'activeborder', 'activecaption', 'appworkspace',
		'background', 'buttonface', 'buttonhighlight', 'buttonshadow', 'buttontext', 'captiontext', 'graytext', 'highlight', 'highlighttext',
		'inactiveborder', 'inactivecaption', 'inactivecaptiontext', 'infobackground', 'infotext', 'menu', 'menutext', 'scrollbar',
		'threeddarkshadow', 'threedface', 'threedhighlight', 'threedlightshadow', 'threedshadow', 'window', 'windowframe', 'windowtext');
			$syntax = trim(strtolower($syntax));
		if (in_array($syntax, $htlm_color_names))
		{
			return($syntax);
		}
		if (preg_match(PATTERN_VALID_COLOR, $syntax, $match))
		{
			return($match[0]);
		}
		return(false);
	}
}

// initialization
$mytext = '';
$style = '';
$output = '';

// *** User input section ***
if (is_array($vars)) 
{
	foreach ($vars as $param => $value) 
	{
		if ($param == 'text')
		{
			$mytext = $this->htmlspecialchars_ent($value);
		}
		elseif (($param == 'c') || ($param == 'hex') || ($param == 'fg'))
		{
			$fgcolor = color_syntax_is_valid($value);
			if ($fgcolor)
			{
				$style .= "color: $fgcolor; ";
			}
		}
		elseif ($param == 'bg')
		{
			$bgcolor = color_syntax_is_valid($value);
			if ($bgcolor)
			{
				$style .= "background-color: $bgcolor; ";
			}
		}
	}
	if (($mytext) && ($style))
	{
		$output .= '<span style="'.$style.'">'.$mytext.'</span>';
	}
	elseif (!$mytext)
	{
		$output .= '<em class="error">'.ERROR_NO_TEXT_GIVEN.'</em>';
	}
	elseif (!$style)
	{
		$output .= '<em class="error">'.ERROR_NO_COLOR_SPECIFIED.'</em>';
	}
	// *** Output section ***
	print ($output);
}

?>

