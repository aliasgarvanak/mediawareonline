<html>
<head>
<title>Dashboard | Mediaware</title>
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
        width: 45%;
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
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <form name="search" id="search" method="get" action="<?php echo base_url('dashboard/search'); ?>">
                <label for="state">State</label>
                <select class="form-control state" name="state[]" id="state" multiple>
                <option>Please Select State</option>
                <?php foreach($states as $state) { ?>
                <option value="<?php echo $state->id; ?>"><?php echo $state->name; ?></option>
                <?php } ?>
                </select> 
                
                <label for="state">District</label>
                <select class="form-control district" name="district[]" multiple id="district">
                <option>Please Select District</option>
                </select> 

                <label for="state">City</label>
                <select class="form-control city" name="city[]" id="city" multiple>
                <option>Please Select City</option>
                </select> <br/>
                <input type="submit" name="submit" value="Search" class="btn btn-default">
                </form>
            </div>
        </div>
    </div>

	<div id="map"></div>
    <div id="pano"></div>
    <script>

      function initialize() {
        var fenway = {lat: 20.5937, lng: 78.9629};
        var map = new google.maps.Map(document.getElementById('map'), {
          center: fenway,
          zoom: 4
        });
        var panorama = new google.maps.StreetViewPanorama(
            document.getElementById('pano'), {
              position: fenway,
              pov: {
                heading: 34,
                pitch: 10
              }
            });
        map.setStreetView(panorama);
      }
    </script>
   <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmGptseMG6ldG2_Qu_TuazaeDyX5h4JRA&callback=initialize"></script>
   <script type="text/javascript">
$(document).ready(function(){
    /* Populate data to city dropdown */
    $('#state').on('change',function(){
      state = $('#state').val();
      //console.log($('#state').val());
        if(state){
            $.ajax({
                type:'POST',
                url:'<?php echo base_url('dashboard/getDistrict'); ?>',
                data: {state:state},
                success:function(data){
                    $('#district').html('<option value="">Select District</option>'); 
                    var dataObj = jQuery.parseJSON(data);
                    if(dataObj){
                        $(dataObj).each(function(){
                            var option = $('<option />');
                            option.attr('value', this.id).text(this.name);           
                            $('#district').append(option);
                        });
                    }else{
                        $('#district').html('<option value="">District not available</option>');
                    }
                }
            }); 
        }else{
            $('#district').html('<option value="">Select district first</option>'); 
        }
    });

    /* Populate data to city dropdown */
    $('#district').on('change',function(){
      district = $('#district').val();
        if(district){
            $.ajax({
                type:'POST',
                url:'<?php echo base_url('dashboard/getCity'); ?>',
                data: {district:district},
                success:function(data){
                    $('#city').html('<option value="">Select City</option>'); 
                    var dataObj = jQuery.parseJSON(data);
                    if(dataObj){
                        $(dataObj).each(function(){
                            var option = $('<option />');
                            option.attr('value', this.id).text(this.name);           
                            $('#city').append(option);
                        });
                    }else{
                        $('#city').html('<option value="">City not available</option>');
                    }
                }
            }); 
        }else{
            $('#state').html('<option value="">Select state first</option>');
            $('#district').html('<option value="">Select district first</option>'); 
        }
    });

});
</script>
</body>
</html>