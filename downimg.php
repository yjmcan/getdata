<?php
/*
*用curl方法获取网页数据
*正则获取目标数据
*pcntl_fork开启多线程
*本例是获取图片并保存到本地
*/

function getone($geturl){

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $geturl);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
  curl_setopt($ch, CURLOPT_HEADER, 0); //不返回头文件 0
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出 1

  $data = curl_exec($ch);
  curl_close($ch);

  //正则获取图片
  $ru = "/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
  preg_match_all($ru, $data, $outdata);

  $urls = $outdata[1];

  foreach ($urls as $url) {

    $filename = '';
    $savefile = dirname(__FILE__) .'/img/';
    $imgArr = array('gif', 'bmp', 'png', 'ico', 'jpg', 'jpeg');

    if(!$url) return false;

    if(!$filename){
      $ext_one = explode('.', $url);
      $ext = end($ext_one);
      if(!in_array($ext, $imgArr)) return false;
      $filename = date("Y-m-d H:i:s").'.'.$ext;
    }

     if(!is_dir($savefile)) mkdir($savefile, 0777);
     if(!is_readable($savefile)) chmod($savefile, 0777);

    $filename = $savefile.$filename;

    ob_start();
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);

    $fp2 = @fopen($filename, "a");
    fwrite($fp2, $img);
    fclose($fp2);
  }

}

//开线程
for ($i=0; $i<11; $i++){

  if (!function_exists('pcntl_fork'))
    die('it is not work');
  $k = $i+1;
  $k2 = $i+10;
  $geturl = "http://example.com?page=".$k;
  $geturl2 = "http://example.com?page=".$k2;
  $pid = pcntl_fork();
  if($pid == -1){
    die("你创建子进程失败了，哈哈哈"); //错误处理，创建子进程失败时返回-1
  } elseif ($pid) {
    //父进程会得到子进程号，所以这里是父进程的逻辑
    getone($geturl);
    pcntl_wait($status); //等待子进程中断
  }else {
    //子进程得到的pid为0，所以这里是子进程执行的逻辑
    getone($geturl2);
    exit($i);
  }

}
