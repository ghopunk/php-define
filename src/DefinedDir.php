<?php

namespace ghopunk\Helpers;

use ghopunk\Helpers\Encryption;

class DefinedDir{
	protected $mainDir;
	protected $mainPath;
	protected $resultName;
	protected $resultFolderName;
	protected $resultSubFolderName;
	
	private $dir 	= array();
	private $path 	= array();
	private $name 	= array();
	private $file 	= array();
	
	private $encryption;
	private $uniqueCode;
	
	public function  __construct( $dir, $path, $resultName = null, $folderName = null, $subFolderName = null ){
		$this->setMainDir( $dir );
		$this->setMainPath( $path );
		$this->setResultName( $resultName );
		$this->setResultFolderName( $folderName );
		$this->setResultSubFolderName( $subFolderName );
		$this->setResult();
	}
	public function clearName( $name ){
		return preg_replace( '/[^0-9a-z_-]/i', '', $name );
	}
	protected function setMain(){
		$dir		= $this->getMainDir();
		$path		= $this->getMainPath();
		$this->setDir( 'main', $dir );
		$this->setPath( 'main', $path );
	}
	protected function setResult(){
		$this->setMain();
		$name 		= $this->getResultName();
		$clearname 	= $this->clearName( $name );
		$dir 		= $this->getDir( 'main' ) . DIRECTORY_SEPARATOR . $clearname;
		$path 		= $this->getPath( 'main' ) . '/' . $clearname;
		$this->setDir( 'result', $dir );
		$this->setPath( 'result', $path );
	}
	protected function setUser(){
		$this->setResult();
		$dir 			= $this->getDir( 'result' );
		$path 			= $this->getPath( 'result' );
		if( !empty( $this->getResultFolderName() ) ){
			$name 		= $this->getResultFolderName();
			$clearname 	= $this->clearName( $name );
			$dir 		= $dir . DIRECTORY_SEPARATOR . $clearname;
			$path 		= $path . '/' . $clearname;
			$subName 	= $this->getResultSubFolderName();
			if( !empty( $subName ) ){
				$sub 	= $this->clearName( $subName );
				$dir 	= $dir . DIRECTORY_SEPARATOR . $subName;
				$path 	= $path . '/' . $sub;
			}
		}
		$this->setDir( 'userDir', $dir );
		$this->setPath( 'userDir', $path );
	}
	
	public function setMainDir( $dir ){
		$dir 			= $this->untrailingslashit( $dir );
		$this->mainDir 	= $dir;
	}
	public function getMainDir(){
		return $this->mainDir;
	}
	
	public function setMainPath( $path ){
		$path 			= $this->untrailingslashit( $path );
		$this->mainPath	= $path;
	}
	public function getMainPath(){
		return $this->mainPath;
	}
	
	public function setResultName( $name ){
		if( !empty( $this->getUniquecode() ) ){
			$uniq = substr( md5( $this->getUniquecode() ), 0, 4 );
			$name = $name . '-' . $uniq;
		}
		$this->resultName = $name;
		$this->setResult();
	}
	public function getResultName(){
		return $this->resultName;
	}
	
	public function setResultFolderName( $name ){
		$this->resultFolderName = $name;
		$this->setResult();
	}
	public function getResultFolderName(){
		return $this->resultFolderName;
	}
	
	public function setResultSubFolderName( $subName ){
		$this->resultSubFolderName = $subName;
		$this->setResult();
	}	
	public function getResultSubFolderName(){
		return $this->resultSubFolderName;
	}
	
	public function setName( $name ){
		$clearname 			= $this->clearName( $name );
		$this->name[$name]	= $clearname;
	}
	public function getName( $name ){
		if( isset( $this->name[$name] ) ){
			return $this->name[$name];
		} else {
			return false;
		}
	}
	
	public function setDir( $name, $value ){
		$value 				= $this->untrailingslashit( $value );
		$this->dir[$name] 	= $value;
	}
	public function getDir( $name='main' ){
		if( empty( $name ) ){
			return false;
		} elseif( isset( $this->dir[$name] ) ){
			$dir = $this->dir[$name];
		} else {
			$this->setUser();
			$userDir = $this->dir['userDir'];
			$userDir = $this->untrailingslashit( $userDir );
			if( empty( $this->getName( $name ) ) ){
				$this->setName( $name );
			}
			$dir	= $userDir . DIRECTORY_SEPARATOR . $this->getName( $name );
		}
		$dir	= $this->untrailingslashit( $dir );
		return $dir;
	}
	
