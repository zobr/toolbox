<?php

namespace Zobr\Toolbox;

use Exception;

/**
 * This is a configuration class, which can be used to retrieve
 * configuration based on the current environment defined in APP_ENV.
 */
class Config {

    /**
     * Config storage
     * @var array
     */
    private $configs = [];

    /**
     * Current environment string, inherited from APP_ENV.
     * @var string
     */
    private $env;

    /**
     * @param string $configDir Directory for configuration files.
     */
    public function __construct(string $configDir) {
        $configDir = realpath($configDir);
        $this->env = getenv('APP_ENV') ? getenv('APP_ENV') : 'local';
        $this->configs['default'] = require $configDir . '/default.php';
        // Add an environment specific config
        $envConfig = $configDir . '/' . $this->env . '.php';
        if (file_exists($envConfig)) {
            $this->configs[$this->env] = require $envConfig;
        }
    }

    /**
     * Returns a config entry.
     * Path is a dot-delimited reference to the config entry. If no path is
     * provided, it returns the whole config for the current environment.
     * @param  string|null $path Example: 'slim.renderer.template_path'.
     * @param  string|null $namespace Chooses where to look for the path.
     * @return mixed
     * @throws \Exception
     */
    public function get($path = null, $namespace = null) {
        // Setup the special 'none' value
        static $none = null;
        if (!$none) {
            $none = uniqid('config_none_');
        }
        // Default to current environment namespace
        if ($namespace === null) {
            $namespace = $this->env;
        }
        // Default to non-existing entry (use a special value)
        $entry = $none;
        // Namespace config is not empty
        if (isset($this->configs[$namespace])) {
            // Assign the config
            $config = $this->configs[$namespace];
            // If no path is specified, whole config is the default.
            $entry = $config;
            // Path was specified
            if ($path !== null) {
                // Traverse the namespace config
                $levels = explode('.', $path);
                foreach ($levels as $level) {
                    // Nothing to see here
                    if (!isset($entry[$level])) {
                        $entry = $none;
                        break;
                    }
                    // Go deeper into the config
                    $entry = $entry[$level];
                }
            }
        }
        // Entry not found
        if ($entry === $none) {
            // Nothing was found
            if ($namespace === 'default') {
                throw new Exception("Config path '{$path}'' does not exist!");
            }
            // Try with default namespace
            return $this->get($path, 'default');
        }
        return $entry;
    }

}
