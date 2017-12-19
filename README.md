Shibboleth
==========

Adds Shibboleth authentication to MODX Revolution. [Shibboleth][1] is an
open-source project that provides single sign-on capabilities and allows sites
to make informed authorization decisions for individual access to protected
online resources in a privacy-preserving manner.

The Shibboleth add-on can be used to secure individual resources and to
authenticate MODX user accounts.

**Note:** Shibboleth replaces the deprecated ShibProtect add-on. Shibboleth
contains the same content protection features as ShibProtect, but adds many
additional capabilities.

Requirements
------------

You must have Shibboleth installed and properly configured on the web server
hosting MODX. This add-on was written for use with an Apache web server. For
example, the syntax for specifying content authorization rules is identical to
the Shibboleth Apache directives. Other server configurations may work, but
should be well tested before being used in production.

Because Shibboleth provides very little security when used over unencrypted
(HTTP) connections, only HTTPS URLs are supported by this extra. Your site must
have SSL support.

Configure Shibboleth
--------------------

This add-on assumes that Shibboleth has been enabled in
[passive or "lazy session" mode][2] for the root directory (containing
index.php) of the MODX site. In a typical setup, the following directives should
be added to the .htaccess file in the MODX root folder, after the RewriteBase
directive.

    # Skip Shibboleth URLs. May be necessary, depending on server configuration
    # Replace "Shibboleth.sso" with the session initiator URL for your server
    RewriteRule ^(Shibboleth.sso)($|/) - [L]

    # Enables Shibboleth lazy sessions
    AuthType Shibboleth
    ShibRequestSetting requireSession false
    Require shibboleth

Note that these settings will not protect any files or resources present in the
file system. For access controlled downloads, appropriate Shibboleth directives
will need to be placed in the .htaccess file for the directory that contains the
files to be protected.

Protect MODX Resources
----------------------

Typically, a resource is protected by Shibboleth using web server directives
that require a Shibboleth session. In addition to authentication, Shibboleth can
restrict access to certain groups based on attributes provided by a user's
Identity Provider (IdP). These directives are often placed in .htaccess files to
protect different paths and directories in the web root.

Protecting MODX resources using this method can be difficult since they do not
exist on the file system. This add-on allows resources to be protected by
Shibboleth based on the value of a template variable.

To protect pages, create a template variable that will be used to designate the
resource as protected by Shibboleth. Make sure it has a 1/0 or true/false value.
Finally, add the TV to a template and set the `shibboleth.tv` system setting to
the TV name. Any resources with the TV set will trigger the ShibbolethProtect
plugin.

### Authentication and Authorization

When handling requests for protected resources, MODX will first check that the
user has an active Shibboleth session. If they do not, they will be redirected
to your server's Login handler. From there, depending on your Shibboleth
environment, they will likely be sent to a web-based sign on page. Following a
successful authentication attempt, they will eventually be directed back to their
original destination on your site.

After authentication, Shibboleth can also check that a user is authorized to
view protected content. Authorization rules can be configured in the
`shibboleth.rules` system setting, one per line. The rules should be entered
using the 'shib-attr' [Apache syntax][3]. The attributes available will vary
based on the user's IdP: contact you IdP administrator for details on the types
of information provided with each user. (One way to get an idea of the
attributes in your environment is to dump the `$_SERVER` variable of an active
Shib session)

An example rule set might look something like this:

    Require shib-attr username person.123@example.com person.456@example.com
    Require shib-attr affiliation employee contractor

Which would allow a user whose Shibboleth username attribute was either
"person.123@example.com" or "person.456@example.com" to access protected
resources. It would _also_ grant access to a user whose affiliation was
"employee" or "contractor". Note that rule conditions are evaluated using OR
relationships: a user who matches any attribute in any rule is considered
authorized.

Authorization rules can also be read from a file by placing its absolute file
system path in `shibboleth.rules_file`. The rules file can be a .htaccess file.
This arrangement could potentially be used to secure a directory of files and a
set of MODX web pages with the same set of directives.

Authenticate MODX Users
-----------------------

In addition to securing front-end content, MODX user accounts can be
authenticated with Shibboleth. This allows manager users to log in with
credentials supplied by their IdP. 

### Logging In

Users should see a 'Shibboleth Login' link on the manager login form. Clicking
this link will direct them to the handler created earlier. From there, they will
either be logged in if they already have a Shibboleth session, or sent to the
identity provider if they do not.

MODX will compare the username provided by Shibboleth against user accounts in
its database. If the username matches, that user will be logged in. If no MDOX
user is found, a new user account will be created if `shibboleth.create_users`
is set to Yes.

If MODX usernames are stored in a different format that those supplied by
Shibboleth, you can run them through the snippet configured in
`shibboleth.transform_snippet` to transform the Shibboleth username. For
example, if Shibboleth supplied usernames in the format `DOMAIN\user` but MODX
accounts use only `username`, you could use this snippet to match the two:

    return str_replace('DOMAIN\\', '', $username);

### Login Handler

You may optionally create a handler that will process all Shibboleth login
attempts. A handler is not required: you should skip these steps if you do not
need one. A handler may be useful if you need to limit Shibboleth-enabled URLs
and can be either a MODX resource _or_ a PHP script.

