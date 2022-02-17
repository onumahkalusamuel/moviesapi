<?php

require_once __DIR__ . '/MoviesTrait.php';

final class CinemaMovies
{

    use MoviesTrait;

    protected $type = 'cinema-movies';

    public function process() {

        $filters = $params = [];
	$page = $_GET['page'] ?? 1;

        // fetch movies
        $movies = $this->fetchMovies($this->type, $page);

	$output = array(
	    'success' => true,
	    'data' => $movies,
	    'pagination' => array('current_page' => (int) $page)
	);

	return $this->jsonResponse($output);
    }
}

$Movies = new CinemaMovies();
$Movies->process();