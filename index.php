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
		    'method' => 'GET',
		    'description' => 'Fetch list of Movies.',
		    'queryString' => array(
			'page' => array(
			    'type' => 'integer',
			    'description' => 'Page number to be retrieved.',
			    'default' => 1
			)
		    )
		),
		'/cinema-movies.php' => array(
                    'method' => 'GET',
                    'description' => 'Fetch list of Cinema Movies.',
                    'queryString' => array(
                        'page' => array(
                            'type' => 'integer',
                            'description' => 'Page number to be retrieved',
                            'default' => 1
                        )
                    )
                ),
		'/movie.php' => array(
                    'method' => 'GET',
                    'description' => 'Get the full details of a single movie, including streaming link.',
                    'queryString' => array(
                        'slug' => array(
                            'type' => 'string',
                            'description' => 'The slug of the video to be loaded.',
                            'default' => null
                        )
                    )
                ),
	    )
	);

	$this->jsonResponse($output);
    }
}

new Index();
