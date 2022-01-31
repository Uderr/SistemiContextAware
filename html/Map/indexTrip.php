<!doctype html>
<html lang="en">

<?php
    session_start();
    if($_SESSION["reserved"] == 1){
        header("location:indexReserve.php");
    }else if($_SESSION["reserved"] == 2){
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
            background: red;
            position: relative;z-index: 2;
            border-radius: 15px;
         }
        .mode
         {
            height: 75px;width: 100px;
            background: white;
            position: relative;z-index: 2;
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
        .close {
          color: #aaaaaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
        }
        .close:hover,
        .close:focus {
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

    <?php
        if($_SESSION["error"] == 5){
            echo ("<script> alert('Sei troppo distante dalla rastrelliera!'); </script>");
        }
        $_SESSION["error"] = 0;
    ?>



<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>Per terminare il servizio clicca sul parcheggio in cui si vuole lasciare la bici</p>
  </div>

</div>


<div id="myModal2" class="modal">
  <div class="modal-content">
    <span class="close2">&times;</span>
    <p>Terminare?</p>
    <button onclick="park()"> Si </button>
  </div>

</div>




<button class="red" onclick="code()"> Termina servizio </button>
<button class="mode"> In bicicletta </button>
<button class="mode"> Cammina </button>


<div id="map" class="map"></div>


<script type="text/javascript">

//--------------------------------------------------
//MAP DISPLAY

    var bikeStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 8,
            fill: new ol.style.Fill({color: 'rgba(240, 60, 240, 1)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });
    var parkStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 12,
            fill: new ol.style.Fill({color: 'rgba(0, 0, 200, 0.7)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });

    var noStyle = new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'rgba(35,35,35,1)',
            lineDash: null,
            lineCap: 'butt',
            lineJoin: 'miter',
            width: 0}),
        fill: new ol.style.Fill({
            color:'rgba(255,6,10,0.8)'
        }),
    })

    var poStyle = new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'rgba(35,35,35,1)',
            lineDash: null,
            lineCap: 'butt',
            lineJoin: 'miter',
            width: 0}),
        fill: new ol.style.Fill({
            color:'rgba(255,255,0,0.7)'
        }),
    })

    var tripPosition = <?php echo (json_encode($_SESSION[tripBike])); ?>;
    console.log(tripPosition);



    var x = "<?php echo ($_SESSION[bikeX]); ?>";
    var y = "<?php echo ($_SESSION[bikeY]); ?>";

    console.log(x);
    console.log(y);

    var map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            new ol.layer.Vector({
                      title: 'parks',
                          source: new ol.source.Vector({
                          url: 'Results/park.json',
                          format: new ol.format.GeoJSON()
                      }),
                      style: parkStyle
                })
        ],

        view: new ol.View({
            center: ol.proj.fromLonLat([y,x]),
            zoom: 18
        })
    });

    var source = new ol.source.Vector();
    var bikes = new ol.layer.Vector({
        title: 'bikes',
        source: source,
        style: bikeStyle,
    })

    source.addFeatures(
      new ol.format.GeoJSON().readFeatures(tripPosition, {
        dataProjection: 'EPSG:4326',
        featureProjection: map.getView().getProjection()
      })
    );

    var noArea = new ol.layer.Vector({
         title: 'noAccess',
         source: new ol.source.Vector({
             url: 'Results/NOAREA.json',
             format: new ol.format.GeoJSON()
         }),
         style: noStyle
    })

    var POIArea = new ol.layer.Vector({
         title: 'POIAccess',
         source: new ol.source.Vector({
             url: 'Results/POI.json',
             format: new ol.format.GeoJSON()
         }),
         style: poStyle
    })


    map.addLayer(POIArea);
    map.addLayer(noArea);
    map.addLayer(bikes);



//--------------------------------------------------
//BIKES INTERACTION

    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];

    var modal2 = document.getElementById("myModal2");
    var span2 = document.getElementsByClassName("close2")[0];

    var modal3 = document.getElementById("myModal3");

    span.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    span2.onclick = function() {
      modal2.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal2) {
        modal2.style.display = "none";
      }
    }



    var featureListener = function (event) {
        console.log("featureListenerCalled");
        alert("Feature Listener Called");
    };

//--------------------------------------------------
//STOP TRIP

    var x = "";
    var y = "";
    var parkID = "";

    function code(){
        modal.style.display = "block";
    }

    map.on('click', function(event) {
        map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
            if ( layer.get('title') == "parks" ) {
                    parkID = feature.get('id');
                    console.log(parkID);
                    modal2.style.display = "block";
            }
        });
    });

    function park(){
        var randomOffset =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset2 =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset3 =  Math.floor((Math.random())*(15-1))+1;

        var perturbedX = x + randomOffset;
        var perturbedY = y + randomOffset;

        var perturbedX2 = x + randomOffset2;
        var perturbedY2 = y + randomOffset2;

        var perturbedX3 = x + randomOffset3;
        var perturbedY3 = y + randomOffset3;

        window.location.href = "park.php?parkID=" + parkID +"&positionX=" + perturbedX + "&positionY=" + perturbedY + "&offset=" + randomOffset +
         "&positionX3=" + perturbedX2 + "&positionY2=" + perturbedY2 + "&offset2=" + randomOffset2 +
         "&positionX2=" + perturbedX3 + "&positionY3=" + perturbedY3 + "&offset3=" + randomOffset3;
    }

