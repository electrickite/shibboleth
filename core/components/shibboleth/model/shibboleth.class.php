<?php
/**
 * Represents user who should be authenticated via Shibboleth.
 */
class ShibbolethUser extends ShibbolethBase {

    /**
     * The authorization rules for this user
     * @var array
     */
    protected $rules;


    /**
     * Checks that the user has been authenticated.
     *
     * @return bool
     *   Returns true if the user has a Shibboleth session, false if not
     */
    public function isAuthenticated()
    {
        $id = $this->sessionId();
        return !empty($id);
    }

    /**
     * Returns the specified Shibboleth attribute for this user
     *
     * @param string $attribute
     *   Name of the Shibboleth attribute to retrieve
     * @return mixed
     *   The attribute value
     */
    public function getAttribute($attribute)
    {
        return isset($_SERVER[$attribute]) ? $_SERVER[$attribute] : null;
    }

    /**
     * Returns the Shibboleth session ID
     *
     * @return string
     *   The session ID
     */
    public function sessionId()
    {
        return $this->getAttribute($this->modx->getOption('shibboleth.session_indicator', $this->scriptProperties, 'Shib-Session-ID'));
    }

    /**
     * Returns the username
     *
     * @return string
     *   The username
     */
    public function username()
    {
        return $this->getAttribute($this->modx->getOption('shibboleth.username_attribute', $this->scriptProperties, 'REMOTE_USER'));
    }

    /**
     * Returns the user email address
     *
     * @return string
     *   The email address
     */
    public function email()
    {
        return $this->getAttribute($this->modx->getOption('shibboleth.email_attribute', $this->scriptProperties, 'EMAIL'));
    }

    /**
     * Returns the display name
     *
     * @return string
     *   The display name
     */
    public function fullname()
    {
        return $this->getAttribute($this->modx->getOption('shibboleth.fullname_attribute', $this->scriptProperties, 'DISPLAYNAME'));
    }

    /**
     * Checks that the user is authorized to view protected pages. If no
     * authorization rules are present, the user is authorized by default.
     *
     * @return bool
     *   Returns true if the user is authorized, false if not
     */
    public function isAuthorized()
    {
        $this->setRules();
        return $this->isAuthenticated() && $this->checkRules($this->rules);
    }

    /**
     * Set authorization rules for this user, either from the input parameter or
     * by searching through system settings.
     *
     * @param string $rules
     *   A string containing authorization rules to apply to this user
     */
    public function setRules($rules = null)
    {
        if ($rules) {
            $this->rules = $rules;
        } elseif (!$this->rules) {
            $this->findRules();
        }

        if (!is_array($this->rules)) $this->rules = array($this->rules);
    }

    /**
     * Checks authorization rules against environment variables.
     *
     * @return bool
     *   True if at least one rule is matched or there are no rules, false if no
     *   rules match.
     */
    public function checkRules($rules=array())
    {
        // If we have no authorization rules, the user is authorized
        $authorized = empty($rules) ? true : false;
    
        foreach ($rules as $rule) {
            $attr = array_shift($rule);
            foreach ($rule as $value) {
                $shib_values = preg_split('/(?<!\\\);/', $this->getAttribute($attr));
                $shib_values = str_replace('\;', ';', $shib_values);

                if (in_array($value, $shib_values)) {
                    $authorized = true;
                    continue(2);
                }
            }
        }

        return $authorized;
    }


    /**
     * Finds authorization rules by looking in system settings or files
     */
    protected function findRules()
    {
        if (! $rules = $this->modx->cacheManager->get('shib_rules')) {

            // If we don't have rules in the cache, grab them from either an option or a file
            $rules_file = $this->modx->getOption('shibboleth.rules_file', $this->scriptProperties);

            if ( ! $rules_raw = $this->modx->getOption('shibboleth.rules', $this->scriptProperties)) {
                if ($rules_file) {
                    $rules_raw = file_get_contents($rules_file);
                }
            }

            $lines = explode("\n", $rules_raw);
            $rules = array();
            
            foreach ($lines as $line) {
                if (strpos($line, '#') !== false) {
                    if ($line[0] == '#') continue;
                    $line = trim(strstr($line, '#', true));
                }
            
                $line_parts = str_getcsv($line, ' ');
                if (strtolower(array_shift($line_parts)) == 'require' && strtolower(array_shift($line_parts)) == 'shib-attr') {
                    $rules[] = $line_parts;
                }
            }

            // However we get the rules array, cache it for later use
            $this->modx->cacheManager->set('shib_rules', $rules, 86400);
        }

        $this->rules = $rules;
    }

}



/**
 * Encapsulates Shibboleth login handler functions
 */
class ShibbolethHandler extends ShibbolethBase  {

    /**
     * The Shibboleth user object
     * @var ShibbolethUser
     */
    protected $shibUser;

    /**
     * The URL scheme for the current request
     * @var string
     */
    protected $scheme;


