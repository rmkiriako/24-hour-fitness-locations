<?php

require('google-maps-geocode-function.php');
require('html-helper-functions.php');

$topLevelSite = 'http://www.24hourfitness.com';

$anchorClose = '</a>';
$dashWithSpaces = ' - ';
$hrefEnd = '">';
$hrefStart = 'href="';
$lineBreak = '<br />';
$strongClose = '</strong>';
$strongOpen = '<strong>';
$tableClose = '</table>';
$tableColumnClose = '</td>';
$tableColumnOpen = '<td';
$tableRowClose = '</tr>';

$locations = getStatesWith24HourFitnessLocations();

foreach($locations['states'] as &$stateWith24HourFitness) {
   $stateName = $stateWith24HourFitness['name'];
   $stateLink = $stateWith24HourFitness['link'];

   $stateWith24HourFitness['cities'] = getCitiesWith24HourFitnessLocationsIn($stateName, $stateLink);
}

echo json_encode($locations, JSON_UNESCAPED_SLASHES);

function getCitiesWith24HourFitnessLocationsIn($stateName, $stateLink) {
   global $topLevelSite;

   global $anchorClose;
   global $hrefEnd;
   global $hrefStart;

   /*
      This is a page that contains all the cities in a particular state that 24 Hour Fitness has a presence in

      The HTML section we care about is formatted as a list of links like this:

      <h1>{stateName} 24 Hour Fitness GYM - Health Fitness Club {stateName}</h1>
      <ul>
         <li><a href="{relativeLinkToCityPage}">{cityName}</a></li>
         ...
      </ul>

      We want to extract each city's name and the absolute link to its 24 Hour Fitness page
      as well as the information about each location in that city
   */
   $stateWith24HourFitnessLocationsHtml = file_get_contents($stateLink);
   
   $headerBeforeList = "<h1>${stateName} 24 Hour Fitness GYM - Health Fitness Club ${stateName}</h1>";
   $citiesListHtml = getListInHtml($stateWith24HourFitnessLocationsHtml, $headerBeforeList);

   $relativeLinksToCityClubs = getAllMatchingSectionsInHtml($citiesListHtml, $hrefStart, $hrefEnd);
   $cityNames = getAllMatchingSectionsInHtml($citiesListHtml, $hrefEnd, $anchorClose);

   $cityLocationsInfo = array();
   foreach($relativeLinksToCityClubs as $index => $relativeLink) {
      $cityLink = $topLevelSite . $relativeLink;
      $cityName = $cityNames[$index];

      $cityLocationsInfo[$index]['link'] = $cityLink;
      $cityLocationsInfo[$index]['name'] = $cityName;
      $cityLocationsInfo[$index]['clubs'] = getInfoForClubsInCity($cityLink);
   }

   return $cityLocationsInfo;
}

function getInfoForClubInHtmlColumn($html) {
   global $topLevelSite;

   global $dashWithSpaces;
   global $hrefEnd;
   global $hrefStart;
   global $lineBreak;
   global $strongClose;
   global $strongOpen;

   /*
      This is a section of the page that contains all the locations in a city that 24 Hour Fitness has a presence in

      The HTML section we care about is formatted as a single column in a table like this:

      <td>
         <a href={relativeLinkTo24HourLocationPage}>
            <strong>{clubName} - {clubType}</strong>
         </a>
         <br>
         {addressLine}
         <br>
         {cityStateZip}
      </td>

      We want to extract each location's address, club name, club type, and the absolute link to its 24 Hour Fitness page

      After extracting all the information from this section, we'll make a call to the Google Maps Geocoder API
      to get the latitude and longitude of each location
   */

   $link = $topLevelSite . getMatchingSectionInHtml($html, $hrefStart, $hrefEnd);
   $name = getMatchingSectionInHtml($html, $strongOpen, $dashWithSpaces);
   $type = getMatchingSectionInHtml($html, $dashWithSpaces, $strongClose);
   $addressLine = getMatchingSectionInHtml($html, $lineBreak, $lineBreak);

   $indexOfLastLineBreak = strrpos($html, $lineBreak);
   $indexOfCityStateZipStart = $indexOfLastLineBreak + strlen($lineBreak);
   $cityStateZip = trim(substr($html, $indexOfCityStateZipStart));

   $formattedAddress = "$addressLine, $cityStateZip";

   $clubInfo = array();
   $clubInfo['name'] = $name;
   $clubInfo['type'] = $type;
   $clubInfo['link'] = $link;
   $clubInfo['address'] = $formattedAddress;
   $clubInfo['location'] = geocode($formattedAddress);
   
   return $clubInfo;
}

