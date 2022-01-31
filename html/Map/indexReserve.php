<!doctype html>
<html lang="en">


<?php
    session_start();
    if($_SESSION["reserved"] == 2){
        header("location:indexTrip.php");
    }else if($_SESSION["reserved"] == 1){
    }else{
        header("location:index.php");
    }
?>


<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.11.0/css/ol.css" type="text/css">
    <script  src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"> </script>
    <style>
        #map {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        .red
         {
            height: 75px;width: 100px;
            background: green;
            position: relative;z-index: 2;
            border-radius: 15px;

         }
        .green
         {
            height: 75px;width: 100px;
            background: red;
            position: absolute;z-index: 2;
            border-radius: 15px;

         }
        .modal {
          display: none; /* Hidden by default */
          position: fixed; /* Stay in place */
          z-index: 1; /* Sit on top */
          padding-top: 100px; /* Location of the box */
          left: 0;
          top: 0;
          width: 100%; /* Full width */
          height: 100%; /* Full height */
          overflow: auto; /* Enable scroll if needed */
          background-color: rgb(0,0,0); /* Fallback color */
          background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
          background-color: #fefefe;
          margin: auto;
          padding: 20px;
          border: 1px solid #888;
          width: 80%;
        }
        .close3 {
          color: #aaaaaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
        }
        .close3:hover,
        .close3:focus {
          color: #000;
          text-decoration: none;
          cursor: pointer;
        }
        .close2 {
          color: #aaaaaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
        }
        .close2:hover,
        .close2:focus {
          color: #000;
          text-decoration: none;
          cursor: pointer;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.11.0/build/ol.js"></script>
    <title>OpenLayers example</title>
</head>
<body>


<div id="myModal3" class="modal">
  <div class="modal-content">
  <h4 class="modal-title "> Sblocca </h4>
    <span class="close3">&times;</span>
    <p>Inserisci il codice per sbloccare la tua bici</p>
        <input type="number" id="resNumb" min="1" max="9999">
        <button id="btn">Sblocca</button>
  </div>
</div>

<div id="myModal2" class="modal">
  <div class="modal-content">
  <h4 class="modal-title "> Sblocca la tua bici </h4>
    <span class="close2">&times;</span>
    <p>Vuoi eliminare la tua prenotazione?</p>
        <button onclick="deletePrenotation()">Elimina</button>
  </div>
</div>


<?php
    if($_SESSION["error"] == 1){
        echo ("<script> alert('Il codice inserito è errato'); </script>");
    }else if($_SESSION["error"] == 2){
        echo ("<script> alert('Sei troppo lontano dalla bici!'); </script>");
    }
    $_SESSION["error"] = 0;
?>

<div id="map" class="map"></div>

<button class="red" onclick="code()"> Codice prenotazione </button>
<button class="green" onclick="code2()"> Annulla prenotazione </button>
<script type="text/javascript">



//--------------------------------------------------
//MAP DISPLAY

    var x = "";
    var y = "";


    function refreshPosition() {
        navigator.geolocation.getCurrentPosition(userPosition);
    }

    function userPosition(position) {
        x = 44.502220;//position.coords.latitude;
        y = 11.333503;//position.coords.longitude;

            var markers = new ol.layer.Vector({
              source: new ol.source.Vector(),
              style: new ol.style.Style({
                image: new ol.style.Icon({
                  anchor: [0.5, 1],
                  scale: 0.03,
                  src: 'marker.png'
                })
              })
            });
            map.addLayer(markers);

            var marker = new ol.Feature(new ol.geom.Point(ol.proj.fromLonLat([y,x])));
            markers.getSource().addFeature(marker);
    }

    refreshPosition();

//--------------------------------------------------
//MAP DISPLAY


    var bikeStyle2 = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 8,
            fill: new ol.style.Fill({color: 'rgba(200, 0, 0, 1)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });

    var bikePosition = <?php echo json_encode($_SESSION[bikePosition]); ?>;
    console.log(bikePosition);




   var source = new ol.source.Vector();
   var map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            new ol.layer.Vector({
                      title: 'bikesRented',
                      source: source,
                      style: bikeStyle2
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([11.341868,44.494949]),
            zoom: 14
        })
    });
    source.addFeatures(
      new ol.format.GeoJSON().readFeatures(bikePosition, {
        dataProjection: 'EPSG:4326',
        featureProjection: map.getView().getProjection()
      })
    );


//--------------------------------------------------
//BIKES INTERACTION

    var modal3 = document.getElementById("myModal3");
    var span3 = document.getElementsByClassName("close3")[0];
    var modal2 = document.getElementById("myModal2");
    var span2 = document.getElementsByClassName("close2")[0];


    span3.onclick = function() {
      modal3.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal3) {
        modal3.style.display = "none";
      }
    }



    span2.onclick = function() {
      modal2.style.display = "none";
    }


    var featureListener = function (event) {
        console.log("featureListenerCalled");
        alert("Feature Listener Called");
    };



    var bikeId = 0;
    map.on('click', function(event) {
        map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
            if( layer.get('title') == "bikesRented" ) {
                    modal3.style.display = "block";
            }
        });
    });




    var btn = document.getElementById("btn");
    var resNumb = "";
    btn.onclick = function(){
        resNumb = document.getElementById("resNumb").value;
        if(resNumb == ""){
            alert("Inserisci il codice prima di continuare");
        }else{
            navigator.geolocation.getCurrentPosition(showPosition);
        }
    }

    function showPosition(position) {
        window.location.href = "unlock.php?x=" + x + "&y=" + y + "&numb=" + resNumb;
    }



//--------------------------------------------------
//CODICE PRENOTAZIONE E ANNULLAMENTO PRENOTAZIONE

    function code(){
        alert("Il tuo codice prenotazione è: <?php echo $_SESSION['resNumb']; ?>");
    }

    function code2(){
        modal2.style.display = "block";
    }

    function deletePrenotation(){
        window.location.href = "deletePrenotation.php?";
    }




</script>
</body>
</html>