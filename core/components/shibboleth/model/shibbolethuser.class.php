<?php
/**
 * Represents user who should be authenticated via Shibboleth.
 *
 * @package shibboleth
 */

class ShibbolethUser extends ShibbolethBase
{
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
