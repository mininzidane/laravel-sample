<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

trait HasName
{
	public $hasName = true;
	protected $nameFields = ["name"];

	public function getNameFields()
	{
		return $this->nameFields;
	}

	public function scopeNameLike($query, $name)
	{
		if (empty($this->nameFields) || !$name) {
			return;
		}

		$tableNamePrefixedNameColumns = [];

		foreach ($this->nameFields as $nameField) {
			$tableNamePrefixedNameColumns[] = $this->table . "." . $nameField;
		}

		$fields = implode(", ", $tableNamePrefixedNameColumns);

		$nameSearch = "%" . str_replace(" ", "%", $name) . "%";

		$query->where(DB::raw("concat_ws('',{$fields})"), "like", $nameSearch);
	}
}
