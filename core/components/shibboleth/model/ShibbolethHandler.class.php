<?php

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
     * The name of the Shibboleth login URL parameter
     * @var string
     */
    protected $loginParam;

    /**
     * State of the current Shibboleth authentication attempt
     * @var bool
     */
    protected $shibAuthAttempt = false;

    /**
     * Current handler MODX user object
     * @var modUser
     */
    protected $user;


    /**
     * Constructor for a ShibbolethHandler object.
     *
     * @param Modx $modx
     *   The Modx object
     */
    public function __construct($modx, $scriptProperties = array()) {
        $this->shibUser = new ShibbolethUser($modx);
        $this->scheme = $modx->getOption('shibboleth.force_ssl', $scriptProperties, true) ? 'https://' : MODX_URL_SCHEME;
        $this->loginParam = $modx->getOption('shibboleth.login_param', $scriptProperties, 'shibboleth_login');
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
     *
     * @param string $target
     *   The destination URL. Defaults to the current page
     * @param string $context
     *   The context for the MODX login. Defaults to the current MODX context
     */
    public function doLogin($target=null, $context=null)
    {
        // Set session context to log into
        if (empty($context)) {
            $ctx = $this->modx->request->getParameters('ctx');
            $context = !empty($ctx) ? urldecode($ctx) : $this->modx->context->get('key');
        }

        // Find target URL
        if (empty($target)) {
            $tgt = $this->modx->request->getParameters($this->loginParam);
            $target = !empty($tgt) ?
                urldecode($tgt) :
                $this->alterUrlParameter($_SERVER['REQUEST_URI'], $this->loginParam, null, true);
        }

        if ( ! $this->modx->getOption('shibboleth.allow_auth', $this->scriptProperties, true)) {
            // Bail if we do not allow MODX users to authenticate via Shibboleth

        } elseif ( ! $this->shibUser->isAuthenticated()) {
            // Authenticate the user
            $this->authenticate($_SERVER['REQUEST_URI']);

        } else {
            // If the user is authenticated, match them to a MODX user and log them in
            if ($this->setModxUser()) {
                $this->syncModxGroups();
                $this->loginModxUser($context);
            }
        }

        if (!$this->modx->user || !$this->modx->user->isAuthenticated($context)) {
            $this->setError($this->modx->lexicon('shibboleth.no_account_message'));
        }

        $this->modx->sendRedirect($target);
    }

    /**
     * Logs the MODX user out of the current context if the Shibboleth session
     * has expired
     */
    public function enforceShibSession() {
        $ctx = $this->modx->context->get('key');

        if ($this->modx->user
            && $this->modx->user->hasSessionContext($ctx)
            && $this->getModxShibSession()
            && $this->shibUser->sessionId() != $this->getModxShibSession()
            && $this->modx->getOption('shibboleth.enforce_session', $this->scriptProperties, false)
        ) {
            $this->modx->user->removeSessionContext($ctx);
            $this->modx->sendRedirect($_SERVER['REQUEST_URI']);
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
    public function modxHandlerUrl($context=null, $target=null)
    {
        $handler = $this->modx->getOption('shibboleth.handler', $this->scriptProperties, null);

        if ($handler) {
            $target = $this->buildTarget($target);
            $context = empty($context) ? $this->modx->context->get('key') : $context;

            if (is_numeric($handler)) {
                $base_url = $this->modx->makeUrl(intval($handler));
            } else {
                $base_url = $handler;
            }

        } else {
            if (!empty($target)) $target = $this->buildTarget($target);
            $base_url = $_SERVER['REQUEST_URI'];
        }
        
        $url = $this->alterUrlParameter($base_url, $this->loginParam, $target);
        if ($context) {
            $url = $this->alterUrlParameter($url, 'ctx', $context);
        }

        return $url;
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
     * Determine if the user is attempting to authenticate via Shibboleth on
     * during request.
     *
     * @return bool
     *   True if this is a Shib auth attempt, false if not
     */
    public function isShibAuthAttempt()
    {
        return $this->shibAuthAttempt;
    }

    /**
     * Match the provided user to the handler's current MODX user
     *
     * @param modUser $user
     *   The user to match
     * @return bool
     *   True if the users match, false if they are different
     */
    public function matchAuthenticatedUserTo($user)
    {
        $match = false;
        if ($this->user) {
            $match = $user->get('id') == $this->user->get('id');
        }
        return $match;
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
        $this->user = $this->modx->getObject('modUser', array('username' => $username));

        if (!$this->user && $this->modx->getOption('shibboleth.create_users', $this->scriptProperties, false)) {
            // Add a new user
            $this->user = $this->modx->newObject('modUser', array('username' => $username));
            $userProfile = $this->modx->newObject('modUserProfile');
            $userProfile->set('email', $this->shibUser->email());
            $userProfile->set('fullname', $this->shibUser->fullname());
            $this->user->addOne($userProfile);
            if ($this->user->save() == false) {
                $this->user = null;
            }
        }

        return $this->user;
    }

    /**
     * Synchronize MODX user groups with Shibboleth using attribute mapping
     * rules
     */
    protected function syncModxGroups()
    {
        $this->requiresUser();
        $groups = $this->getModxGroups();

        foreach($groups['join'] as $group => $role) {
            $this->user->joinGroup($group, $role);
        }
        foreach($groups['leave'] as $group => $role) {
            $this->leaveGroup($group, $role);
        }
    }

    protected function leaveGroup($groupName, $roleName)
    {
        $this->requiresUser();
        $group = $this->modx->getObject('modUserGroup', array('name' => $groupName));
        $role = $this->modx->getObject('modUserGroupRole', array('name' => $roleName));

        if (!empty($group) && !empty($role)) {
            $member = $this->modx->getObject('modUserGroupMember', array(
                'member' => $this->user->get('id'),
                'user_group' => $group->get('id'),
                'role' => $role->get('id'),
            ));

            if (!empty($member)) {
                $this->user->leaveGroup($group->get('id'));
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
        $groups = array('join'=>array(), 'leave'=>array());
        
        foreach ($lines as $line) {
            $line = trim($line);

            $line_parts = str_getcsv($line, ' ');
            if (count($line_parts) >= 2) {
                $group = array_shift($line_parts);
                $role = array_shift($line_parts);

                if (empty($line_parts) || $this->shibUser->checkRules(array($line_parts))) {
                    $action = 'join';
                } else {
                    $action = 'leave';
                }
                $groups[$action][$group] = $role;
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

        $this->startAuthAttempt();

        $response = $this->modx->runProcessor('security/login', array(
            'username'      => $this->user->get('username'),
            'password'      => 'notapassword',
            'login_context' => $context,
        ));

        $this->endAuthAttempt();

        if (!$response->isError()) {
            // Flag this MODX session as a Shibboleth session
            $this->setModxShibSession($this->shibUser->sessionId());
        }
    }

    /**
     * Inidicate that the user is attempting to authenticate via Shib
     */
    protected function startAuthAttempt()
    {
        $this->shibAuthAttempt = true;
    }

    /**
     * Inidicate that the user is no longer attempting to authenticate via Shib
     */
    protected function endAuthAttempt()
    {
        $this->shibAuthAttempt = false;
    }

    /**
     * Ensures a MODX user has been set
     */
    protected function requiresUser()
    {
        if (!$this->user) throw new RuntimeException('MODX user not set');
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
     * @param bool $encode
     *   If true, encode the URL
     * @return string
     *   The sanitized target URL
     */
    protected function buildTarget($target=null, $encode=true)
    {
        if (empty($target)) {
            $target = isset($this->modx->resource) ? 
                $this->modx->makeUrl($this->modx->resource->get('id'),'','','http') :
                $_SERVER['REQUEST_URI'];
        }

        $target = filter_var($target, FILTER_SANITIZE_URL);
        $url_parts = parse_url($target);
        if ($url_parts && ! isset($url_parts['host'])) {
            $slash = substr($target, 0, 1) == '/' ? '' : '/';
            $target = MODX_URL_SCHEME.MODX_HTTP_HOST.$slash.$target;
        }
        $url = str_replace('http://', $this->scheme, $target);

        return $encode ? urlencode($url) : $url;
    }

    /**
     * Alters a query string parameter and returns the full URL
     *
     * @param string $url
     *   The URL strinto alter
     * @param string $param
     *   The parameter name to alter
     * @param string $value
     *   The value for the parameter. If null, output will contain an empty
     *   param (with no =)
     * @param bool $remove
     *   Removes the paramter if true
     *
     * @return string
     *   The sanitized target URL
     */
    protected function alterUrlParameter($url, $param, $value, $remove=false)
    {
        $url_parts = parse_url($url);
        $query_parts = array();
        $new_query = array();

        if (isset($url_parts['query']))
            parse_str($url_parts['query'], $query_parts);

        if ($remove) {
            unset($query_parts[$param]);
        } else {
            $query_parts[$param] = $value;
        }

        // Build the query string
        foreach($query_parts as $param => $value) {
           $new_query[] = $value !== null ? urlencode($param).'='.urlencode($value) : urlencode($param);
        }
        $query = implode('&', $new_query);

        // Reconstruct the URL
        $path = isset($url_parts['path']) ? $url_parts['path'] : '';
        $query = !empty($query) ? '?' . $query : '';
        $fragment = isset($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '';

        return $this->buildTarget($path.$query.$fragment, false);
    }
    
}
