<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
 Copyright 2010 Google Inc. 
 Licensed under the Apache License, Version 2.0: 
 http://www.apache.org/licenses/LICENSE-2.0 
 -->

<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Routeplanner</title>
<link href="style.css" rel="stylesheet">
<script type="text/javascript" src="http://maps.google.nl/maps/api/js?sensor=true&language=nl&libraries=geometry,places,weather"></script>   <!--language=nl toevoegen om de tekst in het Nederlands te krijgen-->
<script type="text/javascript">
  var directionDisplay;
  var directionsService = new google.maps.DirectionsService();
  var map;
  var origin = null;
  var location;
  var destination = null;
  var waypoints = [];
  var marker;
  var markers = [];
  var directionsVisible = false;
  var infowindows = [];




//*********************************************************************************************
  function initialize() {
    directionsDisplay = new google.maps.DirectionsRenderer();
    var myOptions = {
      zoom:13,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
    }
    
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("directionsPanel"));

 //********************************************************************************************

  var input = /** @type {HTMLInputElement} */(document.getElementById('target'));
  var searchBox = new google.maps.places.SearchBox(input);
 
  

  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    for (var i = 0, marker; marker = markers[i]; i++) {
      marker.setMap(null);
    }

    bounds = new google.maps.LatLngBounds();
    for (var i = 0, place; place = places[i]; i++) {
      var image = {
       url: place.icon,
       size: new google.maps.Size(800, 800),
       origin: new google.maps.Point(0, 0),
       anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(40, 40)
        
      };

      marker = new google.maps.Marker({
        map: map,
        icon: image,
        title: place.name,
        position: place.geometry.location,
      });

      markers.push(marker);


  bounds.extend(place.geometry.location);
    }

   map.fitBounds(bounds), map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));

  });

  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  }); 
  
//*********************************************************************************************   


    locatie();                                                                          //roept de functie locatie aan om de locatie te bepalen.

 var trafficLayer = new google.maps.TrafficLayer();
trafficLayer.setMap(map);

 var weatherLayer = new google.maps.weather.WeatherLayer({
    temperatureUnits: google.maps.weather.TemperatureUnit.CELCIUS
  });
  weatherLayer.setMap(map);

  var cloudLayer = new google.maps.weather.CloudLayer();
  cloudLayer.setMap(map);

//*********************************************************************************************
// Met onderstaande code laat je Google Maps wachten tot er op de kaart wordt geklikt om een
// bestemming te bepalen
//*********************************************************************************************

    google.maps.event.addListener(map, 'click', function(event) {
    if (origin == null) {                                                               //Als er geen locatie is opgehaald dan moet de gebruiker dit zelf aangeven met een klik op de kaart
        origin = event.latng;                                                           //De variabele Origin wordt gevuld met de lengte en breedtegraad van de positie waarop geklikt is
        addMarker(origin);                                                              //Er wordt een marker geplaatst op de plek waarop geklikt is (op de coördinaten die in de variavbele Origin zijn opgeslagen)
      } else                                                                            //Zodra er een coördinaat beschikbaar is gaat de code verder
      if (destination == null) {                                                        //Als er nog geen bestemming is geselecteerd moet de gebruiker deze aangeven door op de kaart te klikken. 
        destination = event.latLng;                                                     //Het coördinaat wordt in de variabele destination opgeslagen
        addMarker(destination);
        view();                                                         //Ook hier wordt wederom een marker geplaatst op het coördinaat welke is opgeslagen in de variabele destination
      } else {
        if (waypoints.length < 9) {                                                     //Er zijn tussenstops mogelijk met een maximum van 9
          waypoints.push({ location: destination, stopover: true });                    //De locatie wordt gehaald uit de variabele destination en er wordt een extra waarde toegekend namelijk stopover wat aangeeft dat het een tussenpunt is
          destination = event.latLng;                                                   //hiermee wordt het coördinaat opgehaald
          addMarker(destination); 
          view();                                                      //Er wordt een marker toegevoegd op elk tussenpunt
        } else {
          alert("Maximum number of waypoints reached");                                 //als er meer dan 9 tussenpunten zijn aangegeven dan krijg je een melding
        }
      }
    });





 

}


   

