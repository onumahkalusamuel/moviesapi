<?php

require_once __DIR__ . '/MoviesTrait.php';

final class Movie
{

    use MoviesTrait;

    private $type = 'movies';

    public function process() {

	$slug = $_POST['slug'] ?? $_GET['slug'] ?? null;

	if(!empty($slug)) $movie = $this->fetchDetails($slug);

	if(!empty($movie)) {
	    $output = array('success' => true,'data' => $movie);
	    return $this->jsonResponse($output);
	} else {
	    $output = array('success' => false, 'message' => 'Movie not found...');
	    return $this->jsonResponse($output, 404);
	}
    }
}

$Movie = new Movie();
$Movie->process();
