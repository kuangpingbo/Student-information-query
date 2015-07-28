<?php
$time_start = microtime(true);
define('ROOT', dirname(__FILE__).'/');
define('MATCH_LENGTH', 0.1*1024*1024); //�ַ������� 0.1M �Լ����ã�һ�㹻�ˡ�
define('RESULT_LIMIT',100);

function my_scandir($path){//��ȡ�����ļ���ַ
        $filelist=array();
        if($handle=opendir($path)){
        while (($file=readdir($handle))!==false){
         if($file!="." && $file !=".."){
             if(is_dir($path."/".$file)){
                $filelist=array_merge($filelist,my_scandir($path."/".$file));
                 }else{
                  $filelist[]=$path."/".$file;
                 }
            }
        }
     }
    closedir($handle);
    return $filelist;
}

function get_results($keyword){//��ѯ
    $return=array();
    $count=0;
    $datas=my_scandir(ROOT."kieoidfrwq!!1123@#fewf"); //���ݿ��ĵ�Ŀ¼
    if(!empty($datas))foreach($datas as $filepath){
        $filename = basename($filepath);
        $start = 0;
        $fp = fopen($filepath, 'r');
          while(!feof($fp)){
                fseek($fp, $start);
                $content = fread($fp, MATCH_LENGTH);
                $content.=(feof($fp))?"\n":'';
                $content_length = strrpos($content, "\n");
                $content = substr($content, 0, $content_length);
                $start += $content_length;
                $end_pos = 0;
                while (($end_pos = strpos($content, $keyword, $end_pos)) !== false){
                    $start_pos = strrpos($content, "\n", -$content_length + $end_pos);
                    $start_pos = ($start_pos === false)?0:$start_pos;
                    $end_pos = strpos($content, "\n", $end_pos);
                    $end_pos=($end_pos===false)?$content_length:$end_pos;
                    $return[]=array(
                       'f'=>$filename,
                       't'=>trim(substr($content, $start_pos, $end_pos-$start_pos))
                         );
                    $count++;
                    if ($count >= RESULT_LIMIT) break;
                  }
                unset($content,$content_length,$start_pos,$end_pos);
                if ($count >= RESULT_LIMIT) break;
                  }
        fclose($fp);
       if ($count >= RESULT_LIMIT) break;
     }
     return $return;
}
if(!empty($_POST)&&!empty($_POST['q'])){
    set_time_limit(0);				//���޶��ű�ִ��ʱ��
    $q=strip_tags(trim($_POST['q']));
    $results=get_results($q);
    $count=count($results);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>���ݸ�ְԺѧ����ѯ</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
    <style type="text/css">
    body,td,th{
    color: #FFF;
}
    a:link{
    color: #0C0;
    text-decoration: none;
}
    body{
    background-color: #F0F0F0;
}
    a:visited {
    text-decoration: none;
    color: #999;
}
a:hover{
    text-decoration: none;
}
a:active{
    text-decoration: none;
    color: #F00;
}
    </style>
<script>
<!--
function check(form){
if(form.q.value==""){
  alert("Not null��");
  form.q.focus();
  return false;
  }
}
-->
</script>
</head>
    <body>
    <div id="container"><div id="header"><a href="http://tieba.baidu.com/home/main?un=%E4%B8%80%E7%94%9F%E5%8F%AA%E7%88%B1%E4%B8%89%E6%9C%88%E8%8A%B1&ie=utf-8&fr=frs" ><h1>���ݸ�ְԺѧ����Ϣ��ѯ</h1></a></div><br /><br />
<form name="from" action="index.php" method="post">
    <div id="content">
    <div id="create_form">
    <label><input class="inurl" size="26" id="unurl" name="q" value="<?php echo !empty($q)?$q:''; ?>"/></label>
    <p class="ali"><label for="alias">����ؼ���:</label><span>�༶�����֣����֤��Ϣ...</span></p>
    <p class="but"><input onClick="check(form)" type="submit" value="��ѯ" class="submit" /></p>
    </form>
    </div>
  <?php
       if(isset($count))
       {
         echo 'Get ' .$count .' results,&nbsp;&nbsp;cost ' . (microtime(true) - $time_start) . " seconds";
         if(!empty($results)){
         echo '<ul>';
         foreach($results as $v){
         echo '<li>From_['.$v['f'].']_Datas <br />Content: '.$v['t'].'</li><br />';
           }
         echo '<br /><br /><font color=#666666><li>ѧ����-�༶-ѧ��-����-�Ա�-���֤��<br /></li></font>';
         echo '</ul>';
            }
         echo '</ul>';
         }
         ?>
<!--<div id="nav">
<ul><li class="current"><a href="#">Search Data</a></li><li><a href="html/about.html" target="_blank">Abouts</a></li></ul>
</div>
<div id="footer">
<p>Social Engineering Data &copy;2010-2013 Powered By <a href="#" target="_blank">JKS_��ӭ��<a></p><div style="display:none">
</div>-->
</div>
</body>
</html>