<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Validator;

class CustomValidationRulesServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {        
	
		Validator::extend('gstin', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/', $value);
        });
        Validator::extend('rating', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[1-5]{1}$/', $value);
        });
		Validator::extend('full_name', function($attribute, $value, $parameters, $validator)
        {            
			return preg_match('/^[a-zA-Z]+\s[a-zA-Z]+$/', $value);
        });
		Validator::extend('signup_first_name', function($attribute, $value, $parameters, $validator)
        {            
			return preg_match('/^[a-zA-Z]{3,50}+\s?+[a-zA-Z]?/', $value);
        }); 
		Validator::extend('signup_last_name', function($attribute, $value, $parameters, $validator)
        {            
			return preg_match('/^[a-zA-Z]{1,20}?\s+[a-zA-Z]{1,50}+$/', $value);
        }); 
        Validator::extend('firstname', function($attribute, $value, $parameters, $validator)
        {            		
			return preg_match('/^[a-zA-Z]+$/', $value);
        });
		Validator::extend('username', function($attribute, $value, $parameters, $validator)
        {            
			return preg_match('/^[a-zA-Z0-9]+$/', $value);
        });
        Validator::extend('lastname', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[a-zA-Z]+$/', $value);
			//return preg_match('/^[a-zA-Z]+\s[a-zA-Z]+$/', $value);
			//return preg_match('/^[a-zA-Z ]*$/', $value);
        });
		Validator::extend('zipcode', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{5,6}(\-[0-9]{4})?$/', $value);
        });
        Validator::extend('password', function($attribute, $value, $parameters, $validator)
        {
			return preg_match('/^\S*$/', $value);
			//return preg_match('/^\S*$/', $value);
            //return preg_match('/^\S{6,20}$/', $value);
            //return preg_match('/^\S*(?=\S{6,16})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/', $value);
        });
        Validator::extend('security_pin', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{4}$/', $value);
        });
        Validator::extend('business_name', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[A-Za-z0-9\s]{3,40}$/', $value);
        });
        Validator::extend('lat', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,16}$/', $value);
        });
        Validator::extend('lng', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,16}$/', $value);
        });
        Validator::extend('email', function($attribute, $value, $parameters, $validator)
        {
            return (preg_match('/\A[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}\z/', $value) && preg_match('/^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/', $value));
        });
        Validator::extend('composite_unique', function( $attribute, $value, $parameters, $validator )
        {
            //field_name, value, validation_rules (as array), validator
            $data = $validator->getData();
            $query = \DB::table(array_shift($parameters));
            $column = array_shift($parameters);
            $query->where($column, array_get($data, $column));
            if (count($parameters) % 2 == 0)
            {
                $i = 0;
                while ($field = array_shift($parameters))
                {
                    $value = array_shift($parameters);
                    if (strpos($value, '~'))
                    {
                        $query->whereIn($field, explode('~', $value));
                    }
                    else if ($i == 0)
                    {
                        $query->where($field, '<>', $value);
                        $i++;
                    }
                    else
                    {
                        $query->where($field, $value);
                    }
                };
            }
            else
            {
                throw new InvalidArgumentException("Validation rule composite_unique requires at least 1 parameters.");
            }
            return !$query->exists();
        });
        Validator::extend('greater', function($attribute, $value, $parameters, $validator)
        {
            $given = $validator->getValue($parameters[0]) ? $validator->getValue($parameters[0]) : $parameters[0];
            return (double) $value > (double) $given;
        });
        Validator::replacer('greater', function($message, $attribute, $rule, $parameters)
        {
            return str_replace(':value', $parameters[0], $message);
        });
		Validator::extend('lesser', function($attribute, $value, $parameters, $validator)
        {
            $given = $validator->getValue($parameters[0]) ? $validator->getValue($parameters[0]) : $parameters[0];
            return (double) $value < (double) $given;
        });
        Validator::replacer('lesser', function($message, $attribute, $rule, $parameters)
        {
            return str_replace(':value', $parameters[0], $message);
        });
        Validator::extend('greater_than_equal', function($attribute, $value, $parameters, $validator)
        {
            $given = $validator->getValue($parameters[0]) ? $validator->getValue($parameters[0]) : $parameters[0];
            return (double) $value >= (double) $given;
        });
        Validator::replacer('greater_than_equal', function($message, $attribute, $rule, $parameters)
        {
            return str_replace(':value', $parameters[0], $message);
        });
    }

	//
		/**
		 * Register the application services.
		 *
		 * @return void
		 */
    public function register ()
    {
        //
    }

}
