<div class="page_nav">
      <div class="page_nav_con">
        <div class="big_title">Fiberstore News</div>
        <div class="short_title"><a href="<?php echo  zen_href_link(FILENAME_IN_THE_NEWS);?>" class="title_selected">News Releases</a><a href="#">In The News</a></div>
      </div>
    </div>
    <div class="fs_news_banner">
      <div class="fs_news_banner_con">
        <div class="fs_news_banner_text"><span>News Releases</span>We never stop improving our services, including development methodologies, <br />
          engineering practices, management techniques</div>
      </div>
    </div>
    <?php 
    global $db;
    $event_sql ="select date_format(news_date_published,'%Y') as Eyear from ".TABLE_NEWS_ARTICLES." group by date_format(news_date_published,'%Y') ORDER BY news_date_published desc ";
	$event_result = $db->Execute($event_sql);
	while (!$event_result->EOF){
		$Eyear[] = $event_result->fields['Eyear'];
		$event_result->MoveNext();
	}
	foreach ($Eyear as $key => $value){
		$Eyears[$value]=$key;
	}
	$Eyears = array_flip($Eyears);
    ?>
    <div class="fs_news_con">
      <div class="fs_news_title">News Releases
        <select name="" id="select">
      <?php foreach ($Eyears as $key => $time){?>
          <option value="<?php echo $time;?>" selected="selected"><?php echo $time;?></option>
      <?php };?>
        </select>
      </div>
      <div class="fs_news_con" id="news">
<?php 
//$event_date = $_GET['event'];
//var_dump($event_date);
  if(isset($events_array)){
	for ($i = 0,$n =sizeof($events_array); $i < $n;$i++){
		$id = $events_array[$i]['article_id'];
		$iamge = $events_array[$i]['news_image'];
		$title = $events_array[$i]['news_article_name'];
		$source = substr(strip_tags($events_array[$i]['news_article_text']),0,100);
		$url = $events_array[$i]['in_the_news_url'];
		$time = date('M d,Y',strtotime($events_array[$i]['news_date_published']));
		$image_url='/images/'.$iamge;
		if($title != null)
					echo '<dl>
					          <dt><img src="'.$image_url.'" width="138" height="86" /></dt>
					          <dd><b>'.$time.'</b>
					          <span>'.$title.'</span>
					          <p>'.$source.'.. <a href="'.zen_href_link(FILENAME_NEWS_ARTICLE,'&article_id='.$id).'" target="_blank">Read more</a></p>
					          </dd>
					      </dl>';
				}
			}
?>
    
        <br />
        <?php if (1 < $split->number_of_pages){?>
        <div class="filter_tools filter_tools_no">
			<ul>
			<li class="page page_01"><?php echo $split->display_count(TEXT_DISPLAY_NUMBER_OF_NEWS);?>&nbsp;&nbsp;&nbsp;<b>Page:</b><?php echo $split->display_links(5);?></li>
			<br class="ccc">
			</ul>
		</div>
		<?php }?>
      </div>
    </div>
<script src="/includes/templates/fiberstore/jscript/jquery-1.3.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#select").change(function(){
		var event_data = $(this).attr("value");
		var url = 'ajax_test.php?event='+event_data;
		$.ajax({
			   type: "get",
			   url: url,
			   success: function(re){
					if(re){
						$("#news").empty();
						$("#news").html(re);
				   },
			})
	})
</script>
<script>
//滚动后导航固定
function pageScroll(){
    window.scrollBy(0,-600);
    scrolldelay = setTimeout('pageScroll()',50);
    var sTop=document.documentElement.scrollTop+document.body.scrollTop;
    if(sTop==0) clearTimeout(scrolldelay);
}
$(function(){
    $(window).scroll(function(){
    var this_scrollTop = $(this).scrollTop();
    if(this_scrollTop>170){
    $(".page_nav").show();
    }else{
    $(".page_nav").hide();       
    }
    });
});

</script>
