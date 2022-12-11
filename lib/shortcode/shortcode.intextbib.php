<?php

function Zotpress_zotpressInTextBib ($atts)
{
    /*
    *   RELIES ON THESE GLOBAL VARIABLES:
    *
    *   $GLOBALS['zp_shortcode_instances'][get_the_ID()] {instantiated previously}
    *
    */

    extract(shortcode_atts(array(
        'style' => false,
        'sortby' => "default",
        'sort' => false,
        'order' => false,

        'image' => false,
        'images' => false,
        'showimage' => "no",
        'showtags' => "no",

        'title' => "no",

        'download' => "no",
        'downloadable' => false,
        'notes' => false,
        'abstract' => false,
        'abstracts' => false,
        'cite' => false,
        'citeable' => false,

        'target' => false,
		'urlwrap' => false,

		'highlight' => false,
        'forcenumber' => false,
        'forcenumbers' => false

    ), $atts));



    // FORMAT PARAMETERS
    $style = str_replace('"','',html_entity_decode($style));
    $sortby = str_replace('"','',html_entity_decode($sortby));

    if ($order) $order = str_replace('"','',html_entity_decode($order));
    else if ($sort) $order = str_replace('"','',html_entity_decode($sort));
    else $order = "asc";
    $order = strtolower($order);

    // Show image
    if ($showimage) $showimage = str_replace('"','',html_entity_decode($showimage));
    if ($image) $showimage = str_replace('"','',html_entity_decode($image));
    if ($images) $showimage = str_replace('"','',html_entity_decode($images));

    if ($showimage == "yes" || $showimage == "true" || $showimage === true ) $showimage = true;
	else if ( $showimage === "openlib") $showimage = "openlib";
    else $showimage = false;

    // Show tags
    if ($showtags == "yes" || $showtags == "true" || $showtags === true) $showtags = true;
    else $showtags = false;

    $title = str_replace('"','',html_entity_decode($title));

    if ($download) $download = str_replace('"','',html_entity_decode($download));
    else if ($downloadable) $download = str_replace('"','',html_entity_decode($downloadable));
    if ($download == "yes" || $download == "true" || $download === true) $download = true; else $download = false;

    $shownotes = str_replace('"','',html_entity_decode($notes));

    if ($abstracts) $abstracts = str_replace('"','',html_entity_decode($abstracts));
    else if ($abstract) $abstracts = str_replace('"','',html_entity_decode($abstract));

    if ($citeable) $citeable = str_replace('"','',html_entity_decode($citeable));
    else if ($cite) $citeable = str_replace('"','',html_entity_decode($cite));

    if ($target == "new" || $target == "yes" || $target == "_blank" || $target == "true" || $target === true) $target = true;
    else $target = false;

    if ($urlwrap == "title" || $urlwrap == "image" )
	$urlwrap = str_replace('"','',html_entity_decode($urlwrap)); else $urlwrap = false;

    if ($highlight ) $highlight = str_replace('"','',html_entity_decode($highlight)); else $highlight = false;

    if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
        $forcenumber = true;
    if ($forcenumbers == "yes" || $forcenumbers == "true" || $forcenumbers === true)
        $forcenumber = true;

    // Set up request vars
    $request_start = 0;
    $request_last = 0;
    $overwrite_last_request = false;
    $update = false;

    // Set up item key
	$item_key = "";


	// Get in-text items
	if ( isset( $GLOBALS['zp_shortcode_instances'][get_the_ID()] ) )
	{
        // Handle the possible formats of item/s for in-text
    	//
    	// IN-TEXT FORMATS:
    	// [zotpressInText item="NCXAA92F"]
    	// [zotpressInText item="{NCXAA92F}"]
    	// [zotpressInText item="{NCXAA92F,10-15}"]
    	// [zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]
    	// [zotpressInText items="{000001:NCXAA92F,10-15},{000003:3ITTIXHP}"]
    	// So no multiples without curlies or non-curlies in multiples

		foreach ( $GLOBALS['zp_shortcode_instances'][get_the_ID()] as $intextitem )
		{
            // REVIEW: Actually, let's just remove pages
            $intextitem["items"] = preg_replace( "/(((,))+([\w\d-]+(})+))++/", "}", $intextitem["items"] );

            // Add separator if not the start
			if ( $item_key != "" ) $item_key .= ";";

            // Add to the item key
			$item_key .= $intextitem["items"];
		}
	}

    // Generate instance id for shortcode
    // REVIEW: Added Post ID and newish attributes
    $instance_id = "zotpress-".md5($item_key.$style.$sortby.$order.$title.$showimage.$showtags.$download.$shownotes.$abstracts.$citeable.$target.$urlwrap.$forcenumber.$highlight.get_the_ID());


    // GENERATE IN-TEXT BIB STRUCTURE
	$zp_output = "\n<div id='zp-InTextBib-".$instance_id."'";
    $zp_output .= " class='zp-Zotpress zp-Zotpress-InTextBib wp-block-group";
	if ( $forcenumber ) $zp_output .= " forcenumber";
	$zp_output .= " zp-Post-".get_the_ID()."'>";
	$zp_output .= '
		<span class="ZP_ITEM_KEY" style="display: none;">'.$item_key.'</span>
		<span class="ZP_STYLE" style="display: none;">'.$style.'</span>
		<span class="ZP_SORTBY" style="display: none;">'.$sortby.'</span>
		<span class="ZP_ORDER" style="display: none;">'.$order.'</span>
		<span class="ZP_TITLE" style="display: none;">'.$title.'</span>
		<span class="ZP_SHOWIMAGE" style="display: none;">'.$showimage.'</span>
		<span class="ZP_SHOWTAGS" style="display: none;">'.$showtags.'</span>
		<span class="ZP_DOWNLOADABLE" style="display: none;">'.$download.'</span>
		<span class="ZP_NOTES" style="display: none;">'.$shownotes.'</span>
		<span class="ZP_ABSTRACT" style="display: none;">'.$abstracts.'</span>
		<span class="ZP_CITEABLE" style="display: none;">'.$citeable.'</span>
		<span class="ZP_TARGET" style="display: none;">'.$target.'</span>
		<span class="ZP_URLWRAP" style="display: none;">'.$urlwrap.'</span>
		<span class="ZP_FORCENUM" style="display: none;">'.$forcenumber.'</span>
		<span class="ZP_HIGHLIGHT" style="display: none;">'.$highlight.'</span>
		<span class="ZP_POSTID" style="display: none;">'.get_the_ID().'</span>';

        // <span class="ZP_API_USER_ID" style="display: none;">'.$api_user_id.'</span>
		// <span class="ZOTPRESS_PLUGIN_URL" style="display:none;">'.ZOTPRESS_PLUGIN_URL.'</span>'

    // $zp_output .= "<div class='zp-List loading'></div><!-- .zp-List --></div><!--.zp-Zotpress-->\n\n";
    $zp_output .= "<div class='zp-List loading'>";

    $_GET['instance_id'] = $instance_id;
    // $_GET['api_user_id'] = $api_user_id;
    $_GET['item_key'] = $item_key;
    // $_GET['collection_id'] = $collection_id;
    // $_GET['tag_id'] = $tag_id;
    // $_GET['author'] = $author;
    // $_GET['year'] = $year;
    // $_GET['item_type'] = $item_type;
    // $_GET['inclusive'] = $inclusive;
    $_GET['style'] = $style;
    // $_GET['limit'] = $limit;
    $_GET['sortby'] = $sortby;
    $_GET['order'] = $order;
    $_GET['title'] = $title;
    $_GET['showimage'] = $showimage;
    $_GET['showtags'] = $showtags;
    $_GET['downloadable'] = $downloadable;
    $_GET['shownotes'] = $shownotes;
    $_GET['abstracts'] = $abstracts;
    $_GET['citeable'] = $citeable;
    $_GET['target'] = $target;
    $_GET['urlwrap'] = $urlwrap;
    $_GET['forcenumber'] = $forcenumber;
    $_GET['highlight'] = $highlight;

    $_GET['request_start'] = $request_start;
    $_GET['request_last'] = $request_last;
    // $_GET['is_dropdown'] = $is_dropdown;
    // $_GET['maxresults'] = $maxresults;
    // $_GET['maxperpage'] = $maxperpage;
    // $_GET['maxtags'] = $maxtags;
    // $_GET['term'] = $term;
    $_GET['update'] = $update;
    $_GET['overwrite_last_request'] = $overwrite_last_request;

    $zp_output .= "\n<div class=\"zp-SEO-Content\">";
    $zp_output .= Zotpress_shortcode_request( true ); // Check catche first
    $zp_output .= "</div><!-- .zp-zp-SEO-Content -->\n";

    $zp_output .= "</div><!-- .zp-List --></div><!--.zp-Zotpress-->\n\n";

	// Show theme scripts
	$GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zp_output;
}

?>
