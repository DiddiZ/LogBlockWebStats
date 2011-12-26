<?php
	$username = preg_replace('/[^a-zA-Z0-9_]/', '', $_SERVER['QUERY_STRING']);
	$file = "players/$username.png";
	header('Content-Type: image/png');
	header('Content-Disposition: inline; filename={$username}.png');
	if(file_exists($file) && filemtime($file) > time() - 86400)
		readfile($file);
	else {
		$src = @imagecreatefrompng("http://s3.amazonaws.com/MinecraftSkins/{$username}.png");
		if(!$src) {
			if(file_exists($file))
				readfile($file);
			else
				readfile('players/player.png');
		} else {
			$img = imagecreatetruecolor(16, 32);
			imagealphablending($img, false);
			imagesavealpha($img, true);
			imagefill($img, 0, 0, imagecolorallocatealpha($img, 255, 0, 255, 127));
			imagecopy($img, $src, 4, 0, 8, 8, 8, 8);
			imagecopy($img, $src, 4, 8, 20, 20, 8, 12);
			imagecopy($img, $src, 0, 8, 44, 20, 4, 12);
			imagecopyresampled($img, $src, 12, 8, 47, 20, 4, 12, -4, 12);
			imagecopy($img, $src, 4, 20, 4, 20, 4, 12);
			imagecopyresampled($img, $src, 8, 20, 7, 20, 4, 12, -4, 12);
			imagealphablending($img, true);
			imagecopy($img, $src, 4, 0, 40, 8, 8, 8);
			imagepng($img, $file, 9);
			imagepng($img);
		}
	}
?>