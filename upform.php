<?php
/*
*
*模拟登陆
*post提交数据
*或批量提交数据
*/

//模拟登陆
function login($logurl, $logdata, $save_cookie){
  $ch01 = curl_init();

  curl_setopt($ch01, CURLOPT_URL, $logurl); //登陆地址
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
  curl_setopt($ch01, CURLOPT_HEADER, 0); //不返回头文件 0
  curl_setopt($ch01, CURLOPT_RETURNTRANSFER, 1); //不直接输出 1
  curl_setopt($ch01, CURLOPT_POST, 1); //开启post提交 1
  curl_setopt($ch01, CURLOPT_POSTFIELDS, http_build_query($logdata)); //用户名密码之类
  curl_setopt($ch01, CURLOPT_COOKIEJAR,  $save_cookie);
  curl_setopt($ch01, CURLOPT_COOKIEFILE, $save_cookie); //保存cookie

  curl_exec($ch01);
  curl_close($ch01);
}

//提交表单
function upform($formurl, $save_cookie, $formdata){
  $ch02 = curl_init();

  curl_setopt($ch02, CURLOPT_URL, $formurl); //提交表单地址
  curl_setopt($ch02, CURLOPT_HEADER, 0);
  curl_setopt($ch02, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch02, CURLOPT_POST, TRUE);
  curl_setopt($ch02, CURLOPT_COOKIEFILE, $save_cookie); //带上cookie提交
  curl_setopt($ch02, CURLOPT_POSTFIELDS, http_build_query($formdata)); //提交的数据

  curl_exec($ch02);
  curl_close($ch02);
}


//执行
$logurl = ""; //登陆网址
$formurl = ""; //提交网址

$save_cookie = dirname(__FILE__) .'/cookie123.txt'; //cookie保存地址
$logdata = array (
  'username' => '', // 用户名
  'password' => '', //密码
);
$formurl = array (
  'something' => '', // 提交数据
);

login($logurl, $logdata, $save_cookie);
upform($formurl, $save_cookie, $formdata);



//批量提交数据，提交txt文件
// $file = dirname(__FILE__) .'/updata.txt';
// $content = file_get_contents($file);
// $formalldata = explode("\r\n", $content); //读取txt文件每行数据
//
//
// for($i=0; $i<count($formalldata);$i++)
// {
//   //echo $formalldata[$i].'<br/>';
//   $formdata = array(
//     'one' => $formalldata[$i],
//     'two' => $formalldata[$i+1],
//   );
//   login($logurl, $logdata, $save_cookie); //模拟登陆
//   upform($formurl, $save_cookie, $formdata); //提交表单
//
//   $i = $i+2;
// }
