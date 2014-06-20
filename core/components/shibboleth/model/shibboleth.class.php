<?php
/**
 * Contains the class definition of ShibbolethUser.
 */

/**
 * Represents user who should be authenticated via Shibboleth.
 */
class ShibbolethUser {

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
     * The authorization rules for this user
     * @var array
     */
    protected $rules;

    /**
     * The MODX user object
     * @var modUser
     */
    protected $user;

    /**
     * The URL scheme for the current request
     * @var string
     */
    protected $scheme;


    /**
     * Constructor for a ShibbolethUser object.
     *
     * @param Modx $modx
     *   The Modx object
     */
    public function __construct($modx, $scriptProperties = array()) {
        $this->modx = $modx;
        $this->scriptProperties = $scriptProperties;
        $this->scheme = $this->modx->getOption('shibboleth.force_ssl', $this->scriptProperties, true) ? 'https://' : MODX_URL_SCHEME;
    }

    /**
     * Checks that the user has been authenticated.
     *
     * @return bool
     *   Returns true if the user has a Shibboleth session, false if not
     */
    public function isAuthenticated()
    {
        $attr = $this->modx->getOption('shibboleth.session_indicator', $this->scriptProperties, 'Shib-Session-ID');
        return isset($_SERVER[$attr]) && $_SERVER[$attr] != '';
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
        $handler_id = $this->modx->getOption('shibboleth.handler', $this->scriptProperties);
        return $this->modx->makeUrl($handler_id, '', array('ctx' => $context, 'target' => urlencode($target)));
    }

    /**
     * Builds a sane target URL.
     *
     * @param string $target
     *   A target URL
     * @return string
     *   The sanitized target URL
     */
    public function buildTarget($target=null)
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

        return str_replace('http://', $this->scheme, $target);
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
     * Searches the for a MODX user matching the authenticated Shibboleth user.
     * Creates a new MODX user account if necessary.
     *
     * @return mixed
     *   The modUser object if a user is found, null if not
     */
    public function setModxUser()
    {
        $username = $this->getAttribute($this->modx->getOption('shibboleth.username_attribute', $this->scriptProperties, 'REMOTE_USER'));
        $email = $this->getAttribute($this->modx->getOption('shibboleth.email_attribute', $this->scriptProperties, 'EMAIL'));
        $fullname = $this->getAttribute($this->modx->getOption('shibboleth.fullname_attribute', $this->scriptProperties, 'DISPLAYNAME'));

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
            $userProfile->set('email', $email);
            $userProfile->set('fullname', $fullname);
            $this->user->addOne($userProfile);
            if ($this->user->save() == false) {
                $this->user = null;
            }
        }

        return $this->user;
    }

    /**
     * Gets the MODX user associated with this Shibboleth user
     *
     * @return mixed
     *   The modUser object if a user is found, null if not
     */
    public function getModxUser()
    {
        return $this->user;
    }

    /**
     * Synchronize MODX user groups with Shibboleth using attribute mapping
     * rules
     */
    public function syncModxGroups()
    {
        $this->requiresUser();

        foreach($this->getModxGroups() as $group => $role) {
            if ($role) {
                $this->user->joinGroup($group, $role);
            } else {
                $this->user->leaveGroup($group);
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
    public function getModxGroups()
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

                $groups[$group] = $this->checkRules(array($line_parts)) ? $role : $groups[$group];
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
    public function loginModxUser($context)
    {
        $this->requiresUser();

        // Set MODX instance user
        $this->modx->user =& $this->user;

        // Log user in
        if (!$this->user->hasSessionContext($context)) {
            $this->user->addSessionContext($context);
        }
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

    /**
     * Checks authorization rules against environment variables.
     *
     * @return bool
     *   True if at least one rule is matched or there are no rules, false if no
     *   rules match.
     */
    protected function checkRules($rules=array())
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
     * Ensures a MODX user has been set
     */
    protected function requiresUser()
    {
        if (!$this->user) throw new RuntimeException('MODX user not set');
    }

}
