<?php
 function rssReader($url, $maxItems=3, $nItem=0){
    $rss = simplexml_load_file($url);
    $items = array();
    foreach($rss->channel->item as $item){
        if(++$nItem > $maxItems) break;
        if($item->enclosure['url']){
            $media['url'] = $item->enclosure['url'];
        }else if($item->children('http://search.yahoo.com/mrss/')){
            $media = $item->children('http://search.yahoo.com/mrss/')->thumbnail->attributes();
        }
        $items[] = array(
            "link"  => $item->link,
            "title" => $item->title,
            "media" => $media['url'],
            "nItem" => $nItem,
            "description" => $item->description
        );
    }
    return $items;
}

function rss2html($items, $maxChars=88){
    $html = '';
    foreach($items as $item){
        $html.= '<article>';
        $html.= '<h5><a target="_blank" href="'.$item->link.'">'.$item->title.'</a></h5>';
        if(isset($item->media) && $item->media!='') {
            $html.= '<img src="'.$item->media.'" alt="image '.$nItem.'">';
        }
        $html.= '<div>';
        $html.= substr($item->description,0,strpos($item->description,' ',$maxChars));
        $html.= '...</div>';
        $html.=' </article>';
    }
    return $html;
}

$items = rssReader('http://certificacionenergeticaparaviviendas.wordpress.com/feed/', 4);
echo rss2html($items);
?>