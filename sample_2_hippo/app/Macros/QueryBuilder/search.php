<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

Builder::macro("search", function ($attributes, string $searchTerm) {
	$searchTerm = str_replace(" ", "%", $searchTerm);
	$this->where(function (Builder $query) use ($attributes, $searchTerm) {
		foreach (Arr::wrap($attributes) as $attribute) {
			$query->orWhere($attribute, "LIKE", "%{$searchTerm}%");
		}
	});
	return $this;
});
