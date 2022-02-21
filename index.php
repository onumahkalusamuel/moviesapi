<?php

require_once __DIR__ . '/MoviesTrait.php';

final class Index {

    use MoviesTrait;

    public function __construct() {

	$output = array(
	    'success' => true,
	    'message' => 'Welcome to the world No. 1 Movies Stream API.',
	    'author' => 'Chikezie',
	    'endpoints' => array(
		'/movies.php' => array(
		    'method' => 'GET|POST',
		    'description' => 'Fetch list of Movies.',
		    'parameters' => array(
			'page' => array(
			    'type' => 'integer',
			    'description' => 'Page number to be retrieved.',
			    'default' => 1
			)
		    )
		),
		'/cinema-movies.php' => array(
                    'method' => 'GET|POST',
                    'description' => 'Fetch list of Cinema Movies.',
                    'parameters' => array(
                        'page' => array(
                            'type' => 'integer',
                            'description' => 'Page number to be retrieved',
                            'default' => 1
                        )
                    )
                ),
		'/series.php' => array(
                    'method' => 'GET|POST',
                    'description' => 'Fetch list of Series movies',
                    'parameters' => array(
                        'page' => array(
                            'type' => 'integer',
                            'description' => 'Page number to be retrieved',
                            'default' => 1
                        )
                    )
                ),
		'/recommended-series.php' => array(
                    'method' => 'GET|POST',
                    'description' => 'Fetch list of Recommended Series',
                    'parameters' => array(
                        'page' => array(
                            'type' => 'integer',
                            'description' => 'Page number to be retrieved',
                            'default' => 1
                        )
                    )
                ),
		'/movie.php' => array(
                    'method' => 'GET|POST',
                    'description' => 'Get the full details of a single movie, including streaming link.',
                    'parameters' => array(
                        'slug' => array(
                            'type' => 'string',
                            'description' => 'The slug of the video to be loaded.',
                            'default' => null
                        )
                    )
                ),
		'/search.php' => array(
                    'method' => 'GET|POST',
                    'description' => 'Find movies by entering keyword(s).',
                    'parameters' => array(
                        'keyword' => array(
                            'type' => 'string',
                            'description' => 'Search keywords to be used in the search.',
                            'default' => null
                        ),
			'page' => array(
                            'type' => 'integer',
                            'description' => 'Page of search result to be fetched.',
                            'default' => 1
                        )
                    )
                ),
	    )
	);

	$this->jsonResponse($output);
    }
}

new Index();
