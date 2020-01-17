<?php 

class ImageResultsProvider {

	private $con;

	public function __construct($con){
		$this->con = $con;
	}

	public function getNumResults($term){

		$query = $this->con->prepare("SELECT COUNT(*) as total FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken=0");

		$term = "%" . $term . "%";
		$query->bindParam(":term", $term);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"];

	}

	public function getResultsHtml($page, $pageSize, $term){

		$fromLimit = ($page - 1) * $pageSize;

		$query = $this->con->prepare("SELECT * FROM images WHERE (title LIKE :term OR alt LIKE :term) AND broken=0 ORDER BY clicks DESC LIMIT :fromLimit, :pageSize");

		$term = "%" . $term . "%";
		$query->bindParam(":term", $term);
		$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
		$query->execute(); 

		$resultsHtml = "<div class='imageResults'>";

		$count = 0;
		while($row = $query->fetch(PDO::FETCH_ASSOC)){
			$count++;

			$id = $row["id"];
			$imageSource = $row["imageSource"];
			$siteUrl = $row["siteUrl"];
			$title = $row["title"];
			$alt = $row["alt"];

			if($title){
				$displayText = $title;
			}
			elseif($alt){
				$displayText = $alt;
			}
			else{
				$displayText = $imageSource;
			}

			$resultsHtml .= "<div class='gridItem image$count'>
								
								<a href='$imageSource' data-fancybox data-caption='$displayText' data-siteurl='$siteUrl'>

									<script>

									$(document).ready(function(){
										loadImage(\"$imageSource\", \"image$count\");
									});

									</script>
									<span class='details ellipsis'>$displayText</span>
								</a>
							</div>";
		}

		$resultsHtml .= "</div>";

		return $resultsHtml;
	}


}
?>