    /**
     * Constructor for a ShibbolethHandler object.
     *
     * @param Modx $modx
     *   The Modx object
     */
    public function __construct($modx, $scriptProperties = array()) {
        $this->shibUser = new ShibbolethUser($modx);
        $this->scheme = $modx->getOption('shibboleth.force_ssl', $scriptProperties, true) ? 'https://' : MODX_URL_SCHEME;
        return parent::__construct($modx, $scriptProperties);
    }

    /**
     * Retrieves the Shibboleth user for this handler
     *
     * @return ShibbolethUser
     *   The Shibboleth user
     */
    public function shibUser()
    {
        return $this->shibUser;
    }

    /**
     * Authenticates the user by redirecting through the Shibboleth SSO.
     *
     * @param string $target
     *   Redirect the user to this URL after authentication
     */
    public function authenticate($target=null)
    {
        $this->modx->sendRedirect($this->loginUrl($target));
    }

    /**
     * Builds a Shibboleth login service URL with a sane target.
     *
     * @param string $target
     *   Redirect the user to this URL after authentication
     * @return string
     *   The Shibboleth login URL
     */
    public function loginUrl($target=null)
    {
        $target = $this->buildTarget($target);
        $login_path = $this->modx->getOption('shibboleth.login_path', $this->scriptProperties, '/Shibboleth.sso/Login');
        return sprintf('%s%s%s?target=%s', $this->scheme, MODX_HTTP_HOST, $login_path, $target);
    }

    /**
     * Handles a Shibboleth login attempt. Authenticates the user if necessary
     * and redirects them to their destination.
     */
    public function doLogin()
    {
        // Set session context to log into
        if (isset($this->scriptProperties['authContext'])) {
            $context = $this->scriptProperties['authContext'];
        } elseif ($this->modx->request->getParameters('ctx')) {
            $context = $this->modx->request->getParameters('ctx');
        } else {
            $context = $this->modx->context->get('key');
        }

        // Find target URL
        if (isset($this->scriptProperties['target'])) {
            $target = $this->scriptProperties['target'];
        } elseif ($this->modx->request->getParameters('target')) {
            $target = urldecode($this->modx->request->getParameters('target'));
        } else {
            $target = $this->modx->getOption('site_url', null, MODX_SITE_URL);
        }


        if ( ! $this->modx->getOption('shibboleth.allow_auth', $this->scriptProperties, true)) {
            // Bail if we do not allow MODX users to authenticate via Shibboleth

        } elseif ( ! $this->shibUser->isAuthenticated()) {
            // Authenticate the user
            $this->authenticate($this->modxHandlerUrl($context, $target));

        } else {
            // If the user is authenticated, match them to a MODX user and log them in
            if ($this->setModxUser()) {
                $this->syncModxGroups();
                $this->loginModxUser($context);
            }
        }

        if (!$this->modx->user->isAuthenticated($context)) {
            $this->setError($this->modx->lexicon('shibboleth.no_account_message'));
        }

        $this->modx->sendRedirect($target);
    }

    public function enforceShibSession() {
        if ($this->modx->user
            && $this->getModxShibSession()
            && $this->shibUser->sessionId() != $this->getModxShibSession()
            && $this->modx->getOption('shibboleth.enforce_session', $this->scriptProperties, false)
        ) {
            $this->modx->user->removeSessionContext($this->modx->context->get('key'));
        }
    }

    /**
     * Checks that this MODX session was authenticated with Shibboleth
     *
     * @return string
     *   The Shibboleth session ID that was used to start the MODX sesson or
     *   null if the session was not started using Shibboleth
     */
    public function getModxShibSession()
    {
        return isset($_SESSION['shibboleth_session']) ? $_SESSION['shibboleth_session'] : null;
    }

    /**
     * Builds a MODX login handler URL with a sane target.
     *
     * @param string $context
     *   The MODX context to for the login attempt
     * @param string $target
     *   Redirect the user to this URL after authentication
     * @return string
     *   The MODX login handler URL
     */
    public function modxHandlerUrl($context, $target=null)
    {
        $handler = $this->modx->getOption('shibboleth.handler', $this->scriptProperties);
        if (is_numeric($handler)) {
            $secure = str_replace('://', '', $this->scheme);
            return $this->modx->makeUrl(intval($handler), '', array('ctx' => $context, 'target' => $this->buildTarget($target)), $secure);
        } else {
            $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
            return sprintf('%s%sctx=%s&target=%s', $handler, $separator, $context, $target);
        }
    }

    /**
     * Attempts to work around Apache prepending REDIRECT_ to environment
     * variables
     */
    public function fixEnvironment()
    {
        if ($this->modx->getOption('shibboleth.fixenv', $this->scriptProperties, true)) {
            foreach ($_SERVER as $key => $value)
                if (substr_compare($key, "REDIRECT_", 0, 9) == 0)
                    $_SERVER[preg_replace('/REDIRECT_/', '', $key)] = $value;
        }
    }

    /**
     * Retrieves Shibboleth errors stored in the user session
     *
     * @return string
     *   The Shibboleth error
     */
    public function getError()
    {
        return isset($_SESSION['shibboleth_error']) ? $_SESSION['shibboleth_error'] : null;
    }