//--------------------------------------------------
//AGGIORNA POSIZIONE OGNI TOT E VERIFICA NOAREA E POI / CHECK WALKING


    var positon1;
    var position2;

    var isInside = "";
    var coords = "";
    var startTime, endTime, startTimeTotal;
    var firstPointTime, endPointTime, totalTime;
    var mult = 0.5;
    var isBike = 0;

    var alreadyHere = 0;
    var alreadyHerePOI = 0;

    $(function(){
        setInterval(refreshPosition, 4000);
    });

    function refreshPosition() {
        startTime = new Date();
        firstPointTime = new Date();
        navigator.geolocation.getCurrentPosition(showPosition);
    }

    function showPosition(position) {
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1; //months from 1-12
        var day = dateObj.getUTCDate();
        var year = dateObj.getUTCFullYear();


        var newdate = year + "/" + month + "/" + day;

        x = 44.50224236608374//position.coords.latitude + mult;
        y = 11.333551708418238//position.coords.longitude + mult;
        position1 = [x,y];


        mult = mult+0.25;

        //x = position.coords.latitude + mult;
        //y = position.coords.longitude + mult;



        bikes.getSource().getFeatures()[0].getGeometry().setCoordinates(ol.proj.transform([y,x], 'EPSG:4326', 'EPSG:3857'));

        position2 = [x,y];

        endPointTime = new Date();
        totalTime = endPointTime - firstPointTime;
        totalTime /= 1000;
        totalTime += 4;
        console.log("TEMPO SPOSTAMENTO:");
        console.log(totalTime);


        //STARTING
        var randomOffset =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset2 =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset3 =  Math.floor((Math.random())*(15-1))+1;

        var perturbedX = position1[0] + randomOffset;
        var perturbedY = position1[1] + randomOffset;

        var perturbedX2 = position1[0] + randomOffset2;
        var perturbedY2 = position1[1] + randomOffset2;

        var perturbedX3 = position1[0] + randomOffset3;
        var perturbedY3 = position1[1] + randomOffset3;

        //ARRIVAL
        var randomOffsetArr =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset2Arr =  Math.floor((Math.random())*(15-1))+1;
        var randomOffset3Arr =  Math.floor((Math.random())*(15-1))+1;

        var perturbedXArr = position2[0] + randomOffsetArr;
        var perturbedYArr = position2[1] + randomOffsetArr;

        var perturbedX2Arr = position2[0] + randomOffset2Arr;
        var perturbedY2Arr = position2[1] + randomOffset2Arr;

        var perturbedX3Arr = position2[0] + randomOffset3Arr;
        var perturbedY3Arr = position2[1] + randomOffset3Arr;

            $(document).ready(function(){
                $.ajax({
                      type: "GET",
                      url: "checkIfWalking.php?PpositionX=" + perturbedX + "&PpositionY=" + perturbedY + "&Ooffset=" + randomOffset +
                                 "&positionX3=" + perturbedX2 + "&positionY2=" + perturbedY2 + "&offset2=" + randomOffset2 +
                                 "&positionX2=" + perturbedX3 + "&positionY3=" + perturbedY3 + "&offset3=" + randomOffset3 +
                                 "&positionX1Arr=" + perturbedXArr + "&positionY1Arr=" + perturbedYArr + "&offsetArr=" + randomOffsetArr +
                                 "&positionX3Arr=" + perturbedX2Arr + "&positionY2Arr=" + perturbedY2Arr + "&offset2Arr=" + randomOffset2Arr +
                                 "&positionX2Arr=" + perturbedX3 + "&positionY3Arr=" + perturbedY3 + "&offset3Arr=" + randomOffset3Arr + "&time=" + totalTime,
                      success: function(msg){
                        isBike = msg;
                      }
                })
            });


        console.log("Is bike=");
        console.log(isBike);



        bikes.getSource().getFeatures()[0].getGeometry().setCoordinates(ol.proj.transform([y,x], 'EPSG:4326', 'EPSG:3857'));
        coords = bikes.getSource().getFeatures()[0].getGeometry().getCoordinates();
        isInside = noArea.getSource().getFeaturesAtCoordinate(coords);
        isInsidePOI = POIArea.getSource().getFeaturesAtCoordinate(coords);


        if(isInside.length > 0){

            if(alreadyHere == 0){
                checkAccess = isInside[0].get("name");
                $(document).ready(function(){
                    $.ajax({
                          type: "GET",
                          url: "checkAccess.php?geom=" + checkAccess + "&date=" + newdate,
                          success: function(msg){
                          }
                    })
                });
            }

            endTime = new Date();
            var timeDiff = endTime - startTime;
            timeDiff /= 1000;
            timeDiff += 4;
            console.log("Tempo accesso");
            console.log(timeDiff);

            console.log("arriva");
            if(isBike == 1){
                alert("Esci dalla zona vietata o scendi dalla bicicletta!");
            }
            alreadyHere = 1;
        }else{
            alreadyHere = 0;
        }


        if(isInsidePOI.length > 0){
            if(alreadyHerePOI == 0){
                checkAccessPOI = isInsidePOI[0].get("name");
                $(document).ready(function(){
                    $.ajax({
                          type: "GET",
                          url: "checkAccess.php?geomPOI=" + checkAccess + "&date=" + newdate,
                          success: function(msg){
                            console.log(msg);
                          }
                    })
                });
            alreadyHerePOI = 1;
            }else{
                alreadyHerePOI = 0;
            }
        }
    }






</script>
</body>
</html>