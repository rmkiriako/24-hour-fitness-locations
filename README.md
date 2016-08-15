# 24 Hour Fitness Locations

The 24 Hour Fitness chain of gyms has four different levels:

* Active
* Sport
* Super Sport
* Ultra Sport

## Why Did I Write This?

**Back in 2012**, I was looking to join a gym and wanted to see which level of gym membership I should get.

At the time I was looking to join 24 Hour Fitness, their website did not have a dynamic map which displayed all of their locations, a feature I was looking for in order to determine whether it was worth it to get a higher level membership (which of course comes with a higher cost) as I was planning to travel around the country.

What their website had instead was a form on which you could input an address or zip code and obtain a static map that only showed you a few locations.

Searching around the country with this got annoying (especially since I couldn't pan around or zoom in/out of the map) so I created my own page to accomplish what I was looking for.

I then used the map to determine and purchase the membership level most appropriate for my travels.

## What Did I Do?

I wrote a PHP script to scrape the [http://www.24hourfitness.com](http://www.24hourfitness.com) site for the locations and club types of every 24 Hour Fitness location in the country.

I then used [Google's Geocoder API](https://developers.google.com/maps/documentation/geocoding/intro) to get the latitude and longitude of all of their locations' addresses, and overlayed that information on a Google Map.

The webpage is a simple HTML page with JavaScript on board. It utilizes the [jQuery JavaScript library](https://github.com/jquery/jquery) and the [Google Maps AJAX Map Control](https://developers.google.com/maps/documentation/javascript/tutorial).

The data that is displayed on the page is stored in a JSON file, which is the output of the PHP script, and this JSON file is parsed when the page loads.



