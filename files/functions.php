<?php

function resize($source_image, $destination, $ratio, $quality = 100, $wmsource = false)
{
    $info = getimagesize($source_image);
    $imgtype = image_type_to_mime_type($info[2]);

    #assuming the mime type is correct
    switch ($imgtype) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($source_image);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($source_image);
            break;
        case 'image/png':
            $source = imagecreatefrompng($source_image);
            break;
        default:
            break;
    }

   
    $src_w = imagesx($source);
    $src_h = imagesy($source);

 $tn_w = $ratio*$src_h;
$tn_h = $src_h;

    $x_ratio = $tn_w / $src_w;
    $y_ratio = $tn_h / $src_h;

    if (($src_w <= $tn_w) && ($src_h <= $tn_h)) {
        $new_w = $src_w;
        $new_h = $src_h;
    } elseif (($x_ratio * $src_h) < $tn_h) {
        $new_h = ceil($x_ratio * $src_h);
        $new_w = $tn_w;
    } else {
        $new_w = ceil($y_ratio * $src_w);
        $new_h = $tn_h;
    }

    $newpic = imagecreatetruecolor(round($new_w), round($new_h));
    imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
    $final = imagecreatetruecolor($tn_w, $tn_h);
    $backgroundColor = imagecolorallocate($final, 255, 255, 255);
    imagefill($final, 0, 0, $backgroundColor);
    //imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($tn_w / 2)), ($y_mid - ($tn_h / 2)), $tn_w, $tn_h, $tn_w, $tn_h);
    imagecopy($final, $newpic, (($tn_w - $new_w)/ 2), (($tn_h - $new_h) / 2), 0, 0, $new_w, $new_h);

    #if we need to add a watermark
    if ($wmsource) {
        #find out what type of image the watermark is
        $info    = getimagesize($wmsource);
        $imgtype = image_type_to_mime_type($info[2]);

        #assuming the mime type is correct
        switch ($imgtype) {
            case 'image/jpeg':
                $watermark = imagecreatefromjpeg($wmsource);
                break;
            case 'image/gif':
                $watermark = imagecreatefromgif($wmsource);
                break;
            case 'image/png':
                $watermark = imagecreatefrompng($wmsource);
                break;
            default:
                die('Invalid watermark type.');
        }

        #if we're adding a watermark, figure out the size of the watermark
        #and then place the watermark image on the bottom right of the image
        $wm_w = imagesx($watermark);
        $wm_h = imagesy($watermark);
        imagecopy($final, $watermark, $tn_w - $wm_w, $tn_h - $wm_h, 0, 0, $tn_w, $tn_h);

    }
    if (imagejpeg($final, $destination, $quality)) {
        return true;
    }
    return false;
}
function download($imagename,$logo,$dest)
{ 
  $src2='../images/random_image/'.mt_rand(1,44).'.jpg';
if($imagename!="" && strlen($imagename)-1!=stripos($imagename,"."))
{ 

  $src='http://prop.favistat.com/img/project_images/big/'.$imagename;
  $src1='http://prop.favistat.com/img/project_images/orgimg/'.$imagename;
  $src3='http://prop.favistat.com/img/project_images/original/'.$imagename;
  copy($src,$dest);

  if(!file_exists($dest))
  {
 copy($src1,$dest);
   }

  if(!file_exists($dest))
  {
 copy($src2,$dest);
   }

   if(!file_exists($dest))
  {
 copy($src3,$dest);
   }

 }else  copy($src2,$dest);
   
if(file_exists($logo))
   addwatermark($dest,$logo,$dest);

  resize($dest,$dest, 3/2);
}