function view(){
  var panoOptions = null;
  panoOptions = {
    position: destination,
    addressControlOptions: {
      position: google.maps.ControlPosition.BOTTOM_CENTER
    },
    linksControl: false,
    panControl: false,
    zoomControlOptions: {
      style: google.maps.ZoomControlStyle.SMALL
    },
    enableCloseButton: false
  };

  var panorama = new google.maps.StreetViewPanorama(
      document.getElementById('street'), panoOptions);



}



 //********************************************************************************************** 
 // Met onderstaande functie wordt de locatie opgehaald
 //********************************************************************************************** 
  function locatie(){


    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          origin = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
        
      infowindows.push(new google.maps.InfoWindow({
            map: map,
            position: origin,
            content: 'Je bent nu hier!'
          }));
        
           map.setCenter(origin);
           }
    , function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  } ;
  
 






  }


//*********************************************************************************************
// Onderstaande functie wordt gebruikt om markers te koppelen aan een locatie en deze in de variabele
// markers te stoppen. met icon: wordt het gebruikte icoon aangegeven
//********************************************************************************************** 

  function addMarker(latlng) {
    markers.push(new google.maps.Marker({
      position: latlng, 
      map: map,
      animation: google.maps.Animation.DROP,
      icon: "http://maps.google.com/mapfiles/marker" + String.fromCharCode(markers.length + 65) + ".png"
    }));    
  }

//*********************************************************************************************
//Onderstaande functie rekent de route uit en geeft deze weer op de kaart. Daarnaast wordt de route uitgeschreven
//********************************************************************************************** 

  function calcRoute() {

    if (origin == null) {
      alert("Click on the map to add a start point");
      return;
    }
    
    if (destination == null) {
      alert("Klik op de kaart om aan te geven waar je naar toe wilt.");
      return;
    }
    
    var mode;                                                                     //Hiermee wordt aangegeven welke opties je wilt gebruiken
    switch (document.getElementById("mode").value) {
      case "Fietsen":
        mode = google.maps.DirectionsTravelMode.BICYCLING;
        break;
      case "Auto":
        mode = google.maps.DirectionsTravelMode.DRIVING;
        break;
      case "Wandelen":
        mode = google.maps.DirectionsTravelMode.WALKING;
        break;
      case "OV":
        mode = google.maps.DirectionsTravelMode.TRANSIT;
        break;
    }
    
   

    var request = {
        origin: origin,
        destination: destination,
        waypoints: waypoints,
        travelMode: mode,
        optimizeWaypoints: document.getElementById('optimize').checked,
        avoidHighways: document.getElementById('highways').checked,
        avoidTolls: document.getElementById('tolls').checked
    };
    
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
      }
    });
    
    clearInfo();
    clearMarkers();
    directionsVisible = true;
  }
  

//*********************************************************************************************

  function updateMode() {
    if (directionsVisible) {
      calcRoute();
    }
  }
  
//*********************************************************************************************

  function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }
  }
  
//*********************************************************************************************

  function clearInfo(){
  for (var u = 0; u < infowindows.length; u++) {
    infowindows[u].setMap(null);
  }

  } 
 
//*********************************************************************************************

  function clearWaypoints() {
    markers = [];
    origin = null;
    destination = null;
    waypoints = [];
    directionsVisible = false;
  }

//*********************************************************************************************
  
  function reset() {
    clearMarkers();
    clearWaypoints();
    directionsDisplay.setMap(null);
    directionsDisplay.setPanel(null);
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("directionsPanel")); 
    locatie();   
  }

//*********************************************************************************************



 google.maps.event.addDomListener(window, 'load', initialize);



</script>
<style>
      #target {
        width: 600px;
      }
      </style>
</head>
<body onload="initialize()" style="font-family: sans-serif size:8">
  <table id="control" style="width: 600px; height: 100px;">
    <tr style="width:600px;" align="center" ><h2>Routeplanner van Wagtmans.net</h2></tr>
    <tr>
      <td><input type="checkbox" id="optimize" checked />Optimaliseer</td>
      <td>
        <select id="mode" onchange="updateMode()">
          <option value="Auto">Auto</option>
          <option value="Fietsen">Fietsen</option>
          <option value="Wandelen">Wandelen</option>
          <option value="OV">Openbaar Vervoer</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><input type="checkbox" id="highways" checked />Vermijd Snelwegen</td>
      <td><input type="button" value="Reset" onclick="reset()" /></td>
    </tr>
    <tr>
      <td><input type="checkbox" id="tolls" checked />Vermijd Tolwegen</td>
      <td><input type="button" value="Toon Route" onclick="calcRoute()" /></td>
    </tr>
  </table>
    <section>
    <h2 id="safety">Remmen vast, daar gaan we!</h2>
    <div id="panel" type="text" style="width:200px; height:40px;"><input id="target" placeholder="Search Box"></div>
    <div id="map_canvas"></div>
    <div id="directionsPanel"> </div>
    <div id="street"></div>
    </section>

</body>
</html>