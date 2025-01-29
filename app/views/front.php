<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{TITLE}}</title>

		<meta name="language" content="fr">
		<meta name="keywords" content="mot-clé1, mot-clé2, mot-clé3">
		<meta name="author" content="Nom de l'auteur">
		<meta name="description" content="Description de votre page">

		<meta property="og:title" content="Ma page web en 3D">
		<meta property="og:description" content="Description de votre page">
		<meta property="og:image" content="URL_de_votre_image.jpg">
		<meta property="og:url" content="URL_de_votre_page">
		<meta property="og:type" content="website">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="Ma page web en 3D">
		<meta name="twitter:description" content="Description de votre page">
		<meta name="twitter:image" content="/img/assets.png">
		
		<meta name="robots" content="index,follow">

	<link rel="icon" href="/favicon.ico"/>
	<link rel="stylesheet" href="/css/styles.css">
	<link rel="stylesheet" href="/css/nav.css">
	<link rel="stylesheet" href="/css/form.css">
	<link rel="stylesheet" href="/css/console.css">
	{{backgroundCss}}
	<!-- <script src="/js/JsBarcode.all.min.js"></script> -->
	<script src="/js/clock.js" defer></script>
	<script src="/js/interface.js" defer></script>
	<script src="/vendor/feunico/feunico.js"></script>
</head>

<body class="">
	<div id="container" class="container">
		{{CONTENTS}}
	</div>
	{{topNavView}}
	{{console}}
</body>

</html>