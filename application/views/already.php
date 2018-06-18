<html>
<head>
<title>Already LoggedIn | Mediaware</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- All the files that are required -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />


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
	
</body>
</html>