    /**
     * Store an error message in the user session
     *
     * @param string $message
     *   The error message
     */
    public function setError($message=null)
    {
        $_SESSION['shibboleth_error'] = $message;
    }


    /**
     * Searches the for a MODX user matching the authenticated Shibboleth user.
     * Creates a new MODX user account if necessary.
     *
     * @return mixed
     *   The modUser object if a user is found, null if not
     */
    protected function setModxUser()
    {
        $username = $this->shibUser->username();

        // If a username trasform snippet is configured, run it
        if ($transform = $this->modx->getOption('shibboleth.transform_snippet', $this->scriptProperties, null)) {
            $username = $this->modx->runSnippet($transform, array('username' => $username));
        }

        // Check the MODX database for our Shibboleth user name
        $this->modx->user = $this->modx->getObject('modUser', array('username' => $username));

        if (!$this->modx->user && $this->modx->getOption('shibboleth.create_users', $this->scriptProperties, false)) {
            // Add a new user
            $this->modx->user = $this->modx->newObject('modUser', array('username' => $username));
            $userProfile = $this->modx->newObject('modUserProfile');
            $userProfile->set('email', $this->shibUser->email());
            $userProfile->set('fullname', $this->shibUser->fullname());
            $this->modx->user->addOne($userProfile);
            if ($this->modx->user->save() == false) {
                $this->modx->user = null;
            }
        }

        return $this->modx->user;
    }

    /**
     * Synchronize MODX user groups with Shibboleth using attribute mapping
     * rules
     */
    protected function syncModxGroups()
    {
        $this->requiresUser();

        foreach($this->getModxGroups() as $group => $role) {
            if ($role) {
                $this->modx->user->joinGroup($group, $role);
            } else {
                $this->modx->user->leaveGroup($group);
            }
        }
    }

    /**
     * Determine the MODX groups/roles associated with this shibboleth user
     *
     * @return array
     *   An array of MODX groups, keyed by group name. Values will either be the
     *   user role witrhin the group, or false of the user is not a group member 
     */
    protected function getModxGroups()
    {
        $group_rules = $this->modx->getOption('shibboleth.group_rules', $this->scriptProperties);
        $lines = explode("\n", $group_rules);
        $groups = array();
        
        foreach ($lines as $line) {
            $line = trim($line);

            $line_parts = str_getcsv($line, ' ');
            if (count($line_parts >= 3)) {
                $group = array_shift($line_parts);
                $role = array_shift($line_parts);
                if (!isset($groups[$group])) $groups[$group] = false;

                $groups[$group] = $this->shibUser->checkRules(array($line_parts)) ? $role : $groups[$group];
            }
        }

        return $groups;
    }

    /**
     * Logs in the MODX user to the specified context
     *
     * @param string $context
     *   The context key
     */
    protected function loginModxUser($context)
    {
        $this->requiresUser();

        // Log user in
        if (!$this->modx->user->hasSessionContext($context)) {
            $this->modx->user->addSessionContext($context);
        }

        // Flag this MODX session as a Shibboleth session
        $this->setModxShibSession($this->shibUser->sessionId());
    }

    /**
     * Ensures a MODX user has been set
     */
    protected function requiresUser()
    {
        if (!$this->modx->user) throw new RuntimeException('MODX user not set');
    }

    /**
     * Store the Shibboleth session ID in the MODx user session
     *
     * @param string $sessionId
     *   The Shibboleth session ID
     */
    protected function setModxShibSession($sessionId)
    {
        $_SESSION['shibboleth_session'] = $sessionId;
    }

    /**
     * Builds a sane target URL.
     *
     * @param string $target
     *   A target URL
     * @return string
     *   The sanitized target URL
     */
    protected function buildTarget($target=null)
    {
        if ($target) {
            $target = filter_var($target, FILTER_SANITIZE_URL);
            $url_parts = parse_url($target);
            if ($url_parts && ! isset($url_parts['host'])) {
                $slash = substr($target, 0, 1) == '/' ? '' : '/';
                $target = MODX_URL_SCHEME.MODX_HTTP_HOST.$slash.$target;
            }
        } elseif (isset($this->modx->resource)) {
            $target = $this->modx->makeUrl($this->modx->resource->get('id'),'','','http');
        } else {
            $target = $this->modx->getOption('site_url', null, MODX_SITE_URL);
        }

        return urlencode(str_replace('http://', $this->scheme, $target));
    }
    
}



/**
 * Common methods and properties for Shibboleth classes functions
 */
class ShibbolethBase {

    /**
     * The Modx object for this request
     * @var Modx
     */
    protected $modx;

    /**
     * Optional properties array from the calling script
     * @var array
     */
    protected $scriptProperties;

    /**
     * Constructor for a ShibbolethUser object.
     *
     * @param Modx $modx
     *   The Modx object
     */
    public function __construct($modx, $scriptProperties = array()) {
        $this->modx = $modx;
        $this->scriptProperties = $scriptProperties;
    }

}
