<html>
<head>
<title>City Map | Mediaware</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- All the files that are required -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map, #pano {
        float: left;
        height: 100%;
        width: 100%;
      }
    </style>

</head>
<!-- Where all the magic happens -->
<body>
<!-- LOGIN FORM -->
<div class="text-center" style="padding:50px 0">
	 <!-- END BREADCRUMBS -->
	 <?php if($message_notification = $this->session->flashdata('message_notification')) { ?>
                    <!-- Message Notification Start -->
                    <div id="message_notification">
                    <div class="alert alert-<?= $this->session->flashdata('class'); ?>">    
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        <strong>
                            <?= $this->session->flashdata('message_notification'); ?> 
                        </strong>
                    </div>
                    </div>
                    <!-- Message Notification End -->
                    <?php } ?>
                    <a class="btn btn-danger" href="<?= base_url('login/logout'); ?>">Logout</a>

	<div id="map"></div>
    <script>

      function initialize() {
        var locations = [
        <?php foreach($cityDetail as $v) { ?>
        ['<?php echo $v->description ?>', <?php echo $v->longitude; ?>, <?php echo $v->latitude; ?>, <?php echo $v->id; ?>],
      <?php } ?>
        ];

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 8,
      center: new google.maps.LatLng(<?php echo $cityDetail[0]->longitude; ?>, <?php echo $cityDetail[0]->latitude; ?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) { 
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
      }
    </script>
   <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmGptseMG6ldG2_Qu_TuazaeDyX5h4JRA&callback=initialize"></script>
</script>
</body>
</html>