	public function setPath( $name, $value ){
		$value 				= $this->untrailingslashit( $value );
		$this->path[$name] 	= $value;
	}
	public function getPath( $name='main' ){
		if( empty( $name ) ){
			return false;
		} elseif( isset( $this->path[$name] ) ){
			$path = $this->path[$name];
		} else {
			$this->setUser();
			$userDir = $this->path['userDir'];
			$userDir = $this->untrailingslashit( $userDir );
			if( empty( $this->getName( $name ) ) ){
				$this->setName( $name );
			}
			$path = $userDir . '/' . $this->getName( $name );
		}
		$path = $this->untrailingslashit( $path );
		return $path;
	}
	
	public function setFile( $name, $value ){
		$this->file[$name] = $value;
	}
	public function getFile( $name ){
		if( isset( $this->file[$name] ) ){
			return $this->file[$name];
		} else {
			return false;
		}
	}
	
	public function createDir(){
		if( !empty( $this->dir ) && is_array( $this->dir ) ){
			foreach ( $this->dir as $dir ) {
				$this->mkdir_r( $dir );
			}
		}
	}
	
	public function trailingslashit_dir( $string ) {
		return $this->untrailingslashit( $string ) . DIRECTORY_SEPARATOR;
	}
	public function trailingslashit( $string ) {
		return $this->untrailingslashit( $string ) . '/';
	}
	public function untrailingslashit( $string ) {
		return rtrim( $string, '/\\' );
	}
	
	public function setEncryption( $encryption ){
		$this->encryption = $encryption;
	}
	public function getEncryption(){
		if( empty( $this->encryption ) && class_exists( 'Encryption' ) ) {
			$encryption = new Encryption;
			$this->setEncryption( $encryption );
		}
		return $this->encryption;
	}
	public function setUniquecode( $uniqueCode ){
		if( method_exists( $this->getEncryption(),'setUniquecode' ) ){
			$this->getEncryption()->setUniquecode( $uniqueCode );
		}
	}
	public function getUniquecode(){
		if( method_exists( $this->getEncryption(),'getUniquecode' ) ){
			return  $this->getEncryption()->getUniquecode();
		}
		return false;
	}
	
	public function checkDirFile( $file, $rights=0777){
		$dir = dirname( $file );
		if( !is_dir( $dir ) ){
			$this->mkdir_r( $dir, $rights);
		}
		return true;
	}
	public function mkdir_r( $dir, $rights=0777 ){
		if( !is_dir( $dir ) ){
			$dir 			= str_replace( '/', DIRECTORY_SEPARATOR, $dir );
			$target_parent 	= $this->untrailingslashit( $dir );
			while ( '.' !== $target_parent && ! is_dir( $target_parent ) && dirname( $target_parent ) !== $target_parent ) {
				$target_parent = dirname( $target_parent );
			}
			$folder_parts 	= substr( $dir, strlen( $target_parent ) + 1 );
			$dirs 			= explode( DIRECTORY_SEPARATOR , $folder_parts);
			foreach ( $dirs as $key=>$part) {
				$target_parent = $target_parent . DIRECTORY_SEPARATOR . $part;
				if ( !is_dir( $target_parent) ){
					if(@mkdir( $target_parent, $rights) ){
						file_put_contents( $target_parent. DIRECTORY_SEPARATOR . 'index.php', $this->createIndex() );
						file_put_contents( $target_parent. DIRECTORY_SEPARATOR .'.htaccess', 'deny from all' );
					}
				}
			}
		}
	}
	public function createIndex(){
		$text = <<<EOF
<?php
header("HTTP/1.0 403 Forbidden");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>403 Forbidden</title>
<meta content="NOINDEX,NOFOLLOW" name="ROBOTS">
</head>
<body>
<h1>Forbidden</h1>
<p>You don't have permission to access <?php if( isset(\$_SERVER["REQUEST_URI"] ) ) echo \$_SERVER["REQUEST_URI"].' on' ;?> this server.<br></p><hr>
<?php if( isset(\$_SERVER["SERVER_SIGNATURE"] ) ) echo \$_SERVER["SERVER_SIGNATURE"];?>
</body>
</html>
EOF;
	return $text;
	}
}
