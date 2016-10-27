<?php include '..\Controllers\simple_html_dom.php';

function ReturnCheckBalise($html,$balise){
    
    if (!is_null($html->find($balise,0))){
        return $html->find($balise,0)->plaintext;
    }else
        return "Pas de ".$balise;

}

function ReturnMetaDescription($html,$balise){
    
    if (!is_null($html->find($balise, 0))){
        return $html->find($balise,0)->content;
    }else
        return "Pas de ".$balise;

}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Crawler Php</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php if (!isset($_POST['recherche'])){?>

	<form action="RecupPageGoogle.php" id=scrawler method=post name=formulaire onsubmit="">
	  <fieldset>
	    <legend>Crawler</legend>
		    <ol>
		      <li>
		        <label for=nom>Rechercher</label>
		        <input id=recherche name=recherche type=text placeholder="Recherche google" required autofocus>
		      </li>
		    </ol>
		</fieldset>
		<fieldset>
		     <ol>
		      <li>
		        <label for=nom>Nombre de page:</label>
		        <input id=nbpage name=nbpage type=text placeholder="Nombre de page" required autofocus>
		      </li>
		    </ol>
	  </fieldset>
	  <fieldset>
	    <button type=submit>Ok</button>
	  </fieldset>
	</form>
<?php }else{ 
//-------------------------------------------------------------------------------------------------------------------------    
print $_POST['recherche'];

	if (($_POST['recherche'])!="") {
	    $q=strip_tags($_POST['recherche']);
	}

	if (($_POST['nbpage'])!="") {
	    $nbpage=$_POST['nbpage'];
	}
	$url = array();
    $page=0;
    $urlgoogle="http://www.google.fr/search?hl=fr&q=".urlencode($q)."&start=".$page."&filter=0";
    $useragent="Mozilla/5.0";
    $urlserp="";
    while ($page<$nbpage) {
        if (function_exists('curl_init')) {
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
            curl_setopt($ch, CURLOPT_URL, $urlgoogle);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $serps=curl_exec($ch);
            curl_close($ch);
        } else {
            $serps=file_get_contents($urlgoogle);
        }
        preg_match_all('/<h3 class="r"><a href="(.*?)"/si',$serps,$matches);
        $result=count($matches[1]);
       
        $page++;
        $urlgoogle="http://www.google.fr/search?hl=fr&q=".urlencode($q)."&start=".$page."&filter=0";
        $i=0;
        while($i<$result) 
        {
            $urlserp=trim($matches[1][$i]);
            $urlserp=str_replace("/url?q=","",$urlserp);
            $urlserp=preg_replace("~(.+&amp;sa)[^/]*~","$1",$urlserp);
            $urlserp=str_replace("&amp;sa","\n",$urlserp);
            $urlserp=str_replace("/search?q=".urlencode($q)."&amp;tbm=plcs","",$urlserp);
            $i++;
            array_push($url, $urlserp);
            flush();
        }
    }
    
    foreach ($url as $k) {
    	
//---------------------------------------------------------------------------------------------------------------------------------------------------------
        $html = file_get_html(trim($k));
        print ("<br><br>");
        print ("<br> Site :".trim($k));
        print("<br> h1 :".ReturnCheckBalise($html,'h1'));
        print("<br> h2 :".ReturnCheckBalise($html,'h2'));
        print("<br> h3 :".ReturnCheckBalise($html,'h3'));
        print("<br> title :".ReturnCheckBalise($html,'title'));
        print("<br> Meta description: ".ReturnMetaDescription($html,"meta[name='description']"));
        print("<br> Body: ".ReturnCheckBalise($html,'body'));
    }
} ?>
</body>
</html>