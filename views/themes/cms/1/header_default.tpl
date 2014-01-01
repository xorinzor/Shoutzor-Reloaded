<!DOCTYPE html>
{$THEMESECTION.BEFOREHTML}
<html lang="en">
	{$THEMESECTION.HTMLBEGIN}

	<head>
		{$THEMESECTION.HEADBEGIN}
		<meta charset="utf-8">
		<title>{$PAGETITLE} | {$SITETITLE}</title>

		<link href="{$SITEURL}static/cms/css/bootstrap/bootstrap.css" rel="stylesheet">

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<style type="text/css">
		body {
			background-color: #F1EFED;
			margin: 0;
			padding: 0;
		}
		
		.message-container {
			position: relative;
			max-width: 700px;
			padding: 19px 29px 29px;
			margin: 8% auto 20px auto;
			background-color: #fff;
			border: 1px solid #DFDEDD;
			-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
			-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
			box-shadow: 0 1px 2px rgba(0,0,0,.05);
		}

		.message-container .message-container-title {
			margin-bottom: 10px;
		}
		</style>

		{$THEMESECTION.HEADEND}
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="{$SITEDESCRIPTION}">
		<meta name="author" content="{$SITEAUTHOR}">
	</head>

	{$THEMESECTION.BODYBEFORE}

	<body class="preview" data-spy="scroll" data-target=".subnav" data-offset="80">
		<div class="container">