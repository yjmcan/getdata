<?php
/*
*
*获取zimuzu.tv的电影信息
*
*/
function getinf($geturl){

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $geturl);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
  curl_setopt($ch, CURLOPT_HEADER, 0); //不返回头文件 0
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出 1

  $data = curl_exec($ch);
  curl_close($ch);

  //正则获取电影标题、链接、类型及图片
  $ru = "/<div class=\"resource-showlist\">[\s\S]*<\/div>/";
  preg_match_all($ru, $data, $outdata);

  $outdata1 = $outdata[0][0];

  //获取到标题、类型
  $ru1 = "/<dt class=\"f14\"><strong><a href=\"(.*)\">(.*)<\/a><\/strong><font class=\"f4\">(.*)<\/font><\/dt>/";
  preg_match_all($ru1, $outdata1, $outurl);
  $outurlis = $outurl[1];
  $outurltitle = $outurl[2];

  //获取到url
  $ru2 = "/<p>【类型】(.*)<\/p>/";
  preg_match_all($ru2, $outdata1, $outp);
  $outptype = $outp[1];

  //正则获取图片
  //<img src="http://tu.rrsub.com/ftp/2015/1229/m_e27c6ec241e4cb85a9bbb87d475d1373.jpg" />
  $outdata2 = preg_replace('/m_/', '', $outdata1);
  $ru3 = "/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
  preg_match_all($ru3, $outdata2, $outimg);
  preg_match_all($ru3, $outdata1, $outimgaa);

  $num = count($outptype);
for ($i=0; $i<=$num; $i++){

  //以下是将数据写入数据库以及保存图片到本地
  $outimgurl = $outimg[1][$i];
  $outimgurlaa = $outimgaa[1][$i];
  $outimgname = explode('m_', $outimgurlaa, 2);
  //echo $outimgurl."\n";

  $one = $outurltitle[$i];
  $two = $outurlis[$i];
  $three = $outimgname[1];
  $four = $outptype[$i];
  // echo $one;
  // echo $two;
  // echo $three;
  // echo $four;
  //写到数据库
  if(!empty($one)){
  $dsn = "mysql:host=localhost;dbname=zimuzu";
  $db = new PDO($dsn, 'root','123456');
  $count = $db->exec("INSERT INTO list SET list_title ='$one',list_url='$two',list_imgurl='$three',list_type='$four'");
  echo $count;
  $db = null;
  }

  //保存图片到本地

  $imgArr = array('gif','bmp','png','ico','jpg','jepg');
  if(!$outimgurl) return false;
  $filename = $outimgname[1];
  if(!$filename) {
      $ext=strtolower(end(explode('.',$outimgurl)));
      if(!in_array($ext,$imgArr)) return false;
      $filename=date("Y-m-d H:i:s").'.'.$ext;
    }
  $savefile = dirname(__FILE__) .'/img3/';
  $filename = $savefile.$filename;
  if(!is_dir($savefile)) mkdir($savefile, 0777);
  if(!is_readable($savefile)) chmod($savefile, 0777);
  ob_start();
  readfile($outimgurl);
  $img = ob_get_contents();
  ob_end_clean();
  $size = strlen($img);

  $fp2 = @fopen($filename, "a");
  if(!empty($fp2)){
  fwrite($fp2, $img);
  fclose($fp2);
  }

}

}

$parentPid = posix_getpid();
$childList = array();
$pid = pcntl_fork();
if ( $pid == -1) {
    // 创建失败
    exit("fork progress error!\n");
} else if ($pid == 0) {
    // 子进程执行程序
    $repeatNum = 208;
    for ( $i = 1; $i <= $repeatNum; $i++) {
        $pid = posix_getpid();
        $geturl = "http://www.zimuzu.tv/eresourcelist?page=".$i."&channel=movie&area=&category=&format=&year=&sort=";
        getinf($geturl);
        $rand = rand(1,3);
        sleep($rand);
    }
    exit("({$pid})child progress end!\n");
} else {
    // 父进程执行程序
    $pid = posix_getpid();
    $repeatNum = 417;
    for ( $i = 209; $i <= $repeatNum; $i++) {
      $pid = posix_getpid();
      $geturl = "http://www.zimuzu.tv/eresourcelist?page=".$i."&channel=movie&area=&category=&format=&year=&sort=";
      getinf($geturl);
      $rand = rand(1,3);
      sleep($rand);
    }
    exit("({$pid})papa progress end!\n");
    $childList[$pid] = 1;
}
// 等待子进程结束
pcntl_wait($status);
echo "({$parentPid})main progress end!";






 ?>
