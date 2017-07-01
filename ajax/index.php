<?php
 //header('Location: maintainance.html');exit(0);

//   if ('www.fiberstore.com' != $_SERVER['HTTP_HOST']){
//  	//header('Location: http://www.fiberstore.com');
//   }
//   if ('208.109.106.23' == $_SERVER['HTTP_HOST']){
//  	//header('Location: http://www.fiberstore.com');
//   }
//var_dump($_GET);
//$_GET['page'] = 3;
  require('includes/application_top.php');
  $language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/';
  //echo $_SESSION['language'];
  //echo $_SESSION['languages_id'];
  //echo $_SESSION['languages_code'];
  $directory_array = $template->get_template_part($code_page_directory, '/^header_php/');

  foreach ($directory_array as $value) {
/**
 * We now load header code for a given page.
 * Page code is stored in includes/modules/pages/PAGE_NAME/directory
 * 'header_php.php' files in that directory are loaded now.
 */
    require($code_page_directory . '/' . $value);
  }
/**
 * We now load the html_header.php file. This file contains code that would appear within the HTML <head></head> code
 * it is overridable on a template and page basis.
 * In that a custom template can define its own common/html_header.php file
 */
  require($template->get_template_dir('html_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/html_header.php');
/**
 * Define Template Variables picked up from includes/main_template_vars.php unless a file exists in the
 * includes/pages/{page_name}/directory to overide. Allowing different pages to have different overall
 * templates.
 */
  require($template->get_template_dir('main_template_vars.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/main_template_vars.php');
/**
 * Read the "on_load" scripts for the individual page, and from the site-wide template settings
 * NOTE: on_load_*.js files must contain just the raw code to be inserted in the <body> tag in the on_load="" parameter.
 * Looking in "/includes/modules/pages" for files named "on_load_*.js"
 */
  $directory_array = $template->get_template_part(DIR_WS_MODULES . 'pages/' . $current_page_base, '/^on_load_/', '.js');
  foreach ($directory_array as $value) {
    $onload_file = DIR_WS_MODULES . 'pages/' . $current_page_base . '/' . $value;
    $read_contents='';
    $lines = @file($onload_file);
    foreach($lines as $line) {
      $read_contents .= $line;
    }
  $za_onload_array[] = $read_contents;
  }
/**
 * now read "includes/templates/TEMPLATE/jscript/on_load/on_load_*.js", which would be site-wide settings
 */
  $directory_array=array();
  $tpl_dir=$template->get_template_dir('.js', DIR_WS_TEMPLATE, 'jscript/on_load', 'jscript/on_load_');
  $directory_array = $template->get_template_part($tpl_dir ,'/^on_load_/', '.js');
  foreach ($directory_array as $value) {
    $onload_file = $tpl_dir . '/' . $value;
    $read_contents='';
    $lines = @file($onload_file);
    foreach($lines as $line) {
      $read_contents .= $line;
    }
    $za_onload_array[] = $read_contents;
  }

  // set $zc_first_field for backwards compatibility with previous version usage of this var
  if (isset($zc_first_field) && $zc_first_field !='') $za_onload_array[] = $zc_first_field;

  $zv_onload = "";
  if (isset($za_onload_array) && count($za_onload_array)>0) $zv_onload=implode(';',$za_onload_array);

  //ensure we have just one ';' between each, and at the end
  $zv_onload = str_replace(';;',';',$zv_onload.';');

  // ensure that a blank list is truly blank and thus ignored.
  if (trim($zv_onload) == ';') $zv_onload='';
/**
 * Define the template that will govern the overall page layout, can be done on a page by page basis
 * or using a default template. The default template installed will be a standard 3 column layout. This
 * template also loads the page body code based on the variable $body_code.
 */
  require($template->get_template_dir('tpl_main_page.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_main_page.php');
?>
</html>
<?php
/**
 * Load general code run before page closes
 */
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
