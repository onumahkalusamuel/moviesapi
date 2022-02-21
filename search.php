<?php

require_once __DIR__ . '/MoviesTrait.php';

final class Search
{

    use MoviesTrait;

    private $searchUrl = 'https://vidembed.io/search.html?keyword=%s&page=%s';

    public function process() {

        $keyword = $_POST['keyword'] ?? $_GET['keyword'] ?? '';
	$page = $_POST['page'] ?? $_GET['page'] ?? 1;
	$movies = [];

        // search
        if(!empty($keyword)) {
	    $source = @file_get_contents(sprintf($this->searchUrl, $keyword, $page));
   	    $movies = $this->extractMovies($source);
	}

	$output = array(
	    'success' => true,
	    'data' => $movies,
	    'pagination' => array('current_page' => (int) $page)
	);

	return $this->jsonResponse($output);
    }
}

$Search = new Search();
$Search->process();
