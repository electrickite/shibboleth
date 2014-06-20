ShibProtect
===========

Adds Shibboleth protected resources to MODX Revolution. [Shibboleth][1] is an open-source project that provides single sign-on capabilities and allows sites to make informed authorization decisions for individual access of protected online resources in a privacy-preserving manner.

Typically, a resource is protected by Shibboleth using web server directives that require a Shibboleth session. In addition to authentication, Shibboleth can restrict access to certain groups based on attributes provided by a user's Identity Provider (IdP). These directives are often placed in .htaccess files to protect different paths and directories in the web root.

Protecting MODX resources using this method can be difficult since they do not exist on the file system. ShibProtect allows resources to be protected by Shibboleth based on the value of a template variable.

Requirements
------------

You must have Shibboleth installed and properly configured on the web server hosting MODX to protect content with ShibProtect. This add-on was written for use with an Apache web server. For example, the syntax for specifying authorization rules is identical to the Shibboleth Apache directives. Other server configurations may work, but should be be well tested before being used in production.

Because Shibboleth provides very little security when used over unencrypted (HTTP) connections, only HTTPS URLs are supported by this extra. Your site must have SSL support.

Configure Shibboleth
--------------------

ShibProtect assumes that Shibboleth has been enabled in [passive or "lazy session" mode][2] for the root directory (containing index.php) of the MODX site. In a typical setup, the following directives should be added to the .htaccess file in the MODX root folder, after the RewriteBase directive.

    # Skip Shibboleth URLs. May be necessary, depending on server configuration
    # Replace "Shibboleth.sso" with the session initiator URL for your server
    RewriteRule ^(Shibboleth.sso)($|/) - [L]

    # Enables Shibboleth lazy sessions
    AuthType Shibboleth
    ShibRequestSetting requireSession false
    Require shibboleth

Note that these settings will not protect any files or resources present in the file system. For access controlled downloads, appropriate Shibboleth directives will need to be placed in the .htaccess file for the directory that contains the files to be protected.

Protect MODX Resources
----------------------

To protect pages, create a template variable that will be used to designate the resource as protected by Shibboleth. Make sure it has a 1/0 or true/false value. Finally, add the TV to a template and set the `shibprotect.tv` system setting to the TV name. Any resources with the TV set will trigger the ShibbolethProtectPages plugin.

Authentication and Authorization
--------------------------------

When handling requests for protected resources, MODX will first check that the user has an active Shibboleth session. If they do not, they will be redirected to your server's Login handler. From there, depending on your Shibboleth environment, they will likely be redirected to a web-based sign on page. Following a succesful authentication attempt, the will eventually be directed back to their original destination on your site.

After authentication, ShibProtect can also check that a user is authorized to view protected content. Authorization rules can be configured in the `shibprotect.rules` system setting, one per line. The rules should be entered using the 'shib-attr' [Apache syntax][3]. The attributes available will vary based on the user's IdP: contact you IdP administrator for details on the types of information provided with each user session. (One way to get an idea of the attributes in your environment is to dump the `$_SERVER` variable of an active Shib session)

An example rule set might look something like this:

    Require shib-attr username person.123@example.com person.456@example.com
    Require shib-attr affiliation employee contractor

Which would allow a user whose Shibboleth username attribute was either "person.123@example.com" or "person.456@example.com" to access protected resources. It would _also_ grant access to a user whose affiliation was "employee" or "contractor". Note that rule conditions are evaluated using OR relationships: a user who matches any attribute in any rule is considered authorized.

Authorization rules can also be read from a file by placing its absolute file system path in `shibprotect.rules_file`. The rules file can be a .htaccess file. This arrangement could potentially be used to secure a directory of files and a set of MODX web pages with the same set of directives.

System Settings
---------------

ShibProtect is configured using a number of system settings.

  * shibprotect.tv: Name of the TV used to designate a protected resource
  * shibprotect.username_attribute: The Shibboleth attribute that contains a
    user's unique account name. This should be an environment varibale that is
    only set when a Shibboleth session is active.
  * shibprotect.rules: Authorization rules, one per line, in Apache syntax
  * shibprotect.rules_file: absolute file system path to a file containing
    authorization rules
  * shibprotect.login_path: The relative URL for the server's Shibboleth login
    handler
  * shibprotect.fixenv: Attempt to fix Apache mod_rewrite prepending 'REDIRECT_'
    to variable names
  * shibprotect.session_indicator: Environment variable that indicates the
    presence of a Shibboleth session

Helper Snippets
---------------

ShibProtect provides several convenience snippets that can be used to gather information from the Shibboleth session.

###ShibAuth

Tests whether the current user is authorized to view protected resources. Will return '1' if the user is authorized and '0' if not. The snippet can be used in any resource.

    [[!shibAuth:is=`0`:then=`class=“content-is-locked"`]]

###ShibLoginUrl

Returns the Shibboleth login handler URL for the current resource. Can also redirect to an artibtrary URL by passing a `target` property.

    [[!shibLoginUrl]]
    [[!shibLoginUrl? &target=`http://www.example.com`]]

###ShibAttr

Returns the value of the Shibboleth attribute specified in the `attribute` property for the currently authenticated user. 

    [[!shibAttr? &attribute=`email`]]

Contributing
------------

ShibProtect is [hosted on GitHub][4]. Ideas for improvements? Bug reports? Please open an issue in the project's issue queue.

Author
------

ShibProtect is written and maintained by Corey Hinshaw <hinshaw.25@osu.edu> for the Ohio State University, [University Communications][5]. 



[1]: https://shibboleth.net
[2]: https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPProtectContent#NativeSPProtectContent-PassiveProtection
[3]: https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPhtaccess#NativeSPhtaccess-GeneralSyntax
[4]: https://github.com/osuInteractiveComm/shibprotect
[5]: http://ucom.osu.edu
