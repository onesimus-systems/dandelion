<?php
if (!function_exists('file_put_contents'))
{
	function file_put_contents($path, $data, $properties)
	{
		$filemode = 'w';
		
		if ($properties == 2 || $properties == 3 || $properties == 10 || $properties == 11)
		{
			$filemode = 'a';
		}
		
		$handle = fopen($path, $filemode);
		fwrite($handle, $data);
		fclose($handle);
	}
}