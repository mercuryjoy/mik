<?php

namespace App\Traits;

trait UserTraits
{
	/**
	 * [checkTelephoneIsRegistered]
	 * @param  [string] $telephone [手机号]
	 * @return [boolean]
	 */
	public function checkTelephoneIsRegistered($telephone)
	{
		$is_registered = $this->withTrashed()
							  ->whereTelephone($telephone)
							  ->count();
		return $is_registered;
	}

	/**
	 * [checkTelephoneIsRegistered]
	 * @param  [string] $telephone [手机号]
	 * @return [boolean]
	 */
	public function checkTelephoneIsTrashed($telephone)
	{
		$telephone = $this->withTrashed()
						  ->whereTelephone($telephone)
						  ->first();
	
		return $telephone->trashed();
	}
}