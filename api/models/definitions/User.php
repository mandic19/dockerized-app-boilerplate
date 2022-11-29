<?php

namespace api\models\definitions;

/**
 * @SWG\Definition(required={"username", "email", "first_name", "last_name"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="email", type="string")
 * @SWG\Property(property="username", type="string")
 * @SWG\Property(property="first_name", type="string")
 * @SWG\Property(property="last_name", type="string")
 * @SWG\Property(property="address", type="string")
 * @SWG\Property(property="city", type="string")
 * @SWG\Property(property="country", type="string")
 * @SWG\Property(property="zip", type="string")
 *
 */
class User
{
}