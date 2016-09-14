<?php
/*
*用curl方法获取网页数据
*正则获取目标数据
*pcntl_fork开启多线程
*本例是获取网页内特定链接
*/
function getone($geturl){

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $geturl);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
  curl_setopt($ch, CURLOPT_HEADER, 0); //不返回头文件 0
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出 1

  $data = curl_exec($ch);
  curl_close($ch);

  //正则获取<td>标签内的<a>标签链接及内容
  $ru = "/<td style=\"text-align:left;\"><a href=\"(.*)\"><span class=\"bg-orange\"><\/span>(.*)<\/a><\/td>/";
  preg_match_all($ru, $data, $outdata);

  //var_dump($outdata);  //可以输出看看获取到的内容
  $urls = $outdata[1]; //想要的特定内容

foreach ($urls as $url) {
  $content = $url."\n";   //以下是把数据写入txt文件并且每条数据都换行

  $txturl = dirname(__FILE__).'data.txt';
  $file = @fopen($txturl, "a");
  fwrite($file, $content);
  fclose($file);

}

}

//开线程
for ($i=1; $i<122; $i++){

  if (!function_exists('pcntl_fork'))
    die('it is not work');
  $k = $i+1;
  $k2 = $i+121;
  $geturl = "https://www.example.com/page/".$k;
  $geturl2 = "https://www.example.com/page/".$k2;
  $pid = pcntl_fork();
  if($pid == -1){
    //错误处理，创建子进程失败时返回-1
    die("你创建子进程失败了，哈哈哈");
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
