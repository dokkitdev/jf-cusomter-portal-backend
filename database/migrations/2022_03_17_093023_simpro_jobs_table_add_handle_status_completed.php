<?php

use Illuminate\Database\Migrations\Migration;

class SimproJobsTableAddHandleStatusCompleted extends Migration
{
    public function up()
    {
        $this->changeEnum(['new', 'error', 'completed']);
    }

    public function down()
    {
        $this->changeEnum(['new', 'error']);
    }

    protected function changeEnum($types)
    {
        DB::statement("ALTER TABLE simpro_jobs DROP CONSTRAINT simpro_jobs_handle_status_check");

        $result = join(', ', array_map(function($value) {
            return sprintf("'%s'::character varying", $value);
        }, $types));

        DB::statement("ALTER TABLE simpro_jobs add CONSTRAINT simpro_jobs_handle_status_check CHECK ((handle_status)::text = ANY ((ARRAY[$result])::text[]))");
    }
}
