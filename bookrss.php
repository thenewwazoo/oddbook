<?
include('ob_config.php');
include('includes/ob_vars.inc');
include('includes/ob_sidebar.inc');
include('includes/ob_translate.inc');
include('includes/ob_select.inc');
include('includes/ob_search.inc');
include('includes/ob_cats.inc');

header('Content-Type: text/xml; charset=iso-8859-1', true);

function rssheader() {

	global $baseURLforOddbook, $siteTitle, $siteDescription, $rssMaxEntries, $pathstr, $baseURLforOddbook, $fullURLToOddbook;

	echo "<?xml  version=\"1.0\" encoding=\"iso-8859-1\"?>\n". 
		"<rss version=\"2.0\">\n".
		"  <channel>\n".
		"    <title>". $siteTitle ."</title>\n".
		"    <link>". $fullURLToOddbook ."</link>\n".
		"    <description>". $siteDescription ."</description>\n" .
		"    <language>en-us</language>\n" .
		"    <lastBuildDate>" . date(DATE_RFC822) . "</lastBuildDate>\n" .
		"    <ttl>" . $rssMaxEntries . "</ttl>\n";
}

function rssfooter() {
	echo "  </channel>\n".
		"</rss>";
}

function rss() {
	rssheader();

	global $dbhost,$dbuser,$dbpass,$dbname;
	global $baseURLforOddbook, $siteTitle, $siteDescription, $rssMaxEntries, $pathstr, $baseURLforOddbook, $fullURLToOddbook;
	
	mysql_connect($dbhost, $dbuser,$dbpass);
	mysql_select_db($dbname);

	$res = mysql_query("select review.review_id as review_id,review.book_id 
	       as book,review.author_id as auth,review.review_text as text,
           DAYOFMONTH(review.review_date) as review_day,
           MONTHNAME(review.review_date) review_month,
           YEAR(review.review_date) as review_year,
           review.rating as rating,
           author.lastname as auth_l, author.firstname as auth_f,
           book.title_sw as sw, book.title as title, book.pubyear as pubyear,
           book.ISBN as isbn
           from review
           inner join author on author.author_id = review.author_id
           inner join book on book.book_id = review.book_id
           order by review.review_date desc limit ".$rssMaxEntries.";");

	$fp = fopen("includes/templates/rssreview.tmpl",r);
	while($row = mysql_fetch_assoc($res)) {
        // Grab book categories
        $cats = dn_select_cat_by_book( $row['review_id'], false );
        if ( count($cats) == 0 ) {
            $cat_value = '';
        } else {
            $cat_value = '<category>'.implode( '</category> <category>', $cats ).'</category>';
        }
        //
		echo "    <item>\n".
			"      <title>" . $row['title'] . " by " . $row['auth_f'] ." " . $row['auth_l'] . 
			"</title>\n" .
			"      <link>" . $fullURLToOddbook . "/book/" . $row['review_id'] ."</link>\n".
			"      <description>";
		array_push($row,$fp);
		render_review($row, false);
		echo "</description>\n".
			"<guid>" . $fullURLToOddbook . "/book/" . $row['review_id'] . "</guid>\n" .
			"    </item>\n";
		
	}
	fclose($fp);
	
	rssfooter();

}

?>
<?
rss();
?>
