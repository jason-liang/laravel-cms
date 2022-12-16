<?php

namespace App\Admin\Imports;

use App\Models\OnDuty;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class OnDutyImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        if ($rowIndex === 0 || $rowIndex === 1 || $rowIndex === 2)
            return null;

        $row = $row->toArray();

        if (!isset($row[0])) {
            $index = (int)$rowIndex + 1;
            throw new \Exception("第{$index}行没有日期，这是是不行地～");
        }

        if (OnDuty::whereDate('date', $row[0])->first()) {
            throw new \Exception($row[0] . '该日期已有值班记录了！');
        }
  
        OnDuty::create([
            'date' => $row[0],
            'content' => $row[1]
        ]);
    }
}
