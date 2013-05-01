<?php

// ngGetInstaller.php is a program to get the ngWebSite Installer
//
// Jun 2011 
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
 
	
	class ngGetInstaller
	{

		var $devmode = false;
	
		function wgetInstaller() {
			$cnt = 'Step 1 Feedback from ngPreInstaller: '.BR;
			list($subdir,$nop)=explode('ngInstall.php',$_SERVER['PHP_SELF']);
			$cnt .= '  copy from "' . substr($_SESSION['URLDLPRE'],7) . '"'.BR
				 .	'  copy to   "' . $_SERVER['HTTP_HOST'].$subdir . '"' . BR;
	
			@mkdir('ngtemp');
			@mkdir('ngtemp/css');
			@mkdir('ngtemp/img');
			@mkdir('ngtemp/js');
			@mkdir('ngtemp/pear');
	
				$flist = array(	'ngGetInstaller.php',
								'ngInstaller.php',
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
	
				foreach ($flist as $fname) {
					$cnt .= '  receiving '.$fname.' ';
					if (!$this->devmode  || !file_exists($fname)) {
						$cc=@copy($_SESSION['URLDLPRE'].$fname, $fname);
					} else {
						$cc=true;
						$cnt.=' (devmode) ';
					}
					$cnt .= $cc?OK:KO;
					$cnt .= BR;
				}
						
			
			return $cnt;
		}
		
	}
 ?>