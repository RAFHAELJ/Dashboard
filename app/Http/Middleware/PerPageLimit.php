<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest as TransformsRequest;

class PerPageLimit extends TransformsRequest
{
    private $maxPerPage = 200;
    private $minPerPage = 1;
    private $defaultPerPage = 15;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->input('per_page') && $request->method() === 'GET'){
            $request->merge(['per_page' => $this->defaultPerPage]);
        }

        return parent::handle($request, $next);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if ($key === "per_page") {
            return $value > $this->maxPerPage? $this->maxPerPage: ($value<$this->minPerPage?$this->minPerPage:$value) ;
        }

        return $value;
    }
}
    