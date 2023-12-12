<?php

use Illuminate\Database\Query\Builder;

Builder::macro("whereDateBetween", function (
	$column,
	string $timezone,
	$startDate,
	$endDate
) {
	$this->where(function (Builder $query) use (
		$column,
		$timezone,
		$startDate,
		$endDate
	) {
		return $query->whereRaw(
			"DATE(CONVERT_TZ({$column}, 'UTC', ?)) BETWEEN ? AND ?",
			[$timezone, $startDate, $endDate],
		);
	});

	return $this;
});
