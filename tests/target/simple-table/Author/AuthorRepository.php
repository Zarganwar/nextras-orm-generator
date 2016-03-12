<?php

class AuthorRepository extends Nextras\Orm\Repository\Repository
{

	/**
	 * @return array
	 */
	public static function getEntityClassNames()
	{
		return [Author::class];
	}

}
