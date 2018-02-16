<!-- LUMEN SPECIFIC -->
<style>
	@import url('https://fonts.googleapis.com/css?family=Libre+Franklin|Work+Sans:400,700');

	.catalog-redirect-overlay {
		font-family: 'Libre Franklin', sans-serif;
		background-color: white;
		height: 100vh;
		line-height: 1.5;
		position: absolute;
		width: 100vw;
		z-index: 9999;
	}
	.catalog-redirect-header {
		height: 86px;
		width: 100%;
		background-color: #2576CE;
		display: flex;
		align-items: center;
	}
	.catalog-redirect-lumen-logo {
		margin-left: 15%;
	}
	.catalog-redirect-lumen-logo img {
		height: 44.984px;
	}
	.catalog-redirect-body-container {
		display: flex;
		margin-top: 86px;
		text-align: center;
		flex-direction: column;
	}
	.catalog-redirect-body-container h1 {
		font-family: 'Work Sans', sans-serif;
		font-size: 28px;
		font-weight: bold;
		text-transform: uppercase;
	}
	.catalog-confetti img {
		height: 144px;
	}
	.catalog-instructions {
		font-size: 18px;
		margin-top: 7px;
	}
	.catalog-redirect-visit-wrapper {
		margin: 20px 0;
	}
	#catalog-redirect-visit {
		background-color: #2576CE;
		color: white;
		border-radius: 4px;
		padding: 10px 15px;
	}
	#catalog-redirect-visit:hover {
		cursor: pointer;
		background-color: #064b96;
	}
	.catalog-view-other-community {
		color: #747474;
		font-size: 12px;
		line-height: 2;
		margin-top: 25px;
	}
	.catalog-view-other-community a {
		text-decoration: underline;
	}
</style>
<script type="text/javascript">
	var count = 15;
	var redirect = "https://lumenlearning.com/courses/";

	function countdown() {
		var timer = document.getElementById("timer");

		if (count > 0) {
			count--;
			timer.innerHTML = "This page will automatically redirect you to the Course Catalog in " + count + " seconds."
			setTimeout("countdown()", 1000);
		} else {
			window.location.href = redirect;
		}
	}
</script>
<!-- END LUMEN SPECIFIC -->

<!-- LUMEN SPECIFIC -->
<div class="catalog-redirect-overlay">
	<div class="catalog-redirect-header">
		<div class="catalog-redirect-lumen-logo">
			<a href="https://lumenlearning.com">
				<img src="<?php echo CU_PLUGIN_URL . 'assets/images/lumen-logo-med.png'; ?>" alt="Lumen logo" />
			</a>
		</div>
	</div>
	<div class="catalog-redirect-body-container">
		<h1>Lumen's Course Catalog has Moved!</h1>
		<div class="catalog-confetti">
			<img src="<?php echo CU_PLUGIN_URL . 'assets/images/catalog-confetti.png'; ?>" alt="New Lumen Catalog image" />
		</div>
		<div class="catalog-instructions">
			<p>Find recommended OER courses</p>
			<p>supported by Lumen Learning.</p>
		</div>
		<div class="catalog-redirect-visit-wrapper">
			<a href="https://lumenlearning.com/courses/"><button id="catalog-redirect-visit">Visit Lumen's Course Catalog</button></a>
		</div>
		<div class="catalog-view-other-community">
			<p>View other <a href="https://lumenlearning.com/community-courses/">community-contributed OER courses</a> hosted by Lumen</p>
			<p><em><span id="timer"><script type="text/javascript">countdown();</script></span></em></p>
		</div>
	</div>
</div>
<!-- END LUMEN SPECIFIC -->
