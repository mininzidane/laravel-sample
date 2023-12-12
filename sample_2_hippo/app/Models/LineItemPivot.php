<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LineItemPivot extends Pivot
{
	protected $table = "ospos_sales_items";
}
