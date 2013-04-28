<?php

    $default_wp_url = "http://wordpress.org/latest.zip";

	function download_remote($url , $save_path)
	{
		$f = fopen( $save_path , 'w+');

		$handle = fopen($url , "rb");

		while (!feof($handle))
		{
			$contents = fread($handle, 8192);
			fwrite($f , $contents);
		}

		fclose($handle);
		fclose($f);
	}
	
	function changePath( $path, $replace ) {
		if ( strpos ( $path, "wordpress" ) == 0 )
			$path = preg_replace('/^wordpress/', $replace, $path, 1);
		return $path;
	}

	function unzip($file, $path ){ 
		$zip = zip_open($file); 
		if(is_resource($zip)){ 
			$tree = ""; 
			while(($zip_entry = zip_read($zip)) !== false){ 
				echo "Unpacking ".zip_entry_name($zip_entry)."\n"; 
				if(strpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR) !== false){ 
					$last = strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR); 
					$dir = changePath( substr(zip_entry_name($zip_entry), 0, $last), $path ); 
					$file = substr(zip_entry_name($zip_entry), strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR)+1); 
					if(!is_dir($dir)){ 
						@mkdir($dir, 0755, true) or die("Unable to create $dir\n"); 
					} 
					if(strlen(trim($file)) > 0){ 
						$return = @file_put_contents( $dir."/".$file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry))); 
						if($return === false){ 
							die("Unable to write file $dir/$file\n"); 
						} 
					} 
				}else{ 
					file_put_contents( changePath( $file, $path ), zip_entry_read($zip_entry, zip_entry_filesize($zip_entry))); 
				} 
			} 
		}else{ 
			echo "Unable to open zip file\n"; 
		} 
	} 

	if ( $_POST['wp_url'] != NULL && $_POST['path'] != NULL ) {
		echo "will download ".$_POST['wp_url']." and unzip to ".$_POST['path'];
		mkdir( $_POST['path'] );
		download_remote( $_POST['wp_url'], "wordpress_latest.zip");
		unzip("wordpress_latest.zip", $_POST['path']);
	} else {
	   ?>
	   <form method="POST">
		   <p>
		   <label for="wp_url">Wordpress .zip source URL:</label>
		   <input type="text" size="60" id="wp_url" name="wp_url" value="<?php echo $default_wp_url; ?>" />
		   </p>
		   
		   <p>
		   <label for="path">Target directory name:</label>
		   <input type="text" size="60" id="path" name="path" value="wordpress" />
		   </p>
		   
		   <input type="submit" value="GO!" />
	   </form>
	   <?php
	}
	


?>
