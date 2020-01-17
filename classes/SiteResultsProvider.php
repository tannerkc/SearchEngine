<?php 

class SiteResultsProvider {

	private $con;

	public function __construct($con){
		$this->con = $con;
	}

	public function getNumResults($term){

		$query = $this->con->prepare("SELECT COUNT(*) as total FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term");

		$term = "%" . $term . "%";
		$query->bindParam(":term", $term);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"];

	}

	public function getResultsHtml($page, $pageSize, $term){

		

				$calculation = "";

		if(preg_match('/(\d+)(?:\s*)([\+\-\%\*\/])(?:\s*)(\d+)/', $term) || $term == "calc" || $term == "calculator"){
			if(preg_match('/(\d+)(?:\s*)([\+\-\%\*\/])(?:\s*)(\d+)/', $term, $matches) !== FALSE){
			    @$operator = $matches[2];

			    switch($operator){
			        case '+':
			            $calculation = $matches[1] + $matches[3];
			            break;
			        case '-':
			            $calculation = $matches[1] - $matches[3];
			            break;
			        case '*':
			            $calculation = $matches[1] * $matches[3];
			            break;
			        case '/':
			            $calculation = $matches[1] / $matches[3];
			            break;
			        case '%':
			            $calculation = $matches[1] % $matches[3];
			            break;
			    }

			    if(preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)(\d+)/', $term)){ $startTxt = $term . " ="; }else{ $startTxt = ""; }

			    echo "<div class='calculator'>
					  <div class='input' id='input'>$startTxt $calculation</div>
					  <div class='buttons'>
					    <div class='operators'>
					      <div>+</div>
					      <div>-</div>
					      <div>&times;</div>
					      <div>&divide;</div>
					    </div>
					    <div class='leftPanel'>
					      <div class='numbers'>
					        <div>7</div>
					        <div>8</div>
					        <div>9</div>
					      </div>
					      <div class='numbers'>
					        <div>4</div>
					        <div>5</div>
					        <div>6</div>
					      </div>
					      <div class='numbers'>
					        <div>1</div>
					        <div>2</div>
					        <div>3</div>
					      </div>
					      <div class='numbers'>
					        <div>0</div>
					        <div>.</div>
					        <div id='clear'>C</div>
					      </div>
					    </div>
					    <div class='equal' id='result'>=</div>
					  </div>
					</div>";
			}
		}


		$fromLimit = ($page - 1) * $pageSize;

		$query = $this->con->prepare("SELECT * FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term ORDER BY clicks DESC LIMIT :fromLimit, :pageSize");

		$term = "%" . $term . "%";
		$query->bindParam(":term", $term);
		$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
		$query->execute(); 

		$resultsHtml = "<div class='siteResults'>";

		$count = 0;
		while($row = $query->fetch(PDO::FETCH_ASSOC)){
			$count++;

			$id = $row["id"];
			$url = $row["url"];

			$scheme = parse_url($url)["scheme"];

			$displayURL = str_replace(['http://', 'https://'], '', $url);
			$displayURL = str_replace('/', ' > ', $displayURL);
			$displayURL = $scheme . '://' . $displayURL;

			$title = $row["title"];
			$description = $row["description"];

			$trimTitle = $this->trimField($title, 62);
			$trimDescription = $this->trimField($description, 245);

			$resultsHtml .= "<div class='resultsContainer'>
								<h3 class='title'>
									<iframe src='$url' class='link$count'></iframe>
								<a class='result' href='$url'data-linkId='$id'>

									$trimTitle
								</a>
								</h3>
								<span class='url ellipsis'>$displayURL</span>
								<span class='description'>$trimDescription</span>
							</div>";
		}

		$resultsHtml .= "</div>";

		return $resultsHtml;
	}

	private function trimField($string, $characterLimit){

		$dots = strlen($string) > $characterLimit ? "..." : "";
		return substr($string, 0, $characterLimit) . $dots;

	}

}
?>