function getInfoForClubsInCity($cityLink) {
   global $topLevelSite;
   
   global $tableClose;
   global $tableColumnClose;
   global $tableColumnOpen;
   global $tableRowClose;

   /*
      This is a page that contains all the locations in a city that 24 Hour Fitness has a presence in

      The HTML section we care about is formatted as a table like this:

      <table id="clubListTable">
         <tbody>
            <tr class="oddRow">
               <td>
                  <a href={relativeLinkTo24HourLocationPage}>
                     <strong>{clubName} - {clubType}</strong>
                  </a>
                  <br>
                  {addressLine}
                  <br>
                  {cityStateZip}
               </td>
               <td>
                  ...
               </td>
            </tr>
            <tr class="evenRow">
               <td>
                  <a href={relativeLinkTo24HourLocationPage}>
                     <strong>{clubName} - {clubType}</strong>
                  </a>
                  <br>
                  {addressLine}
                  <br>
                  {cityStateZip}
               </td>
               <td>
                  ...
               </td>
            </tr>
            ...
         </tbody>
      </table>

      We want to extract each location's address, club name, club type, and the absolute link to its 24 Hour Fitness page
   */
   $cityLocationsHtml = file_get_contents($cityLink);

   $clubListTableStartElement = '<table id="clubListTable"';

   $clubCount = 0;
   $clubsInCity = array();

   $cityLocationsTableHtml = getMatchingSectionInHtml($cityLocationsHtml, $clubListTableStartElement, $tableClose);
   $oddRowStart = '<tr class="oddRow">';
   while ( $indexOfRowStart = strpos($cityLocationsTableHtml, $oddRowStart) !== FALSE) {
      $firstColumnInRow = getMatchingSectionInHtml($cityLocationsTableHtml, $tableColumnOpen, $tableColumnClose);
      $clubsInCity[$clubCount] = getInfoForClubInHtmlColumn($firstColumnInRow);
      
      $indexOfRowEnd = strpos($cityLocationsTableHtml, $tableRowClose, $indexOfRowStart);
      $cityLocationsTableHtml = substr($cityLocationsTableHtml, $indexOfRowEnd);

      $clubCount = $clubCount + 1;
   }

   $cityLocationsTableHtml = getMatchingSectionInHtml($cityLocationsHtml, $clubListTableStartElement, $tableClose);
   $evenRowStart = '<tr class="evenRow">';
   while ( $indexOfRowStart = strpos($cityLocationsTableHtml, $evenRowStart) !== FALSE) {
      $firstColumnInRow = getMatchingSectionInHtml($cityLocationsTableHtml, $tableColumnOpen, $tableColumnClose);
      $clubsInCity[$clubCount] = getInfoForClubInHtmlColumn($firstColumnInRow);
      
      $indexOfRowEnd = strpos($cityLocationsTableHtml, $tableRowClose, $indexOfRowStart);
      $cityLocationsTableHtml = substr($cityLocationsTableHtml, $indexOfRowEnd);

      $clubCount = $clubCount + 1;
   }

   return $clubsInCity;
}

function getStatesWith24HourFitnessLocations() {
   global $topLevelSite;

   global $anchorClose;
   global $hrefEnd;
   global $hrefStart;

   /*
      This is a page that contains all the states that 24 Hour Fitness has a presence in

      The HTML section we care about is formatted as a list of links like this:

      <h1>24 Hour Fitness GYM - Health Fitness Club US</h1>
      <ul>
         <li><a href="{relativeLinkToStatePage}">{stateName}</a></li>
         ...
      </ul>

      We want to extract each state's name and the absolute link to its 24 Hour Fitness page
   */
   $allStateWith24HourFitnessLocationsHtml = file_get_contents($topLevelSite . '/ClubList');
   
   $headerBeforeList = '<h1>24 Hour Fitness GYM - Health Fitness Club US</h1>';
   $statesListHtml = getListInHtml($allStateWith24HourFitnessLocationsHtml, $headerBeforeList);
   
   $relativeLinksToStateClubs = getAllMatchingSectionsInHtml($statesListHtml, $hrefStart, $hrefEnd);
   $stateNames = getAllMatchingSectionsInHtml($statesListHtml, $hrefEnd, $anchorClose);

   $stateLocationsInfo = array();
   foreach($relativeLinksToStateClubs as $index => $relativeLink) {
      $stateLink = $topLevelSite . $relativeLink;
      $stateName = $stateNames[$index];

      $stateLocationsInfo[$index]['link'] = $stateLink;
      $stateLocationsInfo[$index]['name'] = $stateName;
   }

   $states = array();
   $states['states'] = $stateLocationsInfo;

   return $states;
}

?>
