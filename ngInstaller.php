<?php

/*
 *  Copyright (C) 2009-2011 VbID Verlagsbuero GmbH
 *  Author: Hilmar Runge
 *  ngwebsite.net
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
 
	
  class ngWebGet {
  
		var $devmode = false;
  
	function index($stage) {
		switch ($stage) {
			case '0';
				$this->pre();
				break;
			default:
				$xmlfile=$_SESSION['URLDL'].'modules/distros.xml';
				$xml = simplexml_load_file($xmlfile);
				if (is_object($xml)) {
					$cnt='<br />Step 2 <span class="hi">Please select a release:</span><br />';
					$n=0;
					foreach ($xml->distro as $distro) {
						$_SESSION['SDT'][$n]=(string)$distro->title;
						$_SESSION['SRL'][$n]=(string)$distro->release;
						$cnt.='Select <a href="./ngInstall.php?op=s21&amp;n='
						.	(string)$n.'">'
						.	$distro->title.'</a> ('.$distro->desc.') <br />';
						$n++;
					}
				} else {
					$cnt='<a href="./ngInstall.php?op=s21">continue</a>';
				}
			
				return $cnt;
				break;
		}
	}
	
	function pre($release)
	{
		echo '<img style="display:none;" src="ngtemp/img/ok.10.gif" alt="ok" />';
		echo '<img style="display:none;" src="ngtemp/img/oh.10.gif" alt="oh" />';
		echo '<img style="display:none;" src="ngtemp/img/no.10.gif" alt="no" />';
		echo '<img style="display:none;" src="ngtemp/img/ko.10.gif" alt="ko" />';
		echo '<img style="display:none;" src="ngtemp/img/ajax10red.gif" alt="..." />';
	
		unset($_SESSION['FG']);
		$_SESSION['FG'][SESSION_NAME]=array();
		
		$cnt='<div id="s21" style="display:none;">'.'da'.SESSION_NAME.'</div>';
		
		$cnt.='<table>';
		$zebra=false;
		$i=0;
		
		if (isset($_SESSION['SDT'][$release])) {
			$xmlfile=$_SESSION['URLDL'].'modules/distros.xml';
			$xml = simplexml_load_file($xmlfile);
			if (is_object($xml)) {
				foreach ($xml->distro as $distro) {
					if ($distro->title==$_SESSION['SDT'][$release]) {
						if (isset($distro->name)) {
							$_SESSION['DNAME']=(string)$distro->name;
						}
						foreach ($distro->modules->module as $mod) {
							$tgzs=array();
							if (isset($mod->parts)) {
								for ($n=1; $n <= $mod->parts; $n++) {
									$tgzs[]=$mod->name.'_'.str_replace('.','_',$mod->version).'.'.$n.'of'.$mod->parts.'.tar.gz';
								}
							} else {
								$tgzs[]=$mod->name.'_'.str_replace('.','_',$mod->version).'.tar.gz';
							}
							foreach ($tgzs as $tgz) {
								$_SESSION['FG'][SESSION_NAME][$i]=$tgz;
								$_SESSION['FG'][SESSION_NAME][$tgz]=(string)$mod->name;
								if ($zebra) {
									$cnt .= '<tr class="toggle1">';
									$zebra=false;
								} else {
									$cnt .= '<tr class="toggle2">';
									$zebra=true;
								}
								$cnt.='<td>'.$mod->name.'</td><td>'.$tgz.'</td><td><span id="msg'.$i.'" class="std"></span></td></tr>';
								$i++;
							}
						}
					}
				}
			}
		}
		$cnt.='</table>';
		return $cnt;
	}
	
	public function verCurrent($da) 
	{
		// BG
		$cnt='';
		list($subdir,$nop)=explode('ngInstall.php',$_SERVER['ORIG_PATH_TRANSLATED']);
		foreach ($_SESSION['FG'][$da] as $k=>$f) {
			if ($k < '999') {
				$cnt.=file_exists($subdir.$f)?'t':'f';
			}
		}
		return $cnt;
	}
	
	public function pickUp($da) 
	{
		// BG
		list($subdir,$nop)=explode('ngInstall.php',$_SERVER['ORIG_PATH_TRANSLATED']);
		$tgz=$_SESSION['FG'][$da][$_REQUEST['i']];
		$sf=$_SESSION['URLDL'].'modules/'.$_SESSION['FG'][$da][$tgz].'/'.$tgz;
		if (!file_exists($tgz)) {
			$cc=@copy($sf, $tgz);
			if ($cc) {
				$cnt='t'.$_REQUEST['i'];
			} else {
				$cnt='f'.$_REQUEST['i'];
			}
		} else {
			$cnt='t'.$_REQUEST['i'];
		}
		return $cnt;
	}
	
	public function checkUp($da) 
	{
		// BG
		list($subdir,$nop)=explode('ngInstall.php',$_SERVER['ORIG_PATH_TRANSLATED']);
		$tgz=$_SESSION['FG'][$da][$_REQUEST['i']];
		$cnt='n';
		// indi = a debug indicator 
		$indi='';
		$xmlvsn=false;
		if (preg_match('/_([0-9]*_[0-9]*_[0-9]*).*\.tar\.gz/',$tgz,$match)) {
			$xmlvsn=$match[1];
			$indi.='Vt';
		} else {
			$cnt='f';
			$indi.='Vf';
		}
		clearstatcache();
		if (file_exists($tgz)) {
		// -----------------
			$tgzmd5=@md5_file($tgz);
			if ($tgzmd5) {
				if (substr($tgz,0,4)=='base') {
					$part = explode('.',$tgz);
					if (isset($part[1])) {
						$nofm=$part[1];
					} else {
						$nofm='';
					}
					$xmlvsnm=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/base'.$nofm.'.'.$xmlvsn.'.check.xml';
					$xmlvsnf=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/base'.$nofm.'.check.'.$xmlvsn.'.xml';
					$xmlfile=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/base'.$nofm.'.check.xml';
				} else {
					$xmlvsnm=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/'
						.$_SESSION['FG'][SESSION_NAME][$tgz].'.'.$xmlvsn.'.check.xml';
					$xmlvsnf=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/'.'check.'.$xmlvsn.'.xml';
					$xmlfile=$_SESSION['URLCK'].'modules/'.$_SESSION['FG'][SESSION_NAME][$tgz].'/check.xml';
				}
				$xmlm = @simplexml_load_file($xmlvsnm);
				if (is_object($xmlm)) {
					$indi.='m';
					if (strtoupper($tgzmd5)==strtoupper((string)$xmlm->module->md5sum)) {
						$cnt='t';
						$indi.='t';
					} else {
						$indi.='f';
					}
				} else {
					$indi.='m0';
				}				
				if ($cnt<>'t') {
					$xmlv = @simplexml_load_file($xmlvsnf);
					if (is_object($xmlv)) {
						$indi.='v';
						if (strtoupper($tgzmd5)==strtoupper((string)$xmlv->module->md5sum)) {
							$cnt='t';
							$indi.='t';
						} else {
							$indi.='f';
						}
					} else {
						$indi.='v0';
					}
					if ($cnt<>'t') {
						$xml = @simplexml_load_file($xmlfile);
						if (is_object($xml)) {
							$indi.='c';
							if (strtoupper($tgzmd5)==strtoupper((string)$xml->module->md5sum)) {
								$cnt='t';
								$indi.='t';
							} else {
								$cnt='f';
								@unlink($tgz);
								$indi.='f';
							}
						} else {
							$indi.='c0';
						}
					}
				}
			}
		}
		$n=(int)count($_SESSION['FG'][$da]) / 2;
		return $cnt.$_REQUEST['i'].'--'.$n.'--'.$indi;
	}
	
	public function deComp($da) 
	{
		// BG
		list($subdir,$nop)=explode('ngInstall.php',$_SERVER['ORIG_PATH_TRANSLATED']);
		$tgz=$_SESSION['FG'][$da][$_REQUEST['i']];
		$cnt='f';
		if (file_exists($tgz)) {
			$tar=substr($tgz,0,-3);
			if (!file_exists($tar)) {
				$cnt='t';
				$fz=gzopen($tgz,'r');
				$fp=fopen($tar,'w');
				while (!gzeof($fz)) {
					fwrite($fp,gzgets($fz,4096),4096);
				}
				fclose($fp);
				gzclose($fz);
			} else {
				$cnt='n';
			}
			@unlink($tgz);
		}
		return $cnt.$_REQUEST['i'];
	}
	
	public function unTar($da) {
		// BG
		list($subdir,$nop)=explode('ngInstall.php',$_SERVER['ORIG_PATH_TRANSLATED']);
		$tgz=$_SESSION['FG'][$da][$_REQUEST['i']];
		$tar=substr($tgz,0,-3);
		$cnt='f';
		if (file_exists($tar)) {
			// a pear addition
			require_once 'ngtemp/pear/Tar.php';
			$tarO = new Archive_Tar($tar);
			$ar = @ $tarO->listContent();
			if ($ar) {
				foreach ($ar as $a) {
					// required to isolate filenames and to remove whitespaces
					$fn=rtrim($a['filename']);
					$sel[]=$fn;
					
					$flag=(int)$a['typeflag'];
					$cmod=(int)$a['mode'];
					if ($flag==5) {$be=(int)0x4000;}
					elseif ($flag==2) {$be=(int)0xa000;}
					elseif ($flag==0) {$be=(int)0x8000;}
					else {$be=(int)0x0000;}
					$perms=$be + $cmod;
					$p=0;
					$p += (($perms & 0x0100) ? 256 : 0);
					$p += (($perms & 0x0080) ? 128 : 0);
					$p += (($perms & 0x0040) ?
						  (($perms & 0x0800) ? 0 : 64 ) :
						  (($perms & 0x0800) ? 0 : 0));
					$p += (($perms & 0x0020) ? 32 : 0);
					$p += (($perms & 0x0010) ? 16 : 0);
					$p += (($perms & 0x0008) ?
						  (($p & 0x0400) ? 0 : 8 ) :
						  (($p & 0x0400) ? 0 : 0));
					$p += (($perms & 0x0004) ? 4 : 0);
					$p += (($perms & 0x0002) ? 2 : 0);
					$p += (($perms & 0x0001) ?
					(($perms & 0x0200) ? 0 : 1 ) :
					(($perms & 0x0200) ? 0 : 0));
					if ($flag==5) {
						@mkdir($fn);
						@chmod($fn,$p);
					}
				}
				$cc = @ $tarO->extractList($sel,'','');
				
				// extented verification
				$ok=$no=0;
				foreach ($sel as $isf) {
					if (file_exists($isf)) {
						$ok++;
					} else {
						$no++;
					}
				}
				if ($no == 0) {
					$cnt='t';
					$nn=$ok;
				} else {
					$nn=$no;
				}
			}
			@unlink($tar);
		}
		return $cnt.$_REQUEST['i'].'--'.$nn;
	}
	
	public function pro($da) 
	{
		if ($_SESSION['FG'][$da]) {
			$flist = array(	'ngGetInstaller.php',
							'ngInstaller.php',
							'ngInRq.php',
							'ngtemp/css/ngIn.css',
							'ngtemp/img/ajax10red.gif',
							'ngtemp/img/ok.10.gif',
							'ngtemp/img/oh.10.gif',
							'ngtemp/img/no.10.gif',
							'ngtemp/img/ko.10.gif',
							'ngtemp/js/jquery.js',
							'ngtemp/js/jquery.progressbar.js',
							'ngtemp/js/ngIn.js',
							'ngtemp/pear/Tar.php');
			if (!$this->devmode) {
				foreach ($flist as $fname) {
					@unlink($fname);
				}
				@rmdir('ngtemp/css/');
				@rmdir('ngtemp/img/');
				@rmdir('ngtemp/js/');
				@rmdir('ngtemp/pear/');
				@rmdir('ngtemp/');
				@rename('ngInstall.php','ngInstall.nop');
				
				if (file_exists('htaccess.txt')) {
					$co=array();
					$co=explode("\n",@file_get_contents('htaccess.txt'));
					array_unshift($co, 	'Order Allow,Deny', 'Allow from all', '');
					array_push($co, '',	'RewriteEngine On','',
										'RewriteCond %{REQUEST_FILENAME} !-d',
										'RewriteCond %{REQUEST_FILENAME} !-f',
										'RewriteRule ^.*$ index.php [L]','');
					@file_put_contents('.htaccess', implode("\n",$co));
				}
			}
		}
		
		$mycfg='mod/ngboost/conf/ngboost.jso';
		if (file_exists($mycfg)) {
			$jso=json_decode(stripslashes(file_get_contents($mycfg)),true);
		} else {
			$jso=array();
		}
		$jso['distro']=$_SESSION['DSERV'];
		if (isset($_SESSION['DNAME'])) {
			$jso['release']=$_SESSION['DNAME'];
		}
		$cc=file_put_contents('mod/ngboost/conf/ngboost.jso',addslashes(json_encode($jso)));
		
		unset($_SESSION['FG']);
		return 'fin';
	}
		
  } // end class
	
?>