#### Handler Resource

  1. Create a new document in a context accessible by anonymous users (typically
     the 'web' context)
  2. Assign the resource an empty template and check "Hide from menus" and
     "Published"
  3. In the content area, call the shibHandler snippet: `[[!shibHandler]]`
  4. Enter the resource ID of the new handler document in the
     `shibboleth.handler` system setting

Make sure this resource remains published and accessible. If removed, it may
prevent users from logging in! You might consider adding it to a protected
resource group to prevent accidental alterations.

#### Handler Script

  1. Locate the example handler script in
     `core/components/shibboleth/example/handler.php`.
  2. Copy the example script to a location in your MODX web root. For example:
     `http://example.com/shibboleth.php`.
  3. If the script is not located in the same directory as `config.core.php`,
     uncomment and set the `$config_path` variable to the absolute file system
     path of a `config.core.php` file.

### Group synchronization

The groups and roles for a MODX user can optionally be synchronized with
attributes provided by Shibboleth. The mapping between MODX groups and
Shibboleth attributes is configured using the `shibboleth.group_rules` setting,
one rule per line. A group mapping rule has the following format:

    GroupName RoleName attribute value1 value2 value3 ...

A Shibboleth user with any of the attribute values will be assigned to the group
with the specified role. For example, with the rule:

    Administrator "Super User" affiliation employee manager

A user who has the "manager" or "employee" affiliation will be added to the
Administrator group with the Super User role. Conversely, if a user does not
have the employee or manager affiliation, they will be removed from the
Administrator group if they have the Super User role.

You can add a group to all users by omitting the attribute and value fields:

    Administrator "All Users Role"

System Settings
---------------

Shibboleth is configured using a number of system settings.

### Environment

  * shibboleth.session_indicator: Environment variable that indicates the
    presence of a Shibboleth session. Typically Shib-Session-ID
  * shibboleth.username_attribute: The Shibboleth attribute that contains a
    user's unique account name.
  * shibboleth.email_attribute: The Shibboleth attribute that contains a
    user's email address.
  * shibboleth.fullname_attribute: The Shibboleth attribute that contains a
    user's display name.
  * shibboleth.login_path: The relative URL for the server's Shibboleth login
    handler. Note that this is the server login handler not the MODX handler
    resource/script. Typically /Shibboleth.sso/Login
  * shibboleth.fixenv: Attempt to fix Apache mod_rewrite prepending 'REDIRECT_'
    to variable names

### Content Protection

  * shibboleth.tv: Name of the TV used to designate a protected resource
  * shibboleth.rules: Authorization rules, one per line, in Apache syntax
  * shibboleth.rules_file: absolute file system path to a file containing
    authorization rules

### User login

  * shibboleth.allow_auth: Allow MODX users to authenticate with Shibboleth
  * shibboleth.force_shib: Force MODX users to authenticate with Shibboleth.
    Prevents normal MODX password login. This could lock users out of the site
    if the IdP is unavailable
  * shibboleth.enforce_session: Logs out a MODX user that was authenticated with
    Shibboleth if their Shibboleth session ends.
  * shibboleth.create_users: Create MODX user accounts for Shibboleth users
  * shibboleth.login_param: The URL parameter used to start a Shibboleth login
    attempt
  * shibboleth.handler: ID of the resource containing the Shibboleth handler
    snippet or the full URL of the handler script. Only needed if using a login
    handler
  * shibboleth.group_rules: Group mapping rules, one per line.
    `GroupName RoleName attribute value1 value2 value3 ...`
  * shibboleth.transform_snippet: Name of the username transform snippet

### Miscelaneous

  * shibboleth.force_ssl: Force URLs to use the HTTPS scheme for SSL
    encryption. WARNING: Do not turn off this setting unless you understand the
    implications. Shibboleth offers very little security without using SSL.
  * shibboleth.login_text: Text for the Shibboleth link on the manager login
    form

Helper Snippets
---------------

Several convenience snippets can be used to gather information from the
Shibboleth session.

### ShibAuth

Tests whether the current user is authorized to view protected resources. Will
return '1' if the user is authorized and '0' if not. The snippet can be used in
any resource.

    [[!shibAuth:is=`0`:then=`class=“content-is-locked"`]]

### ShibAttr

Returns the value of the Shibboleth attribute specified in the `attribute`
property for the currently authenticated user. 

    [[!shibAttr? &attribute=`email`]]

### ShibLoginUrl

Returns the Shibboleth login URL for the current resource. Can also redirect to
an artibtrary URL by passing a `target` property.

    [[!shibLoginUrl]]
    [[!shibLoginUrl? &target=`http://www.example.com`]]

### ShibHandlerUrl

Returns the MODX Shibboleth handler URL. Set the destination context and URL in
the `context` and `target` properties.

    [[!shibHandlerUrl? &context=`mgr` &target=`/manager/`]]


Contributing
------------

The MODX Shibboleth extra is [hosted on GitHub][4]. Ideas for improvements? Bug
reports? Please open an issue in the project's issue queue.

Author
------

Written and maintained by Corey Hinshaw <hinshaw.25@osu.edu>  
Originally written for the [Ohio State University][5].

License and copyright
---------------------

Copyright 2017 Corey Hinshaw

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


[1]: https://shibboleth.net
[2]: https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPProtectContent#NativeSPProtectContent-PassiveProtection
[3]: https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPhtaccess#NativeSPhtaccess-GeneralSyntax
[4]: https://github.com/electrickite/shibboleth
[5]: https://www.osu.edu
