<?php
/**
 * @package GNU_Terry_Pratchett_quotes
 * @version 0.15
 */
/*
Plugin Name: GNU Terry Pratchett quotes
Plugin URI: https://github.com/tjk911/gnu-pratchett-quotes
Description: Just a little thing to keep the memories of Pratchett alive.
Author: Kai Teoh
Version: 0.15
Author URI: http://twitter.com/jkteoh
*/

function init()
{
    if (file_exists("pratchett.json")) {
        return get_pratchett();
    } else {
        $initial_scrape = file_get_contents('http://www.lspace.org/ftp/words/pqf/pqf');
        $jsonify = explode("\n\n", $initial_scrape);
        file_put_contents("pratchett.json", json_encode($jsonify, JSON_FORCE_OBJECT));
        return get_pratchett();
    }
}

function get_pratchett()
{
    $original = file_get_contents("pratchett.json");
    $data = json_decode($original, true);

    return wptexturize($data[mt_rand(0, count($data) -1)]);
}

// Gutenberg causes issues with this plugin, so this is a hack job to add it to
// admin_head but also as a meta_box in edit page.
function GNU_add_meta_box()
{
    $screen = get_current_screen();
    if ($screen->id == 'post') {
        add_meta_box('gnu-quotes', __('Terry Pratchett Quotes'), 'GNU');
        echo 'edit screen';
    } else {
        add_action('admin_notices', 'GNU');
    }
}

// Now we set that function up to execute when the admin_notices action is called
add_action('admin_head', 'GNU_add_meta_box');

// This just echoes the chosen line, we'll position it later
function GNU()
{
    global $post;

    $chosen = init();
    $line_break = str_replace("\n", "</br>", $chosen);

    echo "<p id='pratchett'>$line_break</p>";
}

// We need some CSS to position the paragraph
function GNU_css()
{
    $x = is_rtl() ? 'left' : 'right';

    echo "
    <style type='text/css'>
    #pratchett {
        padding-$x: 15px;
        padding-top: 5px;
        margin: 0;
        font-size: 11px;
    }
    </style>
    ";
}

add_action('admin_head', 'GNU_css');
