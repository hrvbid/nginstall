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
 

	define('BR','<br />');
	define('OK',' <span style="color:#0a0;">==> OK</span>');
	define('OH',' <span style="color:#f90;">==> OH</span>');
	define('KO',' <span style="color:#a00;">==> KO</span>');
	define('NOCO', ' not correctable');
	define('OHCO', ' try to correct');
	
	define('MYVSN','1.0.12');	// 2012.10.09
	
	define('NGINSTALL','ngInstall.php');
	
	define('ATSF',  'http://downloads.sourceforge.net/project/ngwebsite/ngInstaller/');
	define('BYSF',  'http://downloads.sourceforge.net/project/ngwebsite/phpWebSite/');
	define('ATNGWS','http://ngwebsite.net/distro/ngInstaller/');
	define('BYNGWS','http://ngwebsite.net/distro/download/phpwebsite/');
	define('ATASU', 'http://phpwebsite.appstate.edu/downloads/ngInstaller/');
	define('BYASU', 'http://phpwebsite.appstate.edu/downloads/');
	
	define('ATME',	'http://ngwebsite.net/distro/download/');

	if (!defined(SESSION_NAME)) {
		define(SESSION_NAME,'a'.md5($_SERVER['SERVER_ADDR'].$_SERVER['REMOTE_ADDR']));
		session_name(SESSION_NAME);
		session_start();
	}
	
	//error_reporting(E_ALL);
	error_reporting(-1);
	
	clearstatcache();
	//
	$ngpre = new ngPrepare;
	
	if (isset($_REQUEST['da']) && isset($_REQUEST['xaop'])) {
		$da = substr($_REQUEST['da'],2);
		if (SESSION_NAME == $da) {
			$ngpre->s22();
		}
	} else {
		$ngpre->main();
	}
	
	//

	class ngPrepare
	{
		var $devmode = false;
		var $diagok=false;
	
		public function main()
		{
			$this->s0();
			echo $this->t01();
			
			$op='';
			if (isset($_REQUEST['op'])) {
				preg_match('/^s1|s2|s21$/',$_REQUEST['op'])===1 ? $op=$_REQUEST['op'] : $op='';
			}
	
			switch ($op) {
				case 's1':
					echo $this->t03(false);
					$cc=$this->s1();
					echo $this->t04($cc);
					echo $this->t09();
					break;
				case 's2':
					echo $this->t03(false);
					echo $this->t04(false);
					echo $this->s2();
					echo $this->t09();
					break;
				case 's21':
					echo $this->t03(false);
					echo $this->t04(false);
					echo $this->s21();
					echo $this->t09();
					break;
				default:
					$diagcnt = $this->diag();
					echo $this->t03($this->diagok);
					echo $this->t04(false);
					echo $diagcnt;
					echo $this->t09();
			}
		}
	
		protected function s0()
		{
			$cnt='';
			$xmlfile=ATNGWS.'check.xml';
			$xml = simplexml_load_file($xmlfile);
			$ok=false;
			if (is_object($xml)) {
				$mymd5=md5_file(NGINSTALL);
				if (((string)$xml->module->version === MYVSN)
				&& (strtoupper($mymd5) === strtoupper((string)$xml->module->md5sum)) ) {
				$ok=true;
				}
			}
			if ($this->devmode) {
				$ok=true;
			}
			if (!$ok) {	
				$cnt.='Sorry, you are using an older version of me or I\'m broken'.BR
				.	'will provide the current version from '.ATME.NGINSTALL.BR;
				$cc=@copy(ATME.NGINSTALL, NGINSTALL);
				$cnt.=BR.'<b>Please reload the screen.</b>';
				
				echo $cnt;
				exit;
			}
		}
		
		protected function s1()
		{
			$cc=$this->devmode;
			preg_match('/^apws|spws|ngws$/',$_REQUEST['src'])===1 ? $src=$_REQUEST['src'] : $src='';
			switch ($src) {
				case 'apws':
					$_SESSION['URLDLPRE']=ATASU;
					$_SESSION['URLDL']=BYASU;
					$_SESSION['URLCK']=BYASU;
					if (!$this->devmode || !file_exists('ngGetInstaller.php')) {
						$cc=@copy(ATASU.'ngGetInstaller.php', 'ngGetInstaller.php');
					}
				break;
				case 'spws':
					$_SESSION['URLDLPRE']=ATSF;
					$_SESSION['URLDL']=BYSF;
					$_SESSION['URLCK']=BYSF;
					if (!$this->devmode || !file_exists('ngGetInstaller.php')) {
						$cc=@copy(ATSF.'ngGetInstaller.php', 'ngGetInstaller.php');
					}
				break;
				case 'ngws':
					$_SESSION['URLDLPRE']=ATNGWS;
					$_SESSION['URLDL']=BYNGWS;
					$_SESSION['URLCK']=BYNGWS;
					if (!$this->devmode || !file_exists('ngGetInstaller.php')) {
						$cc=@copy(ATNGWS.'ngGetInstaller.php', 'ngGetInstaller.php');
						@mkdir('ngtemp/img');
						@copy($_SESSION['URLDLPRE'].'ngtemp/img/ajax11square.gif', 'ngtemp/img/ajax11square.gif');
					}
				break;
			}
			$_SESSION['DSERV']=$src;
			return $cc;
		}
		
		protected function s2()
		{
			$cnt='';
			if (file_exists('ngGetInstaller.php')) {
				require 'ngGetInstaller.php';
				$nggetin = new ngGetInstaller;
				$cnt.=$nggetin->wgetInstaller();
				
				if (file_exists('ngInstaller.php')) {
					require 'ngInstaller.php';
					$wget = new ngWebGet;
					$cnt= $wget->index('').BR.$cnt;
				} else {
					$cnt.= 'Fatal: step 2 finished unsuccessfully (gave up) '.KO;
				}
			}
			return '<pre class="con con2">'.$cnt.'</pre>';
		}
		
		protected function s21()
		{
			$cnt='';
			if (isset($_REQUEST['n'])) {
				$release=(string)$_REQUEST['n'];
			} else {
				$release='';
			}
			if (file_exists('ngInstaller.php')) {
				require 'ngInstaller.php';
				$wget = new ngWebGet;
				$cnt.='<div id ="probara"><div class="text"><span class="text">&nbsp;</span></div></div>'
				. 	'<div id ="probarc"><div class="text"><span class="text">&nbsp;</span></div></div>'
				. 	'<div class="con con2">'
				. 	$wget->pre($release)
				. 	'</div>';
			}
			return $cnt;
		}

		public function s22()
		{
			if (isset($_REQUEST['da']) && isset($_REQUEST['xaop'])) {
				$da = substr($_REQUEST['da'],2);
				if (SESSION_NAME == $da) {
					require 'ngInstaller.php';
					$install = new ngWebGet;
					switch ($_REQUEST['xaop']) {
						case 'a':
							echo $install->verCurrent($da);
						break;
						case 'A':
							echo $install->pickUp($da);
						break;
						case 'c':
							echo $install->checkUp($da);
						break;
						case 'd':
							echo $install->deComp($da);
						break;
						case 'e':
							echo $install->unTar($da);
						break;
						case 'f':
							echo $install->pro($da);
						break;
					}
				}
			}
		}
		
		protected function t01()
		{
			$cnt='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
			.	'<html lang="en_US" xml:lang="en_US" xmlns="http://www.w3.org/1999/xhtml">'
			.	'<head>'
			.	'<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />'
			.	'<title>ngWebSite Installation and setup preparation</title>'
			.   '<script type="text/javascript">'
			.	'function cwait() {'
			.	' document.body.style.cursor="wait";'
			.	' var wo = document.getElementById("ax11");'
			.	' wo.style.display="inline";'
			.	'}'
			.	'</script>';
			if (file_exists('ngtemp/') && is_dir('ngtemp/')) {
				$cnt.='<script type="text/javascript" src="ngtemp/js/jquery.js"></script>'
				.	'<script type="text/javascript" src="ngtemp/js/jquery.progressbar.js"></script>'
				.	'<script type="text/javascript" src="ngtemp/js/ngIn.js"></script>'
				.	'<link rel="stylesheet" title="ngWS" href="ngtemp/css/ngIn.css" type="text/css" />';
			}
			$cnt.='<style type="text/css">'
			.	'<!--'
			.	'body {'
			.	'	margin: 0;'
			.	'	background: -moz-linear-gradient(left, GoldenRod, Khaki, PaleGoldenrod,PaleGoldenrod,PaleGoldenrod, Khaki, GoldenRod);'
			.	'	background-image: -webkit-linear-gradient(left, GoldenRod, Khaki, PaleGoldenrod,PaleGoldenrod,PaleGoldenrod, Khaki, GoldenRod);'
			.	'	background-image: -o-linear-gradient(left, GoldenRod, Khaki, PaleGoldenrod,PaleGoldenrod,PaleGoldenrod, Khaki, GoldenRod);'
			.	'	line-height: 1.1em; '
			.	'} '
			.	'h2 {'
			.	'	margin-bottom: 4px; '
			.	'} '
			.	'h4 {'
			.	'	margin-top: 0px;'
			.	'} '
			.	'.container {'
			.	'	background-color: #FAFAD2;'
			.	'	height: 480px; margin-left: 210px;'
			.	'	overflow: auto; '
			.	'	border: 3px solid White;'
			.	'	padding: 0 3px 0 3px;'
			.	'} '
			.	'.con {'
			.	'	background-color:#fff; '
			.	'	border: 1px dotted; '
			.	'	padding: 4px 10px; '
			.	'	font-family: monospace; '
			.	'	font-size: 0.9em; '
			.	'	overflow-y: auto;'
			.	'} '
			.	'.con1 {'
			.	'	height: 232px; '
			.	'	line-height: 1.1em; '
			.	'} '
			.	'.con2 {'
			.	'	height: 252px; '
			.	'	line-height: 1.2em; '
			.	'} '
			.	'.hi {'
			.	'	color:#d00; '
			.	'}'
			.	'-->'
			.	'</style>'
			.	'</head>'
			.	'<body>'
			.	'<div style="'
			.	'	position:absolute; z-index:-1;'
			.	'	top:100px; left:0;'
			.	'	height: 100px;'
			.	'	border:0; border-top:3px; border-bottom: 3px; border-style:solid; border-color: white;'
			.	'	background-color: #008080;'
			.	'"></div>'
			.	'<div id="wrap" style="margin: 0 auto; width: 960px;">'
			.	'<div style="'
			.	'	background-color: #ffdd78;'
			.	'	border: 3px solid White; border-top:0; border-bottom:0; height:100px;'
			.	'	padding: 0 3px 0 3px;'
			.	'	overflow: hidden;'
			.	'	text-align: center;'
			.	'"><h2>ngInstall</h2>'.MYVSN.'<h4>The web2.0 driven installer for phpWebSite and ngWebSite</h4>'
			.	'</div>'
			.	'<div style="'
			.	'	background-color: #22BB66;'
			.	'float: left; width: 203px; height: 300px;'
			.	'border: 3px solid White;'
			.	'padding: 0 0 0 3px;'
			.	'">'
			.	'<h4>ngInstall</h4>'
			.	'prepares the installation and the setup of ngWebSite / phpWebSite.'.BR
			.	'After a check agaist some preconditions for phpWebsite / ngWebsite, the installation procedures'
			.	' will be performed in about less than 5 minutes with 4 steps.'
			.	'</div>'
			.	'<div class="container">';
			return $cnt;
		}
				
		protected function t03($t)
		{
			$alnk=$aend=$asrc=$nsrc=$ssrc='';
			if ($t) {
				unset($_SESSION['dsrc']);
				$alnk='<a href="ngInstall.php?op=s1&amp;src=';
				$asrc='apws">';
				$nsrc='ngws">';
				$ssrc='spws">';
				$aend='</a>';
			} else {
				if (isset($_REQUEST['src'])) {
					$_SESSION['dsrc']=$_REQUEST['src'];
				}
			}
			$aatr=$natr=$satr='<span>';
			$zatr='</span>';
			if (isset($_SESSION['dsrc'])) {
					switch($_SESSION['dsrc']) {
						case 'apws':
							$aatr='<span class="hi">';
						break;
						case 'ngws':
							$natr='<span class="hi">';
						break;
						case 'spws':
							$satr='<span class="hi">';
						break;
					}
			}
			$cnt='<ol><li>Select a distribution server to get the ngPreInstaller, ngInstaller and required sortware parts from '.BR
				.	$alnk.$asrc.$aatr.'phpWebSite@AppstateEdu'.$zatr.$aend.' or '
				.	$alnk.$ssrc.$satr.'phpWebSite@Sourceforge'.$zatr.$aend.' or '
				.	$alnk.$nsrc.$natr.'phpWebSite@ngWebSite'.$zatr.$aend
				.	BR
				.	'to your site and location where this ngInstall is called.</li>';
			return $cnt;
		}
			
		protected function t04($t)
		{
			if ($t) {
				if (file_exists('ngGetInstaller.php')) {
					$alnk='<a onclick="cwait()" href="ngInstall.php?op=s2">';
					$cc=true;
				} else {
					$cc=false;
				}
			} else {
				$alnk='';
			}
			$alnk==''?$aend='':$aend='</a>';
			$cnt='<li>'.$alnk.'Run ngPreInstaller to let select a distribution, and ngInstaller '.$aend.'<span id="ela2">&nbsp;</span>'.BR
			.	'that loads and checks the ongoing source archieves from the selected distribution server to your site.</li>'
			.	'<li>Decompresses and expands the source archives on your site. <span id="ela3">&nbsp;</span></li>'
			. 	'<li>Runs the setup'.BR
			.	'performs the administrative setup of ngWebSite / phpWebSite. <span id="ela4">&nbsp;</span></li></ol>';
			if ($t) {
				$cnt.='<pre class="con con2">Step 1 - ';
				$cnt.=$this->devmode?'(devmode) ':'';
				$cnt.=$cc
				?'ngGetInstaller available <img id="ax11" style="display:none;" src="ngtemp/img/ajax11square.gif" alt="running" />'
				:'Error ngGetInstaller not available';
				$cnt.='</pre>';
			}
			return $cnt;
		}
		
		protected function t09()
		{
			return '</div></div></body>';
		}
		
		protected function diag()
		{
			$co=array();
			$cnt  = 'When all is finished successfully, your site is ready with ngWebSite / phpWebSite.'.BR;
			$cnt .= 'Please pay attention to the diagnostic of your site, that verifies the preconditions:';
			$cnt .=   '<pre class="con con1">';
			$cnt .=  '1.1.Server-OS.....................: ' . PHP_OS . BR;
			$cnt .=  '1.2.WebAddress....................: ' . $_SERVER['HTTP_HOST'] . BR;
			list($subdir,$nop)=explode('ngInstall.php',$_SERVER['PHP_SELF']);
			$cnt .=  '1.3.SubDir........................: ' . '/' . trim($subdir,'/') . BR;
			$cnt .=  '1.4.Url...........................: ' . $_SERVER['HTTP_HOST'] .$subdir . BR;
	
			$cnt .=  '    <sub><i>verifying essential php.ini parameters</i></sub>'.BR;
	
			list($i,$nop)=explode('-',PHP_VERSION . '-');
				$cnt .=  '2.1.Php-Version(min 5.2)..........: ' . $i;
				$cnt .=  ($i<'5.2')?KO.NOCO:OK; $cnt .=  BR;
			$i=ini_get('memory_limit');
				substr($i,-1)=='M'?$i=rtrim($i,'M'):$i=round(ini_get('memory_limit')/1024/1024,0);
				$cnt .=  '2.2.MemoryLimit(min 16M)..........: ' . $i . 'M ';
				if ($i<"16") {
					$cnt.=OH.OHCO.BR;
					$co[]='php_value memory_limit 16M';
				} else {
					$cnt.=OK.BR;
				}
			$i=ini_get('max_execution_time');
				$cnt .=  '2.3.MaxExecTime(min 20s)..........: ' . $i.'s ';	
				if ($i<"20") {
					$cnt.=OH.OHCO;
					if (ini_set('max_execution_time', '20')) {
						$cnt.=OK.BR;
						$co[]='php_value max_execution_time 20';
					} else {
						$cnt.=KO.NOCO.BR;
					}
				} else {
					$cnt.=OK.BR;
				}
			$i=ini_get('magic_quotes_gpc');
				$cnt .=  '2.4.MagicQuotes(need Off).........: ';
				if ($i) {
					// cannot tried by ini_set, only with htaccess
					$cnt.='On'.OH.OHCO.BR;
					$co[]='php_flag magic_quotes_gpc off';
				} else {
					$cnt.='Off'.OK.BR;
				}
			$i=ini_get('register_globals');
				$cnt .=  '2.5.RegisterGlobals(need Off).....: ';
				if ($i) {
					// cannot tried by ini_set, only with htaccess
					$cnt.='On'.OH.OHCO.BR;
					$co[]='php_flag register_globals off';
				} else {
					$cnt.='Off'.OK.BR;
				}
			$i=ini_get('safe_mode');
				$cnt .=  '2.6.SafeMode(need Off)............: ' ;
				if ($i) {
					$cnt.='On'.OH.OHCO.BR;
					$co[]='php_flag safe_mode off';
				} else {
					$cnt.='Off'.OK.BR;
				}
			$i=ini_get('allow_url_fopen');
				$cnt .=  '2.7.AllowUrlFopen(need On)........: ';
				if (!$i) {
					$cnt.='Off'.OH.OHCO.BR;
					$co[]='php_flag allow_url_fopen on';
				} else {
					$cnt.='On'.OK.BR;
				}
			$i=ini_get('session.auto_start');
				$cnt .=  '2.8.SessionAutoStart(need Off)....: ';
				if ($i) {
					$cnt.='On'.OH.OHCO.BR;
					$co[]='php_flag session.auto_start off';
				} else {
					$cnt.='Off'.OK.BR;
				}
			$i=ini_get('session.use_trans_sid');
				$cnt .=  '2.9.SessionUseTransSID(need Off)..: ';
				if ($i) {
					$cnt.='On'.OH.OHCO.BR;
					$co[]='php_flag session.use_trans_sid off';
				} else {
					$cnt.='Off'.OK.BR;
				}
		
			$cnt .=  '    <sub><i>verifying file system and database server</i></sub>'.BR;
			
			$i=is_writable('./');
				$cnt .=  '3.1.FileSystemReady...............: ';
				$cnt .=  $i?'Yes'.OK.BR:'No'.KO.BR;
			$i=function_exists('finfo_open') || function_exists('mime_content_type') || !ini_get('safe_mode');
				// finfo_open = 5.3+ / mime_content_type = deprecated / 
				$cnt .=  '3.2.MimeFileTypeDetection.........: ';
				$cnt .=  $i?'Yes'.OK.BR:'No'.KO.BR;
			$i=extension_loaded('gd');
				$cnt .=  '3.3.GDgraficLibrariesInstalled....: ';
				$cnt .=  $i?'Yes'.OK.BR:'No'.KO.BR;
			$i=extension_loaded('mysql');
				$cnt .=  '3.4.mySqlInstalled................: ';
				$cnt .=  $i?'Yes'.OK.BR:'No'.KO.BR;
			$i=extension_loaded('gettext');
				$cnt .=  '3.5.gettextSupported..............: ';
				$cnt .=  $i?'Yes'.OK.BR:'No'.OH.BR;
			$cnt .=  '</pre>';
			
			if (count($co)>0) {
				//	array_unshift($co, 'Order Allow,Deny', 'Allow from all', '');
				if (!file_exists('.htaccess')) {
					@file_put_contents('.htaccess', implode("\n",$co));
				}
				@file_put_contents('htaccess.txt', implode("\n",$co));
				$this->diagok=false;
			} else {
				$this->diagok=true;
			}
			
			return $cnt;
		}
		
	}

?>