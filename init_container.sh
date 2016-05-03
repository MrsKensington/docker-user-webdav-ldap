#!/bin/bash -x

#Build the LDAP Search command
COMMAND="ldapsearch -h '${LDAP_SERVER}' -b '${LDAP_BASE_DN}' -x"
if [ ! -z "${LDAP_SEARCH_DN}" ]; then
    COMMAND="${COMMAND} -D '${LDAP_SEARCH_DN}' -w '${LDAP_SEARCH_PASSWORD}'"
fi
COMMAND="${COMMAND} '${LDAP_FILTER}' '${LDAP_SEARCH_ATTRIBUTE}'"

# do the search and create a dir for each user
USERS=`eval ${COMMAND} | grep "^${LDAP_SEARCH_ATTRIBUTE}:" | sed "s/${LDAP_SEARCH_ATTRIBUTE}: //g"`
for USER in ${USERS}; do
    mkdir -p /var/www/users/${USER}/
done

export LDAP_SCHEME="${LDAP_SCHEME}"
export LDAP_SERVER="${LDAP_SERVER}"
export LDAP_BASE_DN="${LDAP_BASE_DN}"
export LDAP_FILTER="${LDAP_FILTER}"
export LDAP_SEARCH_DN="${LDAP_SEARCH_DN}"
export LDAP_SEARCH_PASSWORD="${LDAP_SEARCH_PASSWORD}"
export LDAP_SEARCH_ATTRIBUTE="${LDAP_SEARCH_ATTRIBUTE}"
export AUTH_REQUIRE="${AUTH_REQUIRE}"
export USERS="${USERS}"

a2dissite webdav
rm -f /etc/apache2/sites-available/webdav.conf
php /etc/apache2/sites-available/webdav.conf.php > /etc/apache2/sites-available/webdav.conf
a2ensite webdav

source /etc/apache2/envvars
exec /usr/local/bin/apache2-foreground
