<?php

	//10>設置session,必須處於腳本最頂部
	session_start();

	$image = imagecreatetruecolor(100, 30); //1>設置驗證碼圖片大小的函數
	//5>設置驗證碼顏色 imagecolorallocate(int im, int red, int green, int blue);
	$bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
	//6>區域填充 int imagefill(int im, int x, int y, int col) (x,y) 所在的區域着色,col 表示欲塗上的顏色
	imagefill($image, 0, 0, $bgcolor);
	//10>設置變量
	$captcha_code = "";
	//7>生成隨機數字
	for($i=0;$i<4;$i++){
	//設置字體大小
	$fontsize = 6; 
	//設置字體顏色，隨機顏色
	$fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120)); //0-120深顏色
	//設置數字
	$fontcontent = rand(0,9);
	//10>.=連續定義變量
	$captcha_code .= $fontcontent; 
	//設置座標
	$x = ($i*100/4)+rand(5,10);
	$y = rand(5,10);

	imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
	}
	//10>存到session
	$_SESSION['authcode'] = $captcha_code;
	//8>增加干擾元素，設置雪花點
	for($i=0;$i<200;$i++){

	//設置點的顏色，50-200顏色比數字淺，不干擾閲讀
	$pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200)); 
	//imagesetpixel — 畫一個單一像素
	imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);
	}
	//9>增加干擾元素，設置橫線
	for($i=0;$i<4;$i++){
	//設置線的顏色
	$linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
	//設置線，兩點一線
	imageline($image,rand(1,99), rand(1,29),rand(1,99), rand(1,29),$linecolor);
	}

	//2>設置頭部，image/png
	header('Content-Type: image/png');
	//3>imagepng() 創建png圖形函數
	imagepng($image);
	//4>imagedestroy() 結束圖形函數 銷燬$image
	imagedestroy($image);

?>