<?php
include 'functions.php';
set_time_limit(0);
$cnt=0;
$fv_images=array();
$fv_units=array();
$con=mysqli_connect("bo.favista.in","readusr","iohdsfn5@49j","favista");

$project= json_decode(file_get_contents('project.json'), true);

{
  $projectid=$project['projectid'];
$googleac=$project['googleac'];
$googlerc=$project['googlerc'];
if(isset($_GET['width1']) && isset($_GET['height1']) && isset($_GET['width2']) && isset($_GET['height2']))
{
  $width1=$_GET['width1'];
  $height1=$_GET['height1'];
   $width2=$_GET['width2'];
  $height2=$_GET['height2'];
  $domain=$_GET['domain'];
  include 'banner.php';
  
}
  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  else
  {
     
    $result = mysqli_query($con,"select p.name,p.builder_id,p.specifications,p.canonical_url,p.lat,p.lon,p.location_id,p.city_id,p.state_id,p.phase,min(pu.price_sft) as minprice,min(pu.sale_price) as smin,max(pu.sale_price) smax,p.unit_types ,
     p.logo,p.priority,loc.name as location,city.name as city,state.name as state,p.site_plan  from fv_projects p left join fv_locations loc on loc.id=p.location_id
      left join fv_cities city on city.id=p.city_id left join fv_states state on state.id=p.state_id inner join fv_properties prop on prop.project_id=p.id and prop.is_verified='1' 
      left join fv_project_units pu on pu.project_id=p.id left join fv_project_images i on p.id=i.project_id and i.is_deleted='0' 
      where p.is_deleted='0' and p.is_active='1' and (p.id=$projectid) group by p.id");
    $count=1;
    
        
   
    while($row = mysqli_fetch_array($result))
    {
      $result1 = mysqli_query($con,"select p.name,m.domain,p.id,pu.floorplan_image1 as fp,i.image,pu.size,pu.price_sft,pu.unit_type,pu.sale_price from fv_projects p  
      left join fv_project_images i on i.project_id=p.id and i.is_deleted='0'
       left join fv_microsites m on p.id=m.project_id left join fv_project_units pu on pu.project_id=p.id
       where builder_id=".$row['builder_id']."  and pu.is_deleted='0'  and pu.is_active='1' order by pu.size");
 $yy=0;
 $set='';
 while($row1 = mysqli_fetch_array($result1))
       {$yy++;
if($row1['id']==$projectid)
{ 
  if(!isset($set[$row1['unit_type']]))
{
$set[$row1['unit_type']]=1;
 array_push(  $fv_units, array('price_sft'=>$row1['price_sft'],'unit_type'=>$row1['unit_type'],'size'=>$row1['size'],'sale_price'=>$row1['sale_price'],'fp'=>$row1['fp']));
  }
  if($row1['image']!='')
  $fv_images[$row1['image']]=$row1['image'];
//echo "x ";
}
if($row1['domain']!='')
$fv_micro[$row1['domain']]=$row1['name'];

   }
   echo sizeof($fv_images)." ".$yy;
   $projectlogo=$row['logo'];
      $state=$row['state'];
        $location=$row['location'];
     $city=$row['city'];
      $lat=$row['lat'];
      $lon=$row['lon'];
      $phase=$row['phase'];
      $varp=$row['location_id'];
      $varq=$row['city_id'];
      $state=$row['state_id'];
      $bhk=$row['unit_types'];

       $type=$row['unit_types']==''?' BHK to be announced':$row['unit_types'];
  if(!strpos($type,'BHK')>=0)
    $type.=" BHK";
  $loc=$location.' - '.$city;
  if(number_format(floatval($row["smin"]/10000000), '2', '.', '')!=0)
    $price ='BSP: '.number_format(floatval($row["smin"]/10000000), '2', '.', '').' Cr - '.number_format(floatval($row["smax"]/10000000), '2', '.', '').' Cr';
  else
    $price='Price on Request';

      $name=strtolower(str_replace(" ", "-", $row['name']))."-".$projectid;
      $count=0;

      $t=$row['name'];
      $a="Information";
       $b=$project['description'];
     $b= str_replace('<body>', "", $b);
      $b=str_replace('</body>', "", $b);
      $b=str_replace('<html>', "", $b);
      $b=str_replace('</html>', "", $b);
      if(!is_dir('../img_db'))
       {  $oldumask = umask(0); 

        mkdir('../img_db', 0775);
        umask($oldumask);
      }
      $i=0;
     $carousel='';
      foreach ($fv_images as $key => $value)
      { 
        if($value!='')
      {
        $img[$i++]=$value;
        if($i==3)
          break;
    }
      }

        
     $i=0;
 while($i++<3)
 {if(!isset($img[$i-1]))
  $img[$i-1]='../images/default.jpg';
  $sl=file_get_contents('projectsliderdiv.txt');
            
            
 if($i==1)$sl=str_replace('$active', "active", $sl);
     else $sl=str_replace('$active', "", $sl);
     $sl=str_replace('$src', 'img_db/image_'.($i).'.jpg', $sl);
 $carousel.=str_replace('$alt', 'image_'.($i), $sl);
         
 }

      


      //$result1 = mysqli_query($con,"SELECT * FROM fv_project_units where project_id=$projectid order by size");

      $plan=$row['site_plan'];
      $t5="1";
      $t6=0;
      $l="";
      $unit=array();
      $unit_1=array();
      $i=-1;
      $z='siteplan';
      //echo sizeof($fv_units);
      array_unshift($fv_units, array());
      //echo sizeof($fv_units);
       //print_r($fv_units);
      foreach ($fv_units as $key1 => $value) 

      {  //echo ">".$key." --<";
        $temp=$value;
        if(isset($temp['fp']))
        $key=$temp['fp'];
      else $key='';
       
        
        $imagename=$plan;
        $dest='../img_db/'.$imagename;
        $logo='../images/logo.png';
        if( $imagename!="" && strlen($imagename)-1!=stripos($imagename,"."))
        { 
          if(!file_exists($dest))
          {
          $src='http://prop.favistat.com/img/project_images/big/'.$imagename;
          $src1='http://prop.favistat.com/img/project_images/orgimg/'.$imagename;
          $src2='http://prop.favistat.com/img/project_images/original/'.$imagename;
          copy($src,$dest);

          if(!file_exists($dest))
          {
           copy($src1,$dest);
         }
         if(!file_exists($dest))
          {
           copy($src2,$dest);
         }

        if(file_exists($logo) && file_exists($dest))
           {addwatermark($dest,$logo,$dest);
         resize($dest,$dest, 3/2);
       }
       }
         if(file_exists($dest))
          {
            $imagename='img_db/'.$imagename;
            if($i!=-1)
            {
                $fpt=file_get_contents('floorplan.txt');
            $fpt=str_replace('$title', $t."- floorplan", $fpt);
            $fpt=str_replace('$href', $imagename, $fpt);
            $fpt=str_replace('$alt', $imagename, $fpt);
            $fpt=str_replace('$src', $imagename, $fpt);
              $l=$l.$fpt;
          }
          else
          {
            $fpt=file_get_contents('siteplan.txt');
            $fpt=str_replace('$title', $t."- siteplan", $fpt);
            $fpt=str_replace('$href', $imagename, $fpt);
            $fpt=str_replace('$alt', $imagename, $fpt);
            $fpt=str_replace('$src', $imagename, $fpt);
              $z=$fpt;
          }
          }

          $t6++;
        }

        $plan = $key;
        $t5++;
        
          

      if($i>=0)
      { echo "$i ";
       $unit[$i]=$temp;
       $unit_1[$i]=array($temp['size']);
     }
       $i++;

     }
     array_shift($fv_units);
     //print_r($fv_units);
     $cnt=--$i;
     $var['units']=$unit;
      $e="Project Specifications";
      $f="<div style=\"padding:10px\">".$row['specifications']."</div>";
      $i="Site Plan/Floor Plan";
     

    $list=array();
    //$result2 = mysqli_query($con,"select p.name,m.domain from fv_projects p left join fv_microsites m on p.id=m.project_id where builder_id=".$row['builder_id']." and m.domain<>'' ");
    foreach ($fv_micro as $key => $value) 
    { $row32=array('domain'=>$key,'name'=>$value);
      if($row32['domain']!='' || $row32['domain']!='null' || $row32['domain']!='NULL')
       array_push($list,$row32);
   }
 
    $lst='';
    $ii2=1;
    if(sizeof($list)>5)
      $ii2=3;
    $ii=ceil(sizeof($list)/3);
    for ($i1=0; $i1 < $ii2; $i1++) { 
     $lst.='<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">';
     if($ii2==1)
       $lst.='</div><div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">';
     for ($i2=0; $i2 <$ii ; $i2++) { 
      $row32=array_pop($list);
      $lst.='<a href="http://'.$row32['domain'].'" target="_blank" title="'.$row32['domain'].'">'.$row32['name'].'</a><br />';
    }
    $lst.='</div>';
  }
  if($row['builder_id']!='')
    $interlink ='<div class="row" style="background:#eaeaea;  padding:10px; text-align:center"> 
  '.$lst.'
  </div>';
  else
    $interlink='';

  $var['interlink']=$interlink;

  $var['size']=$unit_1;
  $var['number']= $cnt;
  $var['gac']=$googleac;
  $var['grc']=$googlerc;
  $var['projectid']=$projectid;
  $var['title']=$t;
  $var['hn']=$a;
  $var['hc']=  delete_all_between('<form', ">", $b,'');
  $var['an']=$e;
  $var['logo']=$projectlogo;
  $var['cty']=$city;
  $var['lct']=$location;
  $var['ac1']=$f;
  $var['fn']=$i;
  $var['fu']=$l;
  $var['su']=$z;
  $var['lat']=$lat;
  $var['lon']=$lon;
  $var['bhk']=$bhk;
  $var['phs']=$phase;
  $var['st']= $state;
 $var['carousel']=$carousel;
  $var['location']= $loc;
  $var['type']= $type;
  $var['price']= $price;
  $var['image']=$img;
  $var['url']="http://www.favista.com/".$row['canonical_url']."-P$projectid.html";
  $unit=$var['units'];
  $unittable="<table class=\"table table-hover\">";
  $unittable.="<tr>
  <th>UNIT TYPE</th>
  <th>SIZE</th>
  <th>PRICE/SFT</th>
  <th>PRICE* ₹</th>
  </tr>";
  for($i=0;$i<sizeof($unit);$i++)
  {
    $unittable.="<tr>
    <td>".$unit[$i]['unit_type']."</td>
    <td>".$unit[$i]['size']."</td>
    <td>".$unit[$i]['price_sft']."</td>

    <td>".(number_format(((floatval($unit[$i]['sale_price']))/10000000),2))." Cr</td>
    </tr>";
    //echo $unit[$i]['sale_price'];
  }
  $unittable.="</table>";
  $var['unittable']=$unittable;
  file_put_contents("data.json",json_encode($var));
  file_put_contents("project.json",json_encode($project));



}

if($count==1)
{

  echo "<script>top.location=\"../index.php\";alert(\"No such Project ID Found!!\");</script>";
header('location: ../../');
}

if($var['logo']!='')
$logo_1 = 'http://prop.favistat.com/img/project_images/project_logos/'.$var['logo'];
else $logo_1="../../image/logo.png";
$lg_db_1 = '../img_db/logo_1.jpg';
if(!file_exists("../img_db"))
{
mkdir("../img_db");
}
$i=0;
while ($k=array_pop($var['image']) ){
  if(strpos($k,'default.jpg')>0)
    $image_1=$k;
  else
$image_1 = 'http://prop.favistat.com/img/project_images/original/'.$k;
$img_db_1 = '../img_db/image_'.(++$i).'.jpg';
if(!file_exists($img_db_1))
{
  copy($image_1, $img_db_1);
}
if(!file_exists($img_db_1))
{
  copy('../images/default.jpg', $img_db_1);
}
}
if(!file_exists($lg_db_1))
copy($logo_1, $lg_db_1);

  header('location: ../');
}
}