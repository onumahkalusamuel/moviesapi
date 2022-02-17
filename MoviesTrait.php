<?php

trait MoviesTrait
{

    private $streamContext;

    private $moviesBase = "https://vidembed.me/";
    private $moviesUrl = "https://vidembed.me/%s?page=%s";

    public function __construct()
    {
        $this->streamContext = stream_context_create(
            array('http' => array('timeout' => 3))
        );

	if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	    return $this->jsonResponse([]);
	}
    }

    public function fetchMovies(string $type = 'movies', int $page = 1): array
    {
        $source = file_get_contents(sprintf($this->moviesUrl, $type, $page), false, $this->streamContext);
        return $this->extractMovies($source);
    }

    public function extractMovies(string $source = null): array
    {
	if(empty($source)) return [];

	$vids = [];
        $dom = new DOMDocument();

        $movies = explode('listing items">', $source);
        $movies = explode('</ul>', $movies[1]);
	$movies = @$dom->loadHTML("<ul>".trim($movies[0])."</ul>");
	$movies = $dom->getElementsByTagName('li');

        foreach ($movies as $movie) {
            $vid = [];
	    $vid['title'] = trim($movie->getElementsByTagName('div')[4]->nodeValue);
            $vid['slug'] = str_replace("/videos/", '', $movie->getElementsByTagName('a')[0]->getAttribute('href'));
            $vid['thumbnail'] = $movie->getElementsByTagName('img')[0]->getAttribute('src');
            $vids[] = $vid;
        }

	return $vids;
    }

    public function fetchDetails(string $slug): array
    {

	$source = "";

	try {
	    $source = @file_get_contents($this->moviesBase . "/videos/" . $slug, false, $this->streamContext);
	    $status_line = $http_response_header[0];
    	    preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
	    $status = $match[1];
	    if($status != 200) $source = "";

	} catch (Exception $e) { $source = ""; }

	return $this->extractDetails($source);
    }

    public function extractDetails(string $source = null): array
    {

	if(empty($source)) return [];

	$details = [];
        $dom = new DOMDocument();
	// get stream link
        $movie = explode('<iframe src="//', $source);
        $movie = explode('"', $movie[1]);
        $moviePlayLink = $movie[0];

	// get synopsis
        $movie = explode('<div class="post-entry">', $source);
        $movie = explode('</div>', $movie[1]);
        $movie = @$dom->loadHTML('<div>'.trim($movie[0]).'</div>');
        $movieSynopsis = trim($dom->textContent);

	// get title
	$movie = explode('<div class="name">', $source);
        $movie = explode('</div>', $movie[1]);
        $movieTitle = trim($movie[0]);

        if(!empty($moviePlayLink)) {
	    $details['title'] = $movieTitle;
            $details['stream_link'] = "https://" . $moviePlayLink;
            $details['synopsis'] = $movieSynopsis;
        }

	return $details;
    }

    public function jsonResponse(array $data, int $code = 200) {

	http_response_code($code);
	header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: no-cache');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization, Access-Control-Allow-Methods, Cache-Control');

	echo json_encode($data);

	exit();
    }
}
