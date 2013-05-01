/**
  * @author Hilmar Runge <ngwebsite.net>
  * @version 20110219
  */

	var ngokimg = '<img src="ngtemp/img/ok.10.gif" alt="ok" />';
	var ngohimg = '<img src="ngtemp/img/oh.10.gif" alt="oh" />';
	var ngnoimg = '<img src="ngtemp/img/no.10.gif" alt="no" />';
	var ngkoimg = '<img src="ngtemp/img/ko.10.gif" alt="ko" />';
	var ngaximg = '<img src="ngtemp/img/ajax10red.gif" alt="..." class="ax"/>';
	
	var probara = 0;
	var promaxa = 100;
	var proopa = 'pickup ';
	var probarc = 0;
	var promaxc = 100;
	var proopc = 'checking ';
	var proopd = 'decompressing ';
	var proope = 'expanding ';
	
	var chkstate = true;
	
	var elad = 0;
	var elas = 0;
	var elat = 0;
	var ela  = 0;
	
	var da = '';
	
	var redo = new Array();
	
	$(document).ready(function(){
	
		var s21da = $('#s21').text();
		if (s21da.substr(0,2) == 'da')
		{
			da=s21da;
			ngC(s21da,'msg');
		}
	
	});

	function ngC(amin,to) {
		elad = new Date();
		elas = elad.getTime();
		ngUniCS(amin,'a',to,'');
	}
	
	function ngCA(amin,to,i) {
		ngUniCS(amin,'A',to,i);
	}
	
	function ngCc(amin,to,i) {
		var wo = '#' + to + i;
		$(wo).append(' check ' + ngaximg);
		ngUniCS(amin,'c',to,i);
	}
	
	function ngCd() {
		$("#ela2 > a").remove();
		$("#ela2").append(ngokimg);
		for (i=0;i<promaxc;++i) {
			var wo = '#msg' + i;
			$(wo).append(' decompress ' + ngaximg);
			ngUniCS(da,'d','msg',i);
		}
	}
	
	function ngCe(i) {
		var wo = '#msg' + i;
		$(wo).append(' expand ' + ngaximg);
		ngUniCS(da,'e','msg',i);
	}
	
	function ngCf() {
		ngUniCS(da,'f','msg','');
	}

	function ngUniCS(amin,op,to,add) {
		var url = 'ngInstall.php?da=' + amin + '&xaop=' + op;
		switch (op) {
			case 'a': case 'f':
				break;
			case 'A': case 'c': case 'd': case 'e':
				url = url + '&i=' + add;
				break;
			default:
				return;
		}
		$.ajax
		({
			type: "GET",
			url: url,
			success: function(reply)
					{
						switch (op) {
							case 'a': case 'A': case 'c': case 'd': case 'e': case 'f':
								ngUniSC(amin,reply,op,to);
							break;
						}
					}
		});
	}
	
	function ngUniSC(amin,reply,op,to) {
		if (ngVerReply(reply)) {
			var eladd = new Date();
			elatt = eladd.getTime();
			elaa = Math.round((elatt - elas) / 1000);
			switch (op) {
				case 'a':
					var tf = reply.split('');
					var x = 0;
					for (i in tf) {
						if (tf[i]=='f') {
							++x;
						}
					}
					promaxa = x;
					promaxc = x;
					$('#probarc').show();
					$('#probarc').reportprogress(probarc,promaxc,proopc);
					for (i in tf) {
						var wo = '#' + to + i;
						if (tf[i]=='t' && isNaN(redo[i])) {
							$(wo).html('exists=' + ngokimg);
							ngCc(amin,to,i);
						} else {
							$(wo).html('pickup ' + ngaximg);
							// trigger
							ngCA(amin,to,i);
						}
					}
					break;
				case 'A':
					// reply tf + i 
					var s = reply.substr(0,1);
					var i = reply.substr(1);
					++probara;
					$('#probara').show();
					$('#probara').reportprogress(probara,promaxa,proopa);
					var wo = '#' + to + i;
					if (s=='t') {
						$(wo).html('exists=' + ngokimg);
						ngCc(amin,to,i);
					} else {
						$(wo).html('exists=' + ngkoimg);
					}
					$('#ela2').html('- elapsed ' + elaa + 's');
					break;
				case 'c':
					// replies tfn + i  -- max
					var s = reply.substr(0,1);
					var a = reply.substr(1).split('--',3);
					var i = a[0];
					var x = a[1];
					var dbg=a[2];
					promaxc=x;
					$('#probarc').show();
					$('#probarc').reportprogress(probarc,promaxc,proopc);
					var wo = '#' + to + i;
					$(wo + ">" + "img.ax").remove();
					switch (s) {
						case 't':
							$(wo).append(' ' + ngokimg);
							break;
						case 'f':
							$(wo).append(' ' + ngkoimg);
							// re
							if (isNaN(redo[i])) {
								redo[i] = 1;
							} else {
								redo[i] = redo[i] + 1;
							}
							if (isNaN(redo[i])==false && redo[i] < 9) {
								++promaxa;
								$(wo).html('check ' + ngkoimg + ' +' + redo[i] + ' reget ' + ngaximg);
								ngCA(amin,to,i);
								return;
							} else {
								$(wo).append(' gave up');
								chkstate=false;
							}
							break;
						case 'n':
							$(wo).append(' ' + ngohimg);
							break;
					}
					$('#ela2').html('- elapsed ' + elaa + 's');
					++probarc;
					if (probarc >= promaxc) {
						probarc=0;
						probara=0;
						$('#probara').hide();
						$('#probarc').hide();
						if (chkstate) {
							var alnk = '  <a class="a" onclick="ngCd()">continue</a>';
						} else {
							var alnk = '  <span style="color:#d00;">Check(s) failed - please reload window to retry.</span>';
						}
						$('#ela2').append(alnk);
					}
					break;
				case 'd':
					// reply tf + i 
					var s = reply.substr(0,1);
					var i = reply.substr(1);
					$('#probara').show();
					$('#probara').reportprogress(probara,promaxc,proopd);
					var wo = '#' + to + i;
					$(wo + ">" + "img.ax").remove();
					switch (s) {
						case 't':
							$(wo).append(' ' + ngokimg);
							ngCe(i);
							break;
						case 'f':
							$(wo).append(' ' + ngkoimg);
							break;
						case 'n':
							$(wo).append(' ' + ngohimg);
							ngCe(i);
							break;
					}
					$('#ela3').html('- elapsed ' + elaa + 's');
					++probara;
					if (probara >= promaxc) {
						probara=0;
						$('#probara').hide();
					}
					break;
				case 'e':
					// reply tf + i + -- in case msgs come with
					var s = reply.substr(0,1);
					var a = reply.substr(1).split('--',2);
					var i = a[0];
					$('#probarc').show();
					$('#probarc').reportprogress(probarc,promaxc,proope);
					var wo = '#' + to + i;
					$(wo + ">" + "img.ax").remove();
					switch (s) {
						case 't':
							$(wo).append(' ' + ngokimg + ' ' + a[1]);
							break;
						case 'f':
							$(wo).append(' ' + ngkoimg + ' ' + a[1]);
							break;
						case 'n':
							$(wo).append(' ' + ngohimg + ' ' + a[1]);
							break;
					}
					$('#ela3').html('- elapsed ' + elaa + 's');
					++probarc;
					if (probarc >= promaxc) {
						probarc=0;
						$('#probarc').hide();
						$("#ela3").append(' ' + ngokimg);
						var alnk = '  <a class="a" href="./index.php">continue with Setup</a>';
						$('#ela4').append(alnk);
						// done all
						ngCf();
					}
					break;
			}
		}
	}

	function ngVerReply(reply) {
		if (reply == '') {
			return false;
		}
		return true;
	}
