<?php

require_once __DIR__ . '/MoviesTrait.php';

final class Series
{
    use MoviesTrait;
    protected $type = 'series';
}

$Series = new Series();
$Series->processMoviesList();
