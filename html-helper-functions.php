<?php

function getMatchingSectionInHtml($html, $start, $end) {
   $startPosition = strpos($html, $start) + strlen($start);

   if($startPosition === FALSE) {
      echo "Unable to find starting element " . $start . " in " . $html . PHP_EOL;
   }

   $endPosition = strpos($html, $end, $startPosition);

   if($endPosition === FALSE) {
      echo "Unable to find ending element " . $end . " in " . $html . PHP_EOL;
   }

   $lengthOfSection = $endPosition - $startPosition;
   return trim(substr($html, $startPosition, $lengthOfSection));
}

function getAllMatchingSectionsInHtml($html, $start, $end) {
   $sections = array();

   $endPosition = 0;
   while ( ($startPosition = strpos($html, $start, $endPosition)) !== FALSE) {
      $startPosition = $startPosition + strlen($start);
      $endPosition = strpos($html, $end, $startPosition);
      $lengthOfSection = $endPosition - $startPosition;

      $section = substr($html, $startPosition, $lengthOfSection);

      $sections[] = $section;
   }

   return $sections;
}

function getListInHtml($html, $elementBeforeStartOfList) {
   $openingTag = '<ul>';
   $closingTag = '</ul>';

   $indexOfListSectionStart = strpos($html, $elementBeforeStartOfList) + strlen($elementBeforeStartOfList);
   if($indexOfListSectionStart === FALSE) {
      echo "Unable to find start of list in " . $html . " after " . $elementBeforeStartOfList . PHP_EOL;
      exit(1);
   }

   $indexOfListSectionStart = strpos($html, $openingTag, $indexOfListSectionStart);
   $listHtml = substr($html, $indexOfListSectionStart);

   $indexOfStatesListSectionEnd = strpos($listHtml, $closingTag) + strlen($closingTag);
   $listHtml = substr($listHtml, 0, $indexOfStatesListSectionEnd);

   return $listHtml;
}

?>