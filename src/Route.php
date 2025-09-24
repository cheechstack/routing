<?php

declare(strict_types=1);

namespace Cheechstack\Routing;

use Exception;

class Route
{

    public ?string $name {
        get { return $this->name; }
        set (string|null $val) { $this->name = $val; }
    }
    public string $path {
        get { return $this->path; }
    }
    public string $method {
        get { return $this->method; }
    }
    protected array $parameters;
    protected \Closure|array $callback;

    /**
     * @param string $path
     * @param string $method
     * @param callable $callback
     */
    public function __construct(string $path, string $method, callable $callback)
    {
        $this->name = null;
        $this->path = $path;
        $this->method = $method;
        $this->callback = $callback;

        $this->parameters = $this->initPathParams(); // fill the template array
    }

    /** Initializes the parameters array from the Route's path. The defined
     * path parameters become the keys of the retuning array with all initial
     * values being null.
     *
     * @return array
     */
    private function initPathParams() : array
    {
        $params = array();

        // Split the path into tokens
        $tokens = explode("/", $this->path);
        foreach ($tokens as $token) {
            // Look for the ":" identifier
            if (str_starts_with($token, ":")) {
                // Remove the identifier and set the value as a new parameter key
                $key = substr($token, 1);
                $params[$key] = null;
            }
        }

        return $params;
    }

    /** Assign the user-supplied path parameters to their corresponding keys
     * in the Routes parameters.
     *
     * @param string $searchPath    The pathInfo() of the request object.
     */
    public function resolvePathParams(string $searchPath) : void
    {
        $searchTokens = explode("/", $searchPath);
        $pathTokens = explode('/', $this->path);

        for ($i = 0; $i < count($pathTokens); $i++) {
            $pToken = $pathTokens[$i];
            if (str_starts_with($pToken, ":")) {
                $key = substr($pToken, 1);

                $this->parameters[$key] = $searchTokens[$i];
            }
        }
    }

    public function getPathParameters() : array {
        return $this->parameters;
    }

    /** Get the Route's callback function
     *
     * @return \Closure|array
     */
    public function getCallable() : \Closure|array
    {
        return $this->callback;
    }

    /** Determine if a Route object matches a requested path and method.
     *
     * @param string $searchPath
     * @param string $method
     * @return bool
     */
    public function matches(string $searchPath, string $method) : bool
    {
        // Create the token lists
        $searchTokens = explode("/", $searchPath);
        // Remove the empty first elements
        if (empty($searchTokens[0])) {
            array_shift($searchTokens);
        }
        $pathTokens = explode("/", $this->path);
        // Remove the empty first elements
        if (empty($pathTokens[0])) {
            array_shift($pathTokens);
        }

        // Return false if the request methods don't match
        if ($this->method !== $method) {
            return false;
        }

        // Verify each token list is the same length
        $sLen = count($searchTokens);
        $pLen = count($pathTokens);
        if ($pLen !== $sLen) {
            return false;
        }

        // Compare each search token against it's counterpart
        for ($i = 0; $i < $sLen; $i++) {
            $pToken = $pathTokens[$i];
            // Ignore placeholders
            if (str_starts_with($pToken, ":")) {
                continue;
            }

            // Return false if the tokens don't match at any point
            if ($pToken !== $searchTokens[$i]) {
                return false;
            }
        }

        return true;
    }
}