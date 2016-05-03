<?php
    $url = sprintf("%s://%s/%s?%s?sub?(%s)", 
                $_ENV['LDAP_SCHEME'],
                $_ENV['LDAP_SERVER'],
                $_ENV['LDAP_BASE_DN'],
                $_ENV['LDAP_SEARCH_ATTRIBUTE'],
                $_ENV['LDAP_FILTER']);

    $users = explode("\n", $_ENV['USERS']);
    
    function outputAuthLdapCode()
    {
        global $_ENV, $url;
?>
        AuthType Basic
        AuthName "WebDav Access"
        AuthBasicProvider ldap
        AuthLDAPURL "<?=$url?>"
<? if(isset($_ENV['LDAP_SEARCH_DN'])) { ?>
        AuthLDAPBindDN "<?=$_ENV['LDAP_SEARCH_DN']?>"
<? } ?>

<? if(isset($_ENV['LDAP_SEARCH_PASSWORD'])) { ?>
        AuthLDAPBindPassword "<?=$_ENV['LDAP_SEARCH_PASSWORD']?>"
<? } ?>
<?php
    }
?>
<VirtualHost *:80>
    # The ServerName directive sets the request scheme, hostname and port that
    # the server uses to identify itself. This is used when creating
    # redirection URLs. In the context of virtual hosts, the ServerName
    # specifies what hostname must appear in the request's Host: header to
    # match this virtual host. For the default virtual host (this file) this
    # value is not decisive as it is used as a last resort host regardless.
    # However, you must set it for any further virtual host explicitly.
    #ServerName www.example.com

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html/>
        AllowOverride None
        Options MultiViews Indexes SymLinksIfOwnerMatch IncludesNoExec

<? outputAuthLdapCode() ?>

        Require <?=$_ENV['AUTH_REQUIRE']?>

    </Directory>

    # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
    # error, crit, alert, emerg.
    # It is also possible to configure the loglevel for particular
    # modules, e.g.
    #LogLevel info ssl:warn

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    UserDir /var/www/users/*/
    UserDir disabled root

    <? foreach($users as $user) { ?>

    <Directory /var/www/users/<?=$user?>/>
        AllowOverride None
        Options MultiViews Indexes SymLinksIfOwnerMatch IncludesNoExec

<? outputAuthLdapCode() ?>

        Require user <?=$user?>

    </Directory>
    <? } ?>

</VirtualHost>

