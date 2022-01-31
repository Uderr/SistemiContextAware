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
            background: green;
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
    <p>Vuoi prenotare questa bici?</p>
        <button id="rent"> Prenota </button>
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
    <p>Ci sono più bici in questa rastrelliera, quale vuoi prenotare? Selezionare il codice identificativo</p>
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
  <h4 class="modal-title "> SEI ADMIN? </h4>
    <span class="close3">&times;</span>
    <p>Inserisci il codice di verifica</p>
        <input type="number" id="resNumb" min="1" max="9999">
        <button id="ver">Verifica</button>
  </div>
</div>


<button class="red" onclick="isAdmin()"> Admin </button>


<div id="map" class="map"></div>


<script type="text/javascript">

//--------------------------------------------------
//MAP DISPLAY

    var bikeStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 15,
            fill: new ol.style.Fill({color: 'rgba(0, 200, 0, 1)'}),
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
            }),
            new ol.layer.Vector({
                      title: 'bikes',
                      source: new ol.source.Vector({
                         url: 'Results/bikes.json',
                         format: new ol.format.GeoJSON()
                      }),
                      style: bikeStyle
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([11.341868,44.494949]),
            zoom: 14
        })
    });



//--------------------------------------------------
//BIKES INTERACTION

    var modal = document.getElementById("myModal");
    var modal2 = document.getElementById("myModal2");
    var span = document.getElementsByClassName("close")[0];
    var span2 = document.getElementsByClassName("close2")[0];
    var modal3 = document.getElementById("myModal3");
    var span3 = document.getElementsByClassName("close3")[0];

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



    var featureListener = function (event) {
        console.log("featureListenerCalled");
        alert("Feature Listener Called");
    };

    var enumerate = 0;
    var bikeId = 0;
    let bikeID = [];
    map.on('click', function(event) {
        map.forEachFeatureAtPixel(event.pixel, function(feature,layer) {
            if ( layer.get('title') == "bikes" ) {
                    enumerate = enumerate + 1;
                    bikeId = feature.get('id');
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
        result.append('<label><input type="radio" name="username" value="' + bikeID[i] + '" required/> ' + bikeID[i] + '</label> ');
      }
      result.append('<input type="submit" value="Prenota" name="user">');
    }


    function go(){
        window.location.href = "prenotation.php?username=username";
    }



    var randomNumb = 0;
    var btn = document.getElementById("rent");
    btn.onclick = function(){
        window.location.href = "prenotation.php?bikeid=" + bikeId;
    }


//IS ADMIN?

    function isAdmin(){
        modal3.style.display = "block";
    }


    var ver = document.getElementById("ver");
    var isAdminNum = "";
    ver.onclick = function(){
        isAdminNum = document.getElementById("resNumb").value;
        if(isAdminNum == ""){
            alert("Inserisci il codice prima di continuare");
        }else{
            window.location.href = "checkIfAdmin.php?admincode=" + isAdminNum;
        }
    }

</script>
</body>
</html>