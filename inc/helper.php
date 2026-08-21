<?php

// DEBUG
function vd1($var) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  var_dump($var);
  echo "\n</pre>\n";
}
function vd2($var1, $var2) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  var_dump($var1);
  var_dump($var2);
  echo "\n</pre>\n";
}
function vd3($var1, $var2, $var3) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  var_dump($var1);
  var_dump($var2);
  var_dump($var3);
  echo "\n</pre>\n";
}
function pr1($var) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  print_r($var);
  echo "\n</pre>\n";   
}
function pr1_logged_in($var) {
  if (is_user_logged_in()) {
  pr1($var);
  }  
}
function pr1_return($var) {
  ob_start();
  
  echo "<pre style='background: #FFFF99; font-size: 14px; color: black;'>";
  print_r($var);
  echo "</pre>";   

  return ob_get_clean();
}
function pr2($var1, $var2) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  print_r($var1);
  echo "\n";
  print_r($var2);
  echo "\n</pre>\n";
}
function pr3($var1, $var2, $var3) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  print_r($var1);
  echo "\n";
  print_r($var2);
  echo "\n";
  print_r($var3);
  echo "\n</pre>\n";
}
function pr4($var1, $var2, $var3, $var4) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 14px; color: black;\">\n";
  print_r($var1);
  echo "\n";
  print_r($var2);
  echo "\n";
  print_r($var3);
  echo "\n";
  print_r($var4);
  echo "\n</pre>\n";
}

function get_current_url() {
  return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function is_mobile()
{
	return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet' .
		'|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]' .
		'|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT']);
}

function convert_string_to_kebab_case($s) {
  $s = str_replace(' ', '-', strtolower($s));

  $chars_to_change = [
  ['ö', 'o'],
  ['ó', 'o'],
  ['ő', 'o'],
  ['ô', 'o'],
  ['ü', 'u'],
  ['ú', 'u'],
  ['ù', 'u'],
  ['ű', 'u'],
  ['û', 'u'],
  ['é', 'e'],
  ['è', 'e'],
  ['ê', 'e'],
  ['ë', 'e'],
  ['á', 'a'],
  ['à', 'a'],
  ['ä', 'a'],
  ['â', 'a'],
  ['ß', 's'],
  ['í', 'i'],
  ['î', 'i'],
  ['ï', 'i'],
  ['ü', 'u'],
  ['ç', 'c']
  ];

  foreach ($chars_to_change as $char_array) {
  $s = str_replace($char_array[0], $char_array[1], $s);
  }

  return $s;
}


