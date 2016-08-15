<?php

function geocode($address) {
   $endpoint = 'http://maps.googleapis.com/maps/api/geocode/json?address=';
   $queryString = '?address=' . urlencode($address) . '&sensor=false';
   $geocodeRequestUrl = $endpoint . $queryString;

   $response = file_get_contents($geocodeRequestUrl);
   
   $json = json_decode($response, true);
   
   $latitude = $json['results'][0]['geometry']['location']['lat'];
   $longitude = $json['results'][0]['geometry']['location']['lng'];
   
   $location = array();
   $location['latitude'] = $latitude;
   $location['link'] = $geocodeRequestUrl;
   $location['longitude'] = $longitude;

   // Pause for a bit avoid Google's usage limits of their Geocoding API
   // https://developers.google.com/maps/documentation/geocoding/usage-limits
   sleep(2);
   
   return $location;
}

?>