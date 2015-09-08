<?php


/**
 * IFTTT BOT plugin
 * v 1.0.0
 * Louis Eveillard & Arnaud Juracek
 *
 * https://github.com/arnaudjuracek/neomateriality/tree/master/site/plugins/iftttbot
 *
 */



// Where the magic happens

function create_post($page, $blueprint, $title, $data){

	// Where we'll put the content.
	$PATH = get_content_path($page);
	$SLUG = str::slug($title);
		$dir = $PATH . DS . $SLUG;
		$dir_matches = glob($PATH . DS . "*" . $SLUG . "*");

	// If the directory already exists don't override it,
	// append a number to it, no matter its visibility.
		// 1-directory
		// directory_1
		// 8-directory_2
	if(count($dir_matches) > 0){
		$dir .= "_" . count($dir_matches);
		$title .= "_" . count($dir_matches);
	}

	// Pass $title into the $data array for easiest manipulation.
	$data["title"] = $title;

	// Create the directory with read&write permissions.
	mkdir($dir, 0777, true);

	// Filename with (almost) multilingual support.
	// Peraphs you'll want to create different files for each
	// languages code.
	$filename = $blueprint . ".fr.txt";

	// Write the file.
	$file = fopen($dir . DS . $filename, 'w');
	if(flock($file, LOCK_EX)) {
		fwrite($file, parse_data(get_blueprint($blueprint), $data));
		flock($file, LOCK_EX);
	}
	fclose($file);

}



// Create the text file for the article

function parse_data($blueprint, $data){

	// This is the separator for markdown file.
	$separator = "\n\n----\n\n";

	// Append each param in the IFTTT body form in the markdown file,
	// separated with the markdown separator.
	// If a param name doesn't match the blueprint, it will be deleted
	// next time you save the article in the panel.
	$output = "";
	foreach($blueprint['fields'] as $key => $value){
		$output .= $separator;
		$output .= ucfirst($key) . ": " . $data[$key];
	}

	// Trim the first and last separator.
	$output = trim($output, $separator);

	return $output;

}



// Get the path of the content/page

function get_content_path($page){

	// Get the content path...
	$path = kirby()->roots()->content() . DS;

	// ... no matter its visibility ( 1-project is the same as project)
	$dir = glob($path . "*" . $page);

	if(count($dir)==1) return $dir[0];
	else return false;

}



// Get the blueprint of the content/page/blueprint

function get_blueprint_path($blueprint){

	$path = kirby()->roots->blueprints() . DS;
	$file = glob($path . $blueprint . '.php');

	if(count($file)==1) return $file[0];
	else return false;

}



// Return an array containing all field in the blueprint

function get_blueprint($blueprint){

	$file = get_blueprint_path($blueprint);

	if($file) return yaml::read($file);
	else return false;

}