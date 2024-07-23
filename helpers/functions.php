<?php


/********************************* Piologs *********************/

function getPiologs(){


	$collectionId=31; //piolog
	$elementId =40; //date
	/* the raw query:
		select om_element_texts.text, om_element_texts.record_id from om_element_texts 
	join om_items on om_element_texts.record_id=om_items.id 
	where om_items.collection_id=32 and om_element_texts.element_id=40 
	order by om_element_texts.text desc;
	*/
	$db = get_db();
	$stmt = $db->query(
		'select qodv_element_texts.text, qodv_element_texts.record_id from qodv_element_texts 
		join qodv_items on qodv_element_texts.record_id=qodv_items.id 
		where qodv_items.collection_id= ? and qodv_element_texts.element_id= ? 
		order by qodv_element_texts.text desc',
		array($collectionId, $elementId)
	);
	$result = $stmt->fetchAll();

	$output="";
	$dates=array();
	$years=array();
	$ids=array();
	$links=array();
	
	foreach ($result as $row) {
		$date=$row["text"];
		$itemId=$row["record_id"];
		$itemLink = "/items/show/$itemId";
		$year=explode("-", $date)[0];
		//$link="/items/browse?advanced%5B0%5D%5Belement_id%5D=92&advanced%5B0%5D%5Btype%5D=is+exactly&advanced%5B0%5D%5Bterms%5D=$url_text";
		  //$html="<a href='/items/browse?search=$url_text&query_type=exact_match&record_types[]=Item'>$text</a> <br /><br />";
		  array_push($dates, $date);
		  array_push($years,$year);
		  array_push($ids, $itemId);
		  array_push($links,$itemLink);
	}

	array_multisort($dates, SORT_DESC,$years, $ids, $links);
	$c=count($dates);
	$yearsUnique=array_unique($years, SORT_REGULAR);


	foreach ($yearsUnique as $year) {
		$springYear=strval($year);
		$fallYear = strval($year - 1);
	$output .= '<div class="card mb-3 bg-color-1 home border-0">
	<div class="card-body">
	  <h5 class="card-title"></h5>
	  <p class="card-text"></p>
	  <div class="accordion" id="appt'.$fallYear.'">
		<div class="accordion-item">
		  <h2 class="accordion-header" id="apptHeading'.$fallYear.' - '.$springYear.'">
			<button class="accordion-button home collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#apptCollapse'.$fallYear.'" aria-expanded="false" aria-controls="apptCollapse'.$year.'">
			  '.$fallYear.' - '.$springYear.'
			</button>
		  </h2>
		  <div id="apptCollapse'.$fallYear.'" class="accordion-collapse collapse" aria-labelledby="apptHeading'.$fallYear.'" data-bs-parent="#appt'.$fallYear.'">
			<div class="accordion-body">';
	$issues = "";
	for ($i = 0; $i < $c; $i++) {
		$date=$dates[$i];
		$month = explode('-', $date)[1];
		if (($years[$i] == intval($fallYear) and (intval($month) >= 8 and intval($month) <= 12))
		or ($years[$i] == intval($springYear) and (intval($month) >= 1 and intval($month) <= 7))) {
		$link=$links[$i];
		$parts=explode("-", $date);
		$yyyy=$parts[0];
		$mm=$parts[1];
		$dd=$parts[2];
		$formattedDate=date("F, Y", mktime(0, 0, 0, $mm, $dd, $yyyy)); 
		$html = "<a href='$link'>$formattedDate</a> <br /><br />";
		$issues .= $html;
		}
	}
	$output.= $issues;
	$output .=	'</div>
		  </div>
		</div>
	</div>
  </div>
</div>';
	}


	return $output;




}
/******************************   end Piologs **********************************/


/******************************   Oral Histories *******************************/

function getOhMetadata($collectionId){
	
	$record=get_record_by_id($modelName="Collection", $recordId=$collectionId);

	if(metadata($record, array('Dublin Core', 'Description'))){
        $description = metadata($record, array('Dublin Core', 'Description'));

    }
	$title=metadata($record, array('Dublin Core', 'Title'));

	$md["title"]=$title;
	$md["description"]=$description;
	return $md;
}

function getOhJs(){

	$js='
	<script>
	jQuery( document ).ready(function() {
	  jQuery("p.subject>span").click(function(){
		var subject=jQuery(this).attr("class");
		var text=jQuery(this).text();
		console.log(subject);
		console.log(text);
		jQuery(".entry").each(function() {
			console.log(this);
			//mvar += $(this).html();
			if(!jQuery(this).hasClass(subject)){
				jQuery(this).fadeOut();
			}
			else{
				jQuery(this).fadeIn();
			}

		});
		jQuery("#displaying").text(text);
		jQuery("#showAll").fadeIn();

	});

	jQuery("#showAll").click(function(){
		jQuery(".entry").each(function() {
			jQuery(this).fadeIn();


		});	
		jQuery("#displaying").text("all");
		jQuery("#showAll").fadeOut();	
	});
  });
  
  
  
  
  </script>
  ';
  return $js;



}




/******************************  end Oral Histories ****************************/


?>
