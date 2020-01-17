<?php
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");


	if(isset($_GET['term'])){
		$term = $_GET['term'];
	}
	else{
		exit();
	}

	$type = isset($_GET['type']) ? $_GET['type'] : "all";
	$page = isset($_GET['page']) ? $_GET['page'] : "1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Loupe</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

	<link rel="stylesheet" type="text/css" href="assets/css/main.css">

	<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
</head>
<body>

	<div class="wrapper">

		<div class="header">

			<div class="headerContent">

				<div class="logoContainer">
					<a href="index.php">
						<img src="assets/img/icons/loupe.png">
					</a>
				</div>

				<div class="searchContainer">

				<form action="search.php" method="GET">
					<div class="searchBarContainer">
						<input type="hidden" name="type" value="<?php echo $type; ?>">
						<input class="searchBox" type="text" name="term" value="<?php echo $term; ?>" autocomplete="off">

						<button>
							<img src="assets/img/icons/icons8_google_web_search_50px_2.png">
						</button>

					</div>
				</form>

				</div>

			</div>


			<div class="tabsContainer">
				<ul class="tabsList">
					<li class="<?php echo $type == 'all' ? 'active' : '' ?>">
						<a href='<?php echo "search.php?term=$term&type=all";  ?>'>All</a>
					</li>

					<li class="<?php echo $type == 'images' ? 'active' : '' ?>">
						<a href='<?php echo "search.php?term=$term&type=images";  ?>'>Images</a>
					</li>
				</ul>
			</div>

		</div>

		<?php

				if($type == "all"){
					$widthClass = "searchWidth";
				}
				else{
					$widthClass = "";
				}
			?>


		<div class="mainResultsSection <?php echo $widthClass; ?>">


				<?php

				if ($term == 'fedex' || strpos($term, 'fedex tracking') !== false){ ?>

					<form method="get" action="https://www.fedex.com/apps/fedextrack/index.html" class="fedex">

					<input class="searchBox" type='text' name='tracknumbers' placeholder="Tracking Number" />

					<input class="fedexButton" type="submit" value="Track" />

					</form>

					<!-- <canvas id="stage" height="400" width="450"></canvas> -->
				<?php
				}

				$pageTxt = "";

				if($type == "all"){
					$resultsProvider = new SiteResultsProvider($con);
					$pageSize = 15;

					if($page != 1){
						$pageTxt = "Page " . $page . " of ";
					}
					else{
						$pageTxt = "";
					}

					$numResults = $resultsProvider->getNumResults($term);
					$resultsTxt = ($numResults == 1 ? 'Result' : 'Results');
				}
				else{
					$resultsProvider = new ImageResultsProvider($con);
					$pageSize = 45;

					$numResults = $resultsProvider->getNumResults($term);
					$resultsTxt = ($numResults == 1 ? 'Image' : 'Images');
				}
				

				echo "<p class='resultsCount'>$pageTxt $numResults $resultsTxt</p>";

				echo $resultsProvider->getResultsHtml($page, $pageSize, $term);
			 	?>	

			 	<?php

					$pagesToShow = 5;
					$numPages = ceil($numResults / $pageSize);

					$pagesLeft = min($pagesToShow, $numPages);
					$currentPage = $page - floor($pagesToShow / 2);

					if($currentPage < 1){
						$currentPage = 1;
					}

					if($currentPage + $pagesLeft > $numPages){
						$currentPage = $numPages - $pagesLeft;
					}

				if($numResults != 0){

				?>


			 <div class="paginationContainer">
				<div class="pageButtons">
					<div class="pageNumberContainer">
						<?php 
						if($page != 1){
							$lastPage = $page - 1;
						echo 
						"<a href='search.php?term=$term&type=$type&page=$lastPage'>
							<img src='assets/img/icons/backActive.png'>
						</a>";

							}else{
						 echo
						"<img src='assets/img/icons/back.png'>";
						 } ?>
					</div>


					<?php
					while($pagesLeft != 0 && $currentPage <= $numPages){

						if($currentPage == $page){

							echo "<div class='pageNumberContainer active'>
								
								<span class='pageNumber'>$currentPage</span>

							</div>";
						}
						else{

						echo "<div class='pageNumberContainer'>
								<a href='search.php?term=$term&type=$type&page=$currentPage'>
								<span class='pageNumber'>$currentPage</span>
								</a>
							</div>";
						}

							$currentPage++;
							$pagesLeft--;
					}

					?>

					<div class="pageNumberContainer">
						<?php 
						if($page != $numPages){
							$nextPage = $page + 1;
						echo 
						"<a href='search.php?term=$term&type=$type&page=$nextPage'>
							<img src='assets/img/icons/forwardActive.png'>
						</a>";

							}else{
						 echo
						"<img src='assets/img/icons/forward.png'>";
						 } 
						}
						?>
					</div>

				</div>
			</div>		
		</div>

	<!--
		<div class="sideSection">
			<div class="topSection"></div>
			<div class="infoSection"></div>
			<div class="bottomSection"></div>
		</div>
	-->

	</div>



<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

<script type="text/javascript" src="assets/js/script.js"></script>
</body>
</html>