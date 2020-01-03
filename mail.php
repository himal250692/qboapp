<?php


$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

echo "<pre/>"; print_r($url); die;
mail('hnagpal2506@gmail.com','Hi','Hello');
?>