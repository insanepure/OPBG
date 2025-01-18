<?php
function get_domain($url)
{
    if(substr( $url, 0, 4 ) !== "http")
    {
        if(substr($url, 0, 1) == "/")
            $url = "https://".$_SERVER['HTTP_HOST'].$url;
        else
            $url = "https://".$_SERVER['HTTP_HOST']."/".$url;
    }
    $urlobj = parse_url($url);
    if($urlobj) {
        $domain = $urlobj['host'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
    }
    return false;
}

if (isset($_GET['url']))
{
    $url = $_GET['url'];
    $domainUrl = get_domain($url);
    if ($domainUrl == 'op-bg.de')
    {
        header('Location: ' . $url);
        exit;
    }
    if ($domainUrl == 'directupload.net')
    {
        exit;
    }

    $path_parts = pathinfo($url);
    if ($path_parts['extension'] != 'jpg' && $path_parts['extension'] != 'png' && $path_parts['extension'] != 'bmp' && $path_parts['extension'] != 'jpeg' && $path_parts['extension'] != 'gif')
    {
        $url = 'img/noimg.jpg';
        header('Location: ' . $url);
        exit;
    }

    $size = getimagesize($url);
    $fp = fopen($url, "rb");
    if ($size && $fp)
    {
        header("Cache-Control: max-age=2592000"); //30days (60sec * 60min * 24hours * 30days)
        header("Content-type: {$size['mime']}");
        fpassthru($fp);
        exit;
    }
    else
    {
        echo '<br/>Fehler<br/>';
        echo 'url: ' . $url . '<br/>';
        echo 'Größe: ' . $size . '<br/>';
        print_r($fp);
    }
}
