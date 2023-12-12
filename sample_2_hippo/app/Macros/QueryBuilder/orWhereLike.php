<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

Builder::macro("orWhereLike", function ($attributes, string $searchTerm) {
	$this->orWhere(function (Builder $query) use ($attributes, $searchTerm) {
		foreach (Arr::wrap($attributes) as $attribute) {
			$query->orWhere($attribute, "LIKE", "%{$searchTerm}%");
		}
	});
	return $this;
});
