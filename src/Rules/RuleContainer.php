<?php

namespace OhKannaDuh\Repositories\Rules;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use OhKannaDuh\Repositories\Rules\Contracts\ProvidesRules;

class RuleContainer
{
    use ForwardsCalls;

    /** @var Collection<ProvidesRules> */
    protected $providers;

    /** @var Collection<string,string> */
    protected $rules;

    /**
     * Create the providers and rules collection.
     */
    public function __construct()
    {
        $this->providers = new Collection();
        $this->rules = new Collection();
    }

    /**
     * @param ProvidesRules $provider
     *
     * @return void
     */
    public function register(ProvidesRules $provider): void
    {
        $key = get_class($provider);
        $this->providers->put($key, $provider);

        foreach ($provider->provides() as $rule) {
            $this->rules[$rule] = $key;
        }
    }

    /**
     * @param string $method
     * @param mixed $parameters
     *
     * @return string[]
     */
    public function __call($method, $parameters): array
    {
        if (!$this->rules->has($method)) {
            // :C
        }

        $provider = $this->providers[$this->rules[$method]];
        $rules = $this->forwardCallTo($provider, $method, $parameters);

        if (is_array($rules)) {
            return $rules;
        }

        return [$rules];
    }
}
