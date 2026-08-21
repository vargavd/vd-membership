<?php

function vd_membership_enqueue_style_with_filetime($name, $filePath, $deps) {
  wp_enqueue_style($name, get_stylesheet_directory_uri() . $filePath, $deps, filemtime(get_stylesheet_directory() . $filePath));
}
function vd_membership_register_style_with_filetime($name, $filePath, $deps) {
  wp_register_style($name, get_stylesheet_directory_uri() . $filePath, $deps, filemtime(get_stylesheet_directory() . $filePath));
}
function vd_membership_enqueue_script_with_filetime($name, $filePath, $deps, $in_footer = true) {
  wp_enqueue_script($name, get_stylesheet_directory_uri() . $filePath, $deps, filemtime(get_stylesheet_directory() . $filePath), $in_footer);
}
function vd_membership_register_script_with_filetime($name, $filePath, $deps, $in_footer = true) {
  wp_register_script($name, get_stylesheet_directory_uri() . $filePath, $deps, filemtime(get_stylesheet_directory() . $filePath), $in_footer);
}