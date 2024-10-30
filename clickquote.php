<?php
/*
Plugin Name: ClickQuote
Plugin URI: http://www.startup20.com/ClickQuote/
Description: Finally, an easy and simple way for your readers to quote and respond to your posts and other comments!  Simply hover over a block of text and click it.  ClickQuote will wrap the paragraph in appropriate tags and insert it into your comment reply box below.  Quote as many paragraphs, from as many people, as you want, all in one comment.
Version: 1.0.0
Author: Steven Romej and Brian Culler
Author URI: http://www.startup20.com/ClickQuote/about
 */


// ------------------------------------------------------------------
//
// split_paragraphs is based on wpautop in wp-includes/formatting.php; it's a reliable 
// and proven function capable of wrapping paragraphs in <p> tags; here it serves to 
// wrap each paragraph in a quotable div.  The real wpautop() is executed after this.
//
// ------------------------------------------------------------------
function split_paragraphs($pee, $br = 1) {
	// just to make things a little easier, pad the end
	$pee = $pee . "\n"; 
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	// Space things out a little
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
	// cross-platform newlines
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee); 
	// take care of duplicates
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); 
	// make paragraphs, including one at the end
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<div title='Click to quote this paragraph in your reply below' class='clickquote'><p>$1</p></div>\n", $pee); 
	// under certain strange conditions it could create a P of entirely whitespace
	$pee = preg_replace("|<div class='clickquote'><p>\s*?</p></div>|", '', $pee);

	return $pee;
}


// ------------------------------------------------------------------
//
// Load the javascript necessary to run this plugin
//
// ------------------------------------------------------------------
function load_header_info( )
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('clickquote','/wp-content/plugins/clickquote/clickquote.js');

	
	echo "<style>";
	echo ".cqhover { background: " . get_option('clickquote_hovercolor') . " }";
	echo "</style>";
}




// ------------------------------------------------------------------
//
// Break paragraphs into quotable chunks
//
// ------------------------------------------------------------------
function filter_clickquote($content) {
	
	if( is_home() )
		return $content;
		
	return split_paragraphs( $content );
}


// ------------------------------------------------------------------
//
// Load jQuery and register ClickQuote filter with WP hooks
//
// ------------------------------------------------------------------
add_action('wp_print_scripts','load_header_info');
add_filter('comment_text', 'filter_clickquote');
add_filter('the_content','filter_clickquote');



// ------------------------------------------------------------------
//
// Setup ClickQuote options and admin menu
//
// ------------------------------------------------------------------
add_option("clickquote_hovercolor", "#FFF9BB", "The background color used when hovering over a paragraph.");
add_action('admin_menu', 'clickquote_menu');

function clickquote_menu() {	if (function_exists('add_options_page')) {
		add_options_page('ClickQuote', 'ClickQuote', 9, 'clickquote', 'show_clickquote_options');
  }
}


// ------------------------------------------------------------------
//
// Print the HTML for the ClickQuote options page
//
// ------------------------------------------------------------------
function show_clickquote_options()
{
			$formatmsg = "eg, #FFF9BB or #ccc";
      
      if($_POST['Submit'])
      {
				$clickquote_hovercolor = $_POST['clickquote_hovercolor'];
			
				if( preg_match("/^#([0-9a-fA-F]{1,2}){3}$/",$clickquote_hovercolor) == 0)
				{
					?>
					<div id="message" class="error"><p>Please enter a hex value for the color (<? echo $formatmsg; ?>)</p></div>
					<?php
				}
				else
				{
					update_option("clickquote_hovercolor", $clickquote_hovercolor);
				?>			
					<div id="message" class="updated fade"><p>Update successful!</p></div>';
				<?php
				}
			}
		?>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<div class="wrap">
			<h2>ClickQuote Options</h2>
			<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
				<tr valign="top">
					<th width="33%" scope="row">Hover color:</th>				
					<td>
						<input type="text" name="clickquote_hovercolor" value="<?php echo get_option('clickquote_hovercolor'); ?>" />
						<span style="color:#ccc;"><?php echo $formatmsg; ?></span>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="Submit" value="Update Options &raquo;" />
			</p>
		</div>
		</form>
		<?php
}

	
/* Metaforum style quoting, for reference, for a later version
			$stringQQ .= "$quoteBegin $codeBegin <div class='qqContainer' id='qqC$postID.$i'>";
			$stringQQ .= "<div class='qqContent' id='qq$postID.$i'>". $stringArray[$i] . "</div>";
			$stringQQ .= "<a class='qq' href='#' onClick=\"quickQuote('$poster', '$postID', '$i'); return false;\" onmouseover=			\"qqHover('qqC$postID.$i');return true;\" onmouseout=\"qqHoverOff('qqC$postID.$i');return true;\" ><img alt='[Q]' title='QuickQuote this passage' src='" . $imgpath . "' border='0' /></a>";
			$stringQQ .= "<div class='clearfix'></div></div> $codeEnd $quoteEnd";
*/

?>
