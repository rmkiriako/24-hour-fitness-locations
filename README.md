# 24 Hour Fitness Mapping

## What Is This?

The 24 Hour Fitness chain of gyms has four different club types:

* Active
* Sport
* Super Sport
* Ultra Sport

This project creates a Google Map with every 24 Hour Fitness location in the country on it along with their location and club type.

## Why Did I Write This?

**Back in 2012**, I was looking to join a gym and wanted to see which level of gym membership I should get.

At the time I was looking to join 24 Hour Fitness, their website did not have a dynamic map which displayed all of their locations, a feature I was looking for in order to determine whether it was worth it to get a higher level membership (which of course comes with a higher cost) as I was planning to travel around the country.

What their website had instead was a form on which you could input an address or zip code and obtain a static map that only showed you a few locations.

Searching around the country with this got annoying (especially since I couldn't pan around or zoom in/out of the map) so I created my own page to accomplish what I was looking for.

I then used the map to determine and purchase the membership level most appropriate for my travels.

## Where's the Data?

All of the processed data can be found in the [24-hour-fitness-locations.json](./24-hour-fitness-locations.json) file

This data is the backing behind the map at [https://tinyurl.com/24HourFitnessMap](https://tinyurl.com/24HourFitnessMap) which allows you to visually see where all the 24 Hour Fitness Locations are

## What Did I Do?

I wrote a PHP script to scrape the [http://www.24hourfitness.com](http://www.24hourfitness.com) site for the locations and club types of every 24 Hour Fitness location in the country.

I then used [Google's Geocoder API](https://developers.google.com/maps/documentation/geocoding/intro) to get the latitude and longitude of all of their locations' addresses, and overlayed that information on a Google Map.

The webpage is a simple HTML page with JavaScript on board. It utilizes the [jQuery JavaScript library](https://github.com/jquery/jquery) and the [Google Maps AJAX Map Control](https://developers.google.com/maps/documentation/javascript/tutorial).

The data that is displayed on the page is stored in a JSON file, which is the output of the PHP script, and this JSON file is parsed when the page loads.

## How Does It Work?

The core functionality is in the main PHP script [24-hour-fitness-locations.php](./24-hour-fitness-locations.php) as follows:

1. Go to the [main page](24hourfitness.com/ClubList) to find all the states in whcih 24 Hour Fitness has locations in

2. Follow each of the state links to find all the cities in which there is a 24 Hour Fitness location

   * For example, [this](http://www.24hourfitness.com/ClubList/ca) is a listing of all 24 Hour Fitness locations in the state of California

3. Follow each of the city links to find all the individual locations in each city 

   * For example, [this](http://www.24hourfitness.com/ClubList/ca/santa_monica) is a listing of the 24 Hour Fitness locations in the city of Santa Monica, CA

4. Parse all the data for each individual location:

   * Club Name
   * Club Type
   * Address

5. Execute a call to [Google's Geocoder API](https://developers.google.com/maps/documentation/geocoding/intro) to get the latitude and longitude of each location's address


