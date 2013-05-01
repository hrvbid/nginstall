readme.nginstaller.txt                                        ngWebSite
***********************************************************************
NOTE: current state (2012.10.09) 1.0.12
***********************************************************************
How to prepare, install and setup phpWebSite/ngWebsite on your server:

-	download the prepare program (ngInstall.php) to your server. That
	is a single file available at sourceforge.net. ngInstall should be
	placed in a directory on your server, from where the webserver will
	deliver content to your visitors. Such a place in general is called
	"document root" or "htdocs" and in many cases named like
	"public_html", "web", "www" or similar. You may also create and use
	a subdirectory below the document root (or nested subdirectories).
	A usual method to have the prepare program is:
	download ngInstall.php from sourceforge.net to your workstation,
	and then upload the program by a file transfer program (FTP,SCP etc)
	to your server (to your site). The D/L url is http:
	//sourceforge.net/projects/ngwebsite/files/ngInstaller/ngInstall.php
	
-	Call the prepare program with your web browser by using your domain
	(and the subdirectory) name(s), but please don't use old MS internet
	explorers. For example:
	
	http://your.site/ngInstall.php
	or
	http://your.site/subdir/ngInstall.php
	
-	The first screen of the prepare program will show some diagnostic infos
	about your site. Please pay attention to the messages, because only when
	the preconditions are confirmed successfully, the operation of the
	phpWebSite or ngWebsite system is eligible for your server. In a bad
	case, the only way to resolve the conflicts is to adjust your webserver
	to the requirements (if possible for you) or change your internet service
	provider (ISP) to one who can safisfy. Let remark, the preconditions are
	far from beeing exotic, thus if you have problems with the preconditions
	of phpWebSite / ngWebSite, probably you will also run in trouble with 
	other modern software to install.
	Messages marked with OK are okey, with OH are a bit worse but not critical
	and with KO (knocked out) are the bad ones. Without any mark, the message
	are info only - perhaps nice to know.
	
-	Afterwards, the initial screen of the prepare program will lead you thru
	the next steps, which consists of ngInstall, ngPreInstaller, ngInstaller
	and finally the setup of phpWebSite / ngWebsite. Not counting the time you
	may drink coffee or read attentive the screens, the installation should be
	finished in less then five minutes.
	
-	After the ngInstaller is finished (that is just before the setup starts),
	the ngInstall.php file is renamed to ngInstall.nop (no operataion) to
	prevent repetitive execution. In case you want to perform the installer
	again, rename it back to ngInstall.php
	
Notes about phpWebSite and ngWebSite.
While phpWebSite is the native, the origin of the server software provided by
the Appalachian State University (ASU), ngWebSite is a fork of phpWebSite with
modifications and additions to have the software suitable for international
multilingual use, collaboration stuff and content security. Apart from that,
both distros are nearby, sharing their knowledge and are not competitors.

Notes about versions.
The software of phpWebSite historically exists in three lines: The older
versions 0.10.x and below are outdated and are not supported since over two
years. The version line 1.7.x, often mentioned as fallout, is the current one
suggested for production use. A future line 2.x.x from ASU is in progress and
is named beanie-cms, but is in development state and in the immediate short
next time not suitable to run for production.

Good luck,
 Hilmar

	