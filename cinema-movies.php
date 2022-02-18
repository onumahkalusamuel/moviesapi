<?php

require_once __DIR__ . '/MoviesTrait.php';

final class CinemaMovies
{
    use MoviesTrait;
    protected $type = 'cinema-movies';
}

$M = new CinemaMovies();
$M->processMoviesList();
