<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css"
	href="vendor/components/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="vendor/components/font-awesome/css/font-awesome.min.css" type="text/css" />
<script src='vendor/components/jquery/jquery.min.js' type='text/javascript'></script>
<script src='vendor/components/bootstrap/js/bootstrap.min.js' type='text/javascript'></script>
<script src='includes/main.inc.js' type='text/javascript'></script>

<title><?php echo __TITLE__; ?></title>
</head>

<body style='padding-top: 60px; padding-bottom: 60px;'>
<nav class="navbar fixed-top navbar-dark bg-dark">
        <a class='navbar-brand' href='#'><?php echo __TITLE__ ?></a>
                <span class='navbar-text'>
                Version <?php echo __VERSION__; ?>&nbsp;
                </span>

</nav>


<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-2 col-lg-2 col-xl-2'>
			<div class='sidebar-nav'>
				<ul class='nav flex-column'>
				<li class='nav-item'><a class='nav-link' href='index.php'>Home</a></li>
				<span class="border-top my-2"></span>
				<?php echo $nav_html; ?>
					<span class="border-top my-2"></span>
					<li class='nav-item'><a class='nav-link' href='hardware.php'>Mac Addresses</a></li>
					<li class='nav-item'><a class='nav-link' href='networks.php'>Networks</a></li>
					<li class='nav-item'><a class='nav-link' href='domains.php'>Domains</a></li>
					<span class="border-top my-2"></span>
					<li class='nav-item'><a class='nav-link' href='logout.php'>Logout</a></li>
				</ul>
				
			</div>
		</div>
		<div class='col-md-8 col-lg-8 col-xl-8'>