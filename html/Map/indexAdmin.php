<!doctype html>
<html lang="en">

<?php
    session_start();
    //echo ($_SESSION['reserved']);
    if($_SESSION["reserved"] == 1){
        header("location:indexReserve.php");
    }else if($_SESSION["reserved"] == 2){
        header("location:indexTrip.php");
    }
    include "connPGSQL.php";

?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.11.0/css/ol.css" type="text/css">

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
            background: blue;
            position: relative;z-index: 2;
            border-radius: 15px;
         }
        .green
         {
            height: 75px;width: 100px;
            background: green;
            position: relative;z-index: 2;
            border-radius: 15px;
         }
        .blue
         {
            height: 75px;width: 100px;
            background: yellow;
            position: relative;z-index: 2;
            border-radius: 15px;
         }
        .dist
         {
            height: 75px;width: 100px;
            background: red;
            position: absolute;z-index: 2;
            border-radius: 15px;
         }
        .dist2
         {
            bottom:0;
            left:0;
            height: 75px;width: 100px;
            background: yellow;
            position: absolute;z-index: 2;
            border-radius: 15px;
         }
        .dist3
         {
            bottom:0;
            right:0;
            height: 75px;width: 100px;
            background: green;
            position: absolute;z-index: 2;
            border-radius: 15px;
         }
        .normal
         {
            top:0;
            right:0;
            height: 75px;width: 100px;
            background: purple;
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
        label {
          display:block;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.11.0/build/ol.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <title>OpenLayers example</title>
</head>
<body>



<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div>
        <p> Percorsi precedenti: </p>
        <button onclick="paths()">Controlla</button>
        <p id="paths"></p>
    </div>
  </div>

</div>

<div id="myModal2" class="modal">
  <div class="modal-content">
    <span class="close2">&times;</span>
    <p>Ci sono più bici in questa rastrelliera, selezionare prima il codice identificativo per controllare i percorsi:</p>
        <form method="POST" id="result" action="prenotation.php">
            <input type="submit" value="username" name="username">
        </form>
        <div>
            <p> Percorsi precedenti: </p>
            <button onclick="paths2()">Controlla</button>
            <p id="paths2"></p>
        </div>
    </div>

</div>

<div id="myModal3" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Selezionare la distanza massima entro la quale un utente può parcheggiare da una rastrelliera (N.B. La distanza è espressa in metri):</p>
        <input type="number" id="resNumb" min="1" max="9999">
        <button id="btn">Seleziona</button>
    </div>

</div>

<div id="myModal4" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Inserire un file relativo alle rastrelliere che si vogliono inserire:</p>
        <form class="upload" action="insertToMap/insertPark.php" enctype="multipart/form-data" method="POST">
           <input type="file" name="file" id="file" accept=".json" required />
           <br/><br/>
           <input type="submit" />
        </form>
    </div>

</div>

<div id="myModal5" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Inserire un file relativo alle rastrelliere che si vogliono inserire:</p>
        <form class="upload" action="insertToMap/insertFence.php" enctype="multipart/form-data" method="POST">
           <input type="file" name="file" id="file" accept=".json" required />
           <br/>
           <input type="radio" name="typeOf" value="1" id="typeOf0" required>
           <label for="html">Aree vietate</label><br>
           <input type="radio" name="typeOf" value="2" id="typeOf">
           <label for="html">Punti di interesse</label><br>
           <br/><br/>
           <input type="submit" />
        </form>
    </div>

</div>


<div id="myModal6" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Vuoi inserire una rastrelliera qui?</p>
        <button onclick="yes()"> Si </button>
    </div>

</div>


<div id="myModal7" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Cosa vuoi inserire con un click?</p>
           <input type="radio" class="mode" name="mode" value="1" id="mode" required>
           <label for="html">Rastrelliere</label><br>
           <input type="radio" class="mode" name="mode" value="2" id="mode" required>
           <label for="html">Aree vietate</label><br>
           <input type="radio" class="mode" name="mode" value="3" id="mode">
           <label for="html">Punti di interesse</label><br>
           <br/><br/>
           <button onclick="changeMode()"> Seleziona </button>
    </div>

</div>

<div id="myModal8" class="modal">
  <div class="modal-content">
    <span class="close3">&times;</span>
    <p>Registro accessi:</p>
    <p id="access"></p>
    <button onclick="access()">Controlla</button>
    </div>
</div>



<div id="map" class="map"></div>

<button class="red" onclick="insertPark()"> Inserisci restrelliere </button>
<button class="green" onclick="insertGeoFence()"> Inserisci Geofence </button>
<button class="dist" onclick="maxDistance()"> Distanza massima rastrelliere </button>
<button class="dist2" onclick="selectMode()"> Inserisci con click </button>
<button id="isDrawing" class="dist3" onclick="draw()"> OK </button>
<button class="normal" onclick="normal()"> Torna alla sessione normale </button>






<script type="text/javascript">

//--------------------------------------------------
//MAP DISPLAY

    var bikeStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 15,
            fill: new ol.style.Fill({color: 'rgba(120, 255, 12, 1)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });
    var bikeStyle2 = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 15,
            fill: new ol.style.Fill({color: 'rgba(250, 60, 20, 1)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });
    var bikeStyle3 = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 15,
            fill: new ol.style.Fill({color: 'rgba(240, 60, 240, 1)'}),
            stroke: new ol.style.Stroke({color: 'green', width: 1})
        })
    });
    var parkStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 18,
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





    var bikes = "";
    var reservedBikes = "";
    var tripBikes = "";

    var map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            new ol.layer.Vector({
                      title: 'POI',
                      source: new ol.source.Vector({
                         url: 'Results/POI.json',
                         format: new ol.format.GeoJSON()
                      }),
                      style: poStyle
            }),
            new ol.layer.Vector({
                      title: 'noAccess',
                      source: new ol.source.Vector({
                         url: 'Results/NOAREA.json',
                         format: new ol.format.GeoJSON()
                      }),
                      style: noStyle
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
            center: ol.proj.fromLonLat([11.341868,44.494949]),
            zoom: 14
        })
    });






