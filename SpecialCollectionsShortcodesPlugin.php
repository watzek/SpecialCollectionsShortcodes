<?php
define('SPECIALCOLLECTIONS_SHORTCODES_PLUGIN_DIR', PLUGIN_DIR . '/SpecialCollectionsShortcodes');

require_once(SPECIALCOLLECTIONS_SHORTCODES_PLUGIN_DIR . '/helpers/functions.php');


class SpecialCollectionsShortcodesPlugin extends Omeka_Plugin_AbstractPlugin{



    protected $_hooks = array('initialize');

    public function hookInitialize()
    {

        add_shortcode('piolog', array($this, 'piologShortcode'));
        add_shortcode('oralHistories', array($this, 'oralHistoriesShortcode'));



    }


    public function piologShortcode($args, $view){

        $output="<div class='container-flex bg-light py-2'>
                    <div class='container w-md-75'>
                    <ul id='simple-pages-breadcrumbs' class='breadcrumb'>
    <li class='breadcrumb-link'><a href='/'>Home</a>&nbsp>&nbsp</li>
    <li class='breadcrumb-link'><a href='/collections/browse?sort_field=Dublin+Core%2CTitle'>Digital Collections</a></li>
</ul>
                        <h4 class='my-3'>Lewis & Clark College: Publications - Newspaper - Pioneer Log / Mossy Log</h4>

                        <p>This collection includes complete issues in pdf format of Lewis & Clark College student newspaper the Pioneer Log, now named The Mossy Log.</p>
                    </div>
                </div>";
        $piologHtml=getPiologs();
        $output.="<div class='container w-md-75' style='padding-top: 20px;'>";
        $output.=$piologHtml;
        $output.="</div>";

        return $output;

    }


    public function oralHistoriesShortcode($args, $view){
        $collectionId=60;
        $ohMetadata=getOhMetadata($collectionId);
        $title=$ohMetadata["title"];
        $description=$ohMetadata["description"];
        

        $items = get_records('Item',array('collection'=>$collectionId,),500);
        //var_dump($items);


        $output="<div class='container-flex bg-light py-2'>
                    <div class='container w-md-75'>
                        <ul id='simple-pages-breadcrumbs' class='breadcrumb'>
                            <li class='breadcrumb-link'><a href='/'>Home</a>&nbsp>&nbsp</li>
                            <li class='breadcrumb-link'><a href='/collections/browse?sort_field=Dublin+Core%2CTitle'>Digital Collections</a></li>
                        </ul>
                        <h4 class='my-3'>$title</h4>

                        <p>$description</p>
                    </div>
                </div>";


        $piologHtml=getPiologs();
        $output.="<div class='container w-md-75' style='padding-top: 20px;'>";

        $output.="<p>Displaying: <span id='displaying'>all</span></p>";
        $output.="<p id='showAll' style='display:none;'>Show all</p>";

        foreach ($items as $item){

            $itemTitle=metadata($item, array('Dublin Core', 'Title'));
            $itemDescription=metadata($item, array('Dublin Core', 'Description'), $options=array("snippet"=>150));
            $itemSubjects=metadata($item, array('Dublin Core', 'Subject'), $options=array("all"=>true));
            $img=record_image($item, 'square_thumbnail');
            $id=$item->id;
            $interviewDate=metadata($item, array('Item Type Metadata', 'Interview Date'));

            $subjectsList="";
            $classList="";
            foreach($itemSubjects as $subject){
                if($subject !="Oral History" && $subject!="Oral history"){
                    $s=urlencode($subject);
                    $subjectsList.="<span class='$s'>$subject</span> | ";
                    $classList.="$s ";

                }
            }
            $subjectsList=rtrim($subjectsList, "| ");
            $output.="<div class='row mb-3 entry $classList '>
            <div class='card shadow' style='width:100%;padding:20px;'>
                <div class='card-img-top d-flex align-items-center bg-light'>
                    <div class='ohImage'>
                    $img
                        <!--<img src='' class='rounded-lg' width='127px' height='150px' style='margin-right:10px;'>-->
                    </div>
                    <div class='ml-2 pl-2'>
                        <h2><a href='/items/show/$id'>$itemTitle</a></h2>
                        <p class='col p-2 m-0'>$itemDescription</p>
                        <p class='col p-2 m-0'>Interviewed $interviewDate</p>
                        <p class='col p-2 m-0 subject'>$subjectsList</p>

                    </div>
                </div>
            </div>
        </div>";

        }

        





        //$output.=$piologHtml;
        $output.="</div>";
        $output.=getOhJs();

        return $output;

    }






}




?>
