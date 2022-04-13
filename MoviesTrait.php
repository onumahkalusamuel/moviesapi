<?php

trait MoviesTrait
{
    private $defaultType = 'movies';
    private $streamContext;

    private $moviesBase = "https://membed.net/";
    private $moviesUrl = "https://membed.net/%s?page=%s";

    public function __construct()
    {
        // prepare stream context for external calls
        $this->streamContext = stream_context_create(
            array('http' => array('timeout' => 3))
        );

        // handle pre-flight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return $this->jsonResponse([]);
        }

        // handle json type POST requests
        $input = file_get_contents("php://input");
        if (json_decode($input, true) !== null) {
            $post = json_decode($input, true);
            foreach ($post as $key => $value) {
                $_POST[$key] = $_POST[$key] ?? $value;
            }
        }
    }

    public function processMoviesList()
    {
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;

        // fetch movies
        $movies = $this->fetchMovies($this->type ?? $this->defaultType, $page);

        $output = array(
            'success' => true,
            'data' => $movies,
            'pagination' => array('current_page' => (int) $page)
        );

        return $this->jsonResponse($output);
    }

    public function fetchMovies(string $type = 'movies', int $page = 1): array
    {
        $source = file_get_contents(sprintf($this->moviesUrl, $type, $page), false, $this->streamContext);
        return $this->extractMovies($source);
    }

    public function extractMovies(string $source = null): array
    {
        if (empty($source)) return [];

        $vids = [];
        $dom = new DOMDocument();

        $movies = explode('listing items">', $source);
        $movies = explode('</ul>', $movies[1]);
        $movies = @$dom->loadHTML("<ul>" . trim($movies[0]) . "</ul>");
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
            if ($status != 200) $source = "";
        } catch (Exception $e) {
            $source = "";
        }

        return $this->extractDetails($source);
    }

    public function getDownloadLinks(string $url): array
    {

        $source = "";
        $result = [];
        $downloadUrl = "https://" . str_replace('streaming.php', 'download', $url);
        $dom = new DOMDocument();

        try {
            $source = @file_get_contents($downloadUrl, false, $this->streamContext);
            $status_line = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $status = $match[1];
            if ($status != 200) $source = "";
        } catch (Exception $e) {
            $source = "";
        }


        if (!empty($source)) {

            $movie = explode('<div class="content_c_bg">', $source);
            $movie = explode('<footer>', $movie[1]);
            $movie = trim(
                trim(
                    trim(
                        trim($movie[0]),
                        '<span class="clr"></span>'
                    )
                ),
                '</div>'
            );

            $movie = @$dom->loadHTML('<div>' . trim($movie) . '</div>');

            $links = $dom->getElementsByTagName("a");

            /** @var DOMElement $link */
            foreach ($links as $link) $result[] = [
                'link' => $link->getAttribute('href'),
                'text' => $link->textContent
            ];
        }

        return $result;
    }

    public function extractDetails(string $source = null): array
    {

        if (empty($source)) return [];

        $details = [
            'title' => '',
            'stream_link' => '',
            'download_links' => [],
            'synopsis' => '',
            'episodes' => [],
            'latest_releases' => []
        ];

        $dom = new DOMDocument();
        // get stream link
        $movie = explode('<iframe src="//', $source);
        $movie = explode('"', $movie[1]);
        $moviePlayLink = $movie[0];

        // get synopsis
        $movie = explode('<div class="post-entry">', $source);
        $movie = explode('</div>', $movie[1]);
        $movie = @$dom->loadHTML('<div>' . trim($movie[0]) . '</div>');
        $movieSynopsis = trim($dom->textContent);

        // get download link
        $downloadLinks = $this->getDownloadLinks($moviePlayLink) ?? [];

        // get title
        $movie = explode('<div class="name">', $source);
        $movie = explode('</div>', $movie[1]);
        $movieTitle = trim($movie[0]);

        // get episodes
        $movie = explode('<h3 class="list_episdoe">List episode</h3>', $source);
        $movie = explode('<ul class="listing items lists">', $movie[1]);
        $movie = explode('</ul>', $movie[1]);
        $movieEpisodes = $this->extractMovies('<ul class="listing items">' . trim($movie[0]) . '</ul>');

        // get latest
        $movie = explode('<ul class="listing items videos">', $source);
        $movie = explode('</ul>', $movie[1]);
        $movieLatestReleases = $this->extractMovies('<ul class="listing items">' . trim($movie[0]) . '</ul>');

        if (!empty($moviePlayLink)) {
            $details['title'] = $movieTitle;
            $details['stream_link'] = "https://" . $moviePlayLink;
            $details['download_links'] = $downloadLinks;
            $details['synopsis'] = $movieSynopsis;
            //if(!empty($movieEpisodes) && count($movieEpisodes) > 1)
            $details['episodes'] = $movieEpisodes;
            $details['latest_releases'] = $movieLatestReleases;
        }

        return $details;
    }

    public function jsonResponse(array $data, int $code = 200)
    {

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