//CHECK BIKES POSITION


    var intervalId = window.setInterval(function(){
      newPositions();
    }, 2000);

    function newPositions(){
        $(document).ready(function(){
                    $.ajax({
                          type: "POST",
                          url: "bikesPosition.php",
                          success: function(msg){
                                bikes = msg;
                                //console.log(bikes);

                                var source = new ol.source.Vector();
                                var bike = new ol.layer.Vector({
                                    title: 'bikes',
                                    source: source,
                                    style: bikeStyle,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(bikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );

                                bikeID = [];
                                map.removeLayer(bike);

                                source = new ol.source.Vector();
                                bike = new ol.layer.Vector({
                                    title: 'bikes',
                                    source: source,
                                    style: bikeStyle,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(bikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );
                                map.addLayer(bike);
                          }
                    })
       });
        $(document).ready(function(){
                    $.ajax({
                          type: "POST",
                          url: "reservedPosition.php",
                          success: function(msg){
                              reservedBikes = msg;
                              //console.log(reservedBikes);
                                var source = new ol.source.Vector();
                                var reservedBike = new ol.layer.Vector({
                                    title: 'reservedBikes',
                                    source: source,
                                    style: bikeStyle2,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(reservedBikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );


                                map.removeLayer(reservedBike);


                                source = new ol.source.Vector();
                                reservedBike = new ol.layer.Vector({
                                    title: 'tripBikes',
                                    source: source,
                                    style: bikeStyle2,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(reservedBikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );

                                map.addLayer(reservedBike);

                          }
                    })
       });
        $(document).ready(function(){
                    $.ajax({
                          type: "POST",
                          url: "trippingPosition.php",
                          success: function(msg){
                              tripBikes = msg;
                              //console.log(tripBikes);
                                var source = new ol.source.Vector();
                                var tripBike = new ol.layer.Vector({
                                    title: 'tripBikes',
                                    source: source,
                                    style: bikeStyle,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(tripBikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );

                                map.removeLayer(tripBike);

                                var source = new ol.source.Vector();
                                var tripBike = new ol.layer.Vector({
                                    title: 'tripBikes',
                                    source: source,
                                    style: bikeStyle,
                                })

                                source.addFeatures(
                                  new ol.format.GeoJSON().readFeatures(tripBikes, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: map.getView().getProjection()
                                  })
                                );

                                map.addLayer(tripBike);
                          }
                    })
       });


    }



//--------------------------------------------------
//BIKES INTERACTION

    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];

    var modal2 = document.getElementById("myModal2");
    var span2 = document.getElementsByClassName("close2")[0];

    var modal3 = document.getElementById("myModal3");
    var span3 = document.getElementsByClassName("close3")[0];

    var modal4 = document.getElementById("myModal4");

    var modal5 = document.getElementById("myModal5");
    var modal6 = document.getElementById("myModal6");

    var modal7 = document.getElementById("myModal7");
    var modal8 = document.getElementById("myModal8");





    span.onclick = function() {
      modal.style.display = "none";
      document.getElementById('paths').innerHTML = '';
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
        document.getElementById('paths').innerHTML = '';
      }
    }




    span2.onclick = function() {
      modal2.style.display = "none";
      document.getElementById('paths2').innerHTML = '';
      bikeID = [];
    }

    window.onclick = function(event) {
      if (event.target == modal2) {
        modal2.style.display = "none";
        document.getElementById('paths2').innerHTML = '';
        bikeID = [];
      }
    }




    span3.onclick = function() {
      modal7.style.display = "none";
      bikeID = [];
    }





    window.onclick = function(event) {
      if (event.target == modal3) {
        modal3.style.display = "none";
      }
    }

    window.onclick = function(event) {
      if (event.target == modal7) {
        modal7.style.display = "none";
        document.getElementById('paths').innerHTML = '';
      }
    }


    var featureListener = function (event) {
        console.log("featureListenerCalled");
        alert("Feature Listener Called");
    };

    var enumerate = 0;
    var bikeId = 0;
    let bikeID = [];
    var accessZone = "";
    map.on('click', function(event) {
        map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
            if ( layer.get('title') == "bikes" ) {
                    enumerate = enumerate + 1;
                    bikeId = feature.get('id');
            }
            else if((layer.get('title') == "POI") && (enumerate == 0)){
                modal8.style.display = "block";
                accessZone = feature.get('name');
                console.log(accessZone);
            }
            else if((layer.get('title') == "noAccess") && (enumerate == 0)){
                modal8.style.display = "block";
                accessZone = feature.get('name');
                console.log(accessZone);
            }
        });
        if(enumerate >= 2){
            map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
                if ( layer.get('title') == "bikes" ) {
                        console.log(feature.get('id'));
                        bikeId = feature.get('id');
                        bikeID.push(bikeId);
                }
            });
            console.log(bikeID);
            showSuggestions();
            modal2.style.display = "block";
        }else if(enumerate ==1){
            modal.style.display = "block";
        }
        enumerate = 0;
    });

    function showSuggestions() {

      var result = $('#result');
      result.html('');

      for (var i = 0; i < bikeID.length; i++) {
        result.append('<label id="options"><input type="radio" name="username" value="' + bikeID[i] + '" required/> ' + bikeID[i] + '</label> ');
      }
    }







//-----------------------------------------------------------------------------------------------------------
//SEE PREVIOUS PATHS


    function paths(){
        $(document).ready(function(){
                    $.ajax({
                          type: "GET",
                          url: "checkPaths.php?bikeid=" + bikeId,
                          success: function(msg){
                               //console.log(msg);
                               document.getElementById("paths").innerHTML = msg
                          }
                    })
       });
    }


    function paths2(){

       var username = "";
       if ($('input[name=username]:checked').length > 0) {
            username = document.querySelector('input[name="username"]:checked').value;
       }
       console.log(username);
       $(document).ready(function(){
                    $.ajax({
                          type: "GET",
                          url: "checkPaths.php?bikeid=" + username,
                          success: function(msg){
                               //console.log(msg);
                               document.getElementById("paths2").innerHTML = msg
                          }
                    })
       });
    }

//MAX DISTANCE FROM PARKS


    function maxDistance(){
        modal3.style.display = "block";
    }

    var btn = document.getElementById("btn");
    var max= "";
    btn.onclick = function(){
        max = document.getElementById("resNumb").value;
        if(max == ""){
            alert("Inserisci il valore prima di continuare");
        }else{
            window.location.href = "maxDistancePark.php?maxdistance=" + max;
        }
    }




//RETURN TO NORMAL SESSION

    function normal(){
        window.location.href = "checkIfAdmin.php";
    }


//INSERT FROM JSON

    function insertPark(){
        modal4.style.display = "block";

    }

    function insertGeoFence(){
        modal5.style.display = "block";
    }

//INSERT DYNAMICALLY

    var prova = 0;
    if((prova == 2)){
        document.getElementById("isDrawing").style.display = "block";
    }else{
        document.getElementById("isDrawing").style.display = "none";
    }

    function selectMode(){
        modal7.style.display = "block";
    }

    function changeMode(){
        prova = document.querySelector('.mode:checked').value;
        console.log(prova);
        if((prova == 2) || (prova == 3)){
            alert("Per disegnare il poligono clicca sulla mappa! Ogni click corrisponde a un vertice della figura!")
            document.getElementById("isDrawing").style.display = "block";
        }else{
            document.getElementById("isDrawing").style.display = "none";
        }
    }

    var x = 0;
    var y = 0;
    var vertices = [];
    var onlyFirst = "";
    var controlClick = 0;

    map.on('click', function(event) {
        if(prova == 1){
            var isFull = 0;
            var geometry = event.pixel;
            console.log(geometry);
            var coordinate = ol.proj.transform(map.getCoordinateFromPixel(event.pixel), 'EPSG:3857', 'EPSG:4326');
            console.log(coordinate);
            map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
                console.log("Hello");
                isFull = 1;
            });
            console.log(isFull);
            if(isFull == 0){
                x = coordinate[0];
                y = coordinate[1];
                modal6.style.display = "block";
            }
            isFull = 0;
        }else if((prova == 2) || (prova == 3)){
            var geometry = event.pixel;
            var coordinate = ol.proj.transform(map.getCoordinateFromPixel(event.pixel), 'EPSG:3857', 'EPSG:4326');
            var modified = coordinate.join(" ");
            vertices.push(modified);
            controlClick = controlClick+1;
            console.log(controlClick);
        }
        console.log(vertices);
    });

    function yes(){
        window.location.href = "insertToMap/insertParkOnclick.php?x=" + x + "&y=" + y;
    }

    function draw(){
        onlyFirst = vertices[0];
        vertices.push(onlyFirst);
        if(controlClick >= 3){
            window.location.href = "insertToMap/drawFence.php?polygon=" + vertices + "&prova=" + prova;
        }else{alert("Clicca ancora");}
    }

//GET ACCESS

    function access(){
        $(document).ready(function(){
                    $.ajax({
                          type: "GET",
                          url: "returnAccess.php?name=" + accessZone,
                          success: function(msg){
                               console.log(msg);
                               document.getElementById("access").innerHTML = msg
                          }
                    })
       });
    }



</script>
</body>
</html>