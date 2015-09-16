<?php
$jsonFileName = 'anime-titles.json';

if (!file_exists($jsonFileName) || time() - filemtime($jsonFileName) >= 60*60*24) {
    exec('wget http://anidb.net/api/anime-titles.xml.gz && gzip -d anime-titles.xml.gz');

    $xmlContent = simplexml_load_file('anime-titles.xml');

    $animesToJSON = [];

    foreach($xmlContent->children() as $anime) {
        $id = (int) $anime->attributes()['aid'];

        $titles = $anime->xpath('title');

        foreach ($titles as $title) {
            if ($type = (string) $title->attributes()['type'] === 'main') {
                $title = (string) $title;
                break;
            }
        }

        $animesToJSON[] = [
            'id' => $id,
            'title' => $title
        ];
    }

    file_put_contents($jsonFileName, json_encode($animesToJSON));

    unlink('anime-titles.xml');
}
