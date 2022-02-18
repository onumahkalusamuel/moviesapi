<?php

require_once __DIR__ . '/MoviesTrait.php';

final class Movies
{
    use MoviesTrait;
    protected $type = 'movies';
}

$M = new Movies();
$M->processMoviesList();
