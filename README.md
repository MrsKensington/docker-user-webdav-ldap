# docker-user-webdav-ldap
A webdav container that gives a different protected directory to each user specified in a separate LDAP Directory. It uses basic authentication with an LDAP backend (not provided in the container) to authenticate the user and then redirects the user to their own WebDav enabled directory.

All content is stored under /var/www/users with a subdirectory per user (e.g. /var/www/users/bob, /var/www/users/frank) and only the user authenticated is allowed to access that directory.

Example Usage:

    docker run -d --name "webdav" --restart="always" \
        -v /path/to/data:/var/www/users \
        -e LDAP_BASE_DN="dc=example,dc=org" \
        -e LDAP_SEARCH_DN="cn=admin,dc=example,dc=org" \
        -e LDAP_SEARCH_PASSWORD="password" \
        -e LDAP_FILTER="memberOf=cn=webdav,ou=groups,dc=example,dc=org"
        
Environment Variables:
  - ENV LDAP_SCHEME - Whether to use ldap or ldaps (Default: ldap)
  - LDAP_SERVER - The hostname of the LDAP server (Default: openldap)
  - LDAP_BASE_DN - The base DN to do all searching from
  - LDAP_SEARCH_ATTRIBUTE - The attribute to use for matching the username (Default: uid)
  - LDAP_FILTER - An extra filter to filter all users by (Default: objectClass=*)
  - LDAP_SEARCH_DN - The user to bind to the LDAP directory as during the search phase
  - LDAP_SEARCH_PASSWORD - The password to use when binding to the LDAP directory during the search phase
  - AUTH_REQUIRE - The Require value to put into the doc root (Default: valid-user)