function addwatermark($oimage,$watermark,$write_dir)
{
$ratio=5;

$imagesource = $oimage;
$file_2 = $watermark;
 $size2 = getimagesize($file_2);
$size = getimagesize($imagesource);
  $h=$size[1]/$ratio;
  $w=$size[0]/$ratio;
  
 $image_2=smart_resize_image($file_2, $w, $h, true, "logo2.png",false,false);

  

$filetype = substr($imagesource,strlen($imagesource)-4,4);
$filetype = strtolower($filetype);
if($filetype == ".gif")  $file_1 = @imagecreatefromgif($imagesource);
if($filetype == ".jpg")  $file_1 = @imagecreatefromjpeg($imagesource);
if($filetype == ".png")  $file_1 = @imagecreatefrompng($imagesource);
if($filetype == "jpeg")  $file_1 = @imagecreatefromjpeg($imagesource);

// open image 1
$image_1 = $file_1;


imageAlphaBlending($image_1, false);
imageSaveAlpha($image_1, true);
$x1 = imagesx($image_1);
$y1 = imagesy($image_1);
// open image 2

$x2 = imagesx($image_2);
$y2 = imagesy($image_2);
// make a transparent background
$slate = imagecreatetruecolor(max($x1, $x2), max($y1, $y2));
$transparent = imagecolorallocatealpha($slate,0,255,0,127);
imagefill($slate,0,0,$transparent);
// now do the copying
$marge_right = 8;
$marge_bottom = 8;
$sx = imagesx($image_2);
$sy = imagesy($image_2);
imagecopy($slate, $image_1, 0, 0, 0, 0, imagesx($image_1)-1, imagesy($image_1)-1);
imagecopy($slate, $image_2, imagesx($image_1) - $sx - $marge_right, imagesy($image_1) - $sy - $marge_bottom, 0, 0, imagesx($image_2)-1, imagesy($image_2)-1);
// for the background do this after copying is finished
imageAlphaBlending($slate, false);
imageSaveAlpha($slate, true);
imagepng($slate,$write_dir);
imagedestroy($image_1);
imagedestroy($image_2);
}
function smart_resize_image($file,
                              $width              = 0, 
                              $height             = 0, 
                              $proportional       = false, 
                              $output             = 'file', 
                              $delete_original    = true, 
                              $use_linux_commands = false ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;

    # Setting defaults and meta
    $info                         = getimagesize($file);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;

    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );

      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }

    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
      case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
      case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);

      if ($transparency >= 0) {
        $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
        $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
    
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }

    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination
  

    return $image_resized;
  }
  function copydir($source,$destination)
{
  if(!is_dir($destination)){
    $oldumask = umask(0); 
    mkdir($destination, 0775); 
    umask($oldumask);
  }
  $dir_handle = @opendir($source) or die("Unable to open ".$source);
  while ($file = readdir($dir_handle)) 
  {
    if($file!="." && $file!=".." && !is_dir("$source/$file"))
      copy("$source/$file","$destination/$file");
  }
  closedir($dir_handle);
}
function removeattr_a($str)
{
  $newstr='';

  while(strlen($str)>0)
  {
    if(strpos($str,"<a")>0)
    {
      $newstr.=strstr($str,"<a",true);
      $str=strstr($str,"<a");

      $i=strpos($str, ">");

      $j=strpos($str, "</a>");
      $newstr.=substr($str,$i+1,$j-$i-1);
      $str=substr($str,$j+4);

    }
    else
    {
      $newstr.=$str;
      $str='';
    }

  }
  $b= str_replace('<body>', "", $newstr);
  $b=str_replace('</body>', "", $b);
  $b=str_replace('<html>', "", $b);
  $b=str_replace('</html>', "", $b);
  $b=delete_all_between('<form', ">", $b,'');
  return $b;
}
function delete_all_between($st, $en, $str,$add) {
  if($str!='')
  {
  $pos=strpos($str, $st);
  $pos2=strpos($str, $en,$pos+strlen($st));

  //echo $pos."--".$pos2;
  return substr($str,0,$pos).$add.substr($str, $pos2+strlen($en));
}
else return '';
}
  ?>