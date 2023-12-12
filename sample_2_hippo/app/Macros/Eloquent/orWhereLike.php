<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

Builder::macro("orWhereLike", function ($attributes, string $searchTerm) {
	$this->orWhere(function (Builder $query) use ($attributes, $searchTerm) {
		foreach (Arr::wrap($attributes) as $attribute) {
			$query->when(
				str_contains($attribute, "."),
				function (Builder $query) use ($attribute, $searchTerm) {
					[$relationName, $relationAttribute] = explode(
						".",
						$attribute,
					);
					$query->orWhereHas($relationName, function (
						Builder $query
					) use ($relationAttribute, $searchTerm) {
						$query->where(
							$relationAttribute,
							"LIKE",
							"%{$searchTerm}%",
						);
					});
				},
				function (Builder $query) use ($attribute, $searchTerm) {
					$query->orWhere($attribute, "LIKE", "%{$searchTerm}%");
				},
			);
		}
	});
	return $this;
});
