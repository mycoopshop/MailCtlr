# MailCtlr

MailCtlr is a new system for the management of your advertising campaigns based on email, it allows you to send all the emails you want for free. 
Using different SMTP server can send free mail to all the contacts you want at no cost or almost.

# How to: Install Web Application

* Open config/mailctlr.alpha.php and edit the follow line

```php
<?php
    //Database
	'db' => array(
		'host' => 'localhost',
		'user' => 'mailctlr',
		'pass' => 'mailctlr',
		'name' => 'mailctlt',
		'pref' => 'mc_',
	),
            
    //Location web application
	'url' => 'http://www.your-site.com/folder',
	'home' => 'http://www.your-site.com/folder',
?>
```

* Open .htaccess and edit the follow line

```php
RewriteBase /folder/
RewriteRule . /folder/index.php [L]
```


# MailCtlr License 
MailCtlr is licensed GNU AFFERO GENERAL PUBLIC LICENSE http://www.gnu.org/licenses/agpl-3.0.html 


# Roadmap for beta version

 - Import contact from a .csv file
 - Registration to list form website 
 - Automatic upgrade to the new version
 - Statistics on sending: email receipts, open (opening number), deleted
 - Autoresponders for default actions: registration, cancellation, modification, (you can also create your actions)
 - Website for Application
 - [DONE!] Port to GitHub