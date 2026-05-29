<?php

use App\Models\EducacensoDegree;
use App\Models\EmployeeGraduation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            '0615S013' => '0612S013',
            '0615S014' => '0612S014',
            '0614C012' => '0613C012',
            '0614C013' => '0613C013',
            '0614C014' => '0613C014',
            '0614I012' => '0613I012',
            '0614I013' => '0613I013',
            '0615S022' => '0613S022',
            '0615S023' => '0613S023',
            '0615S024' => '0613S024',
            '0615S032' => '0613S032',
            '0615S033' => '0613S023',
            '0615S034' => '0613S034',
            '0616I013' => '0613I013',
            '0613J012' => '0681J012',
            '0613J013' => '0681J013',
            '0613J014' => '0681J014',
            '0617A013' => '0681A013',
            '0617C012' => '0681C012',
            '0617C013' => '0681C013',
            '0617C022' => '0681C022',
            '0617C023' => '0681C023',
            '0617C032' => '0681C032',
            '0617C033' => '0681C033',
            '0616S013' => '0714S013',
        ];

        foreach ($updates as $old => $new) {

            $courseOldId = EducacensoDegree::query()
                ->where('curso_id', $old)
                ->value('id');

            $courseNewId = EducacensoDegree::query()
                ->where('curso_id', $new)
                ->value('id');

            if ($courseOldId && $courseNewId) {
                EmployeeGraduation::query()
                    ->where('course_id', $courseOldId)
                    ->update([
                        'course_id' => $courseNewId,
                    ]);
            }

            EducacensoDegree::query()
                ->where('curso_id', $old)
                ->delete();
        }
    }
};
