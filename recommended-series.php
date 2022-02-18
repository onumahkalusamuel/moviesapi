<?php

require_once __DIR__ . '/MoviesTrait.php';

final class RecommendedSeries
{
    use MoviesTrait;
    protected $type = 'recommended-series';
}

$R = new RecommendedSeries();
$R->processMoviesList();
