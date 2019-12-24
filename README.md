# log

Data from error.log is passed in json format and then javascript renders it on the page.

To set up:
    -Store in the server source folder of 192.168.0.(**)/log.php.
    -If you're getting the error, 'fail to openstream' you may need to change permissions on the folder that stores error.log. Try "sudo chmod -R 777 /var/log/apache2".
    -Double check that your error log is in '/var/log/apache2/error.log' and change the $file variable in getAllLogEntries function if it's not.

There is a function called trigger_notice. It isn't used in this file but I use it instead of trigger_error, and it will format the error when displayed on this log (log.php).

Features:
It will refresh automatically if you leave on auto refresh, and you can highlight and hide individual errors (without editing the source file error.log) or you can completely clear the source file error.log by clicking 'X' when no errors are highlighted. 'R' restarts apache but I have some comments about that at the bottom of this set up. Hopefully it's intuituitive and doesn't take much more set up than copying and pasting into the log.php file.

When I use it on the raspberry pi it works well for all functions, but on Ubuntu the restart apache command doesn't work, not sure why and no amount of permission changing has sorted it for me.
