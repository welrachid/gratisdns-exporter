<?php

$html = file_get_contents('gratisdns.html');
$dom = new DOMDocument;
//there is a lot of warnings in the html. : PHP Warning:  DOMDocument::loadHTML(): htmlParseEntityRef: expecting ';' in Entity, line: 6213 in extract_links.php on line 5
@$dom->loadHTML($html);
$links = $dom->getElementsByTagName('a');
$templates = []; // templates
$domains = []; // standalone

$current_template = null;
foreach ($links as $link){
    if($link->nodeValue == 'Export'){
        $link_uri = $link->getAttribute('href');
        $template_or_domain=$link->parentNode->parentNode->parentNode->firstElementChild->textContent;

        if(strpos($template_or_domain,"(template)") !== false){
            // we are a template. update and go to next
            $templates[$link_uri]['name'] =$template_or_domain;
            $templates[$link_uri]['domains']=[];
            $current_template=$link_uri;
            continue;
        }
        if(substr($link_uri,0,8) != 'https://'){
            // append hostname
            $link_uri='https://admin.gratisdns.com'.$link_uri;
        }
        if(!preg_match("/[a-zA-Z0-9]/",substr($template_or_domain,0,1))){
            // the domain is inside a template - must require current_template to be set
            if($current_template === null){
                die("Current template not set in a context where domain is expected to be in a template");
            }
            $templates[$current_template]['domains'][] = $link_uri;
        } else {
            // domain is standardalone with no template
            $domains[$template_or_domain] = $link_uri;
        }
    }
    
}
if(!file_exists('export_from_gdns')) mkdir("export_from_gdns");

$write_to_file = "";
foreach($templates as $index => $template){
    $directory_name = preg_replace('/[^A-Za-z0-9\-\_\.]/', '-', $template['name']);
    $write_to_file .= "rm -R export_from_gdns/".$directory_name."\n";
    $write_to_file .= "mkdir export_from_gdns/".$directory_name."\n";
    foreach($template['domains'] as $domain_index => $domain_export_uri){
        $write_to_file .= "curl -b 'cookiefile' '".$domain_export_uri."' -o 'export_from_gdns/".$directory_name."/".$domain_index."' \n";
        $write_to_file .= "sleep 1\n";
    }
}
foreach($domains as $domain_name => $domain_export_uri){
    $directory_name = preg_replace('/[^A-Za-z0-9\-\_\.]/', '-', $domain_name);
    $write_to_file .= "rm -R export_from_gdns/".$directory_name."\n";
    $write_to_file .= "mkdir export_from_gdns/".$directory_name."\n";
    $write_to_file .= "curl -b 'cookiefile' '".$domain_export_uri."' -o 'export_from_gdns/".$directory_name."/".$directory_name."' \n";
    $write_to_file .= "sleep 1\n";
}

file_put_contents("download.sh",$write_to_file);
chmod("download.sh",0755);