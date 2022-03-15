<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Storage;

class createModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createModel {ModelName? : 欲執行的Y-m-d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model by table name';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = DB::select("SELECT * FROM Information_Schema.COLUMNS where TABLE_SCHEMA = '".env('DB_DATABASE', '')."' and TABLE_NAME = '".$this->argument('ModelName', '')."'");

        $COLUMNS = array();
        $primaryKey = '';
        foreach ($result as $key => $value) {
            $COLUMNS['fillable'][] = $value->COLUMN_NAME;
            if ($value->DATA_TYPE == 'int') {
                $COLUMNS['casts'][] = array('COLUMN_NAME' => $value->COLUMN_NAME, 'TYPE' => 'int');
            }
            if ($value->COLUMN_KEY == 'PRI') {
                $primaryKey = $value->COLUMN_NAME;
            }
        }
        // dd($COLUMNS);

        $temp = Storage::get('template.php');
        $temp = str_replace('{TableName}', $this->argument('ModelName', ''), $temp);
        $temp = str_replace('{primaryKey}', $primaryKey, $temp);
        $temp = str_replace('{fillable}', implode("',\n      '", $COLUMNS['fillable']), $temp);
        $castsTemp = '';
        foreach ($COLUMNS['casts'] as $key => $casts) {
            $castsTemp .= "'".$casts['COLUMN_NAME']."' => '".$casts['TYPE']."',\n      ";
        }
        $temp = str_replace('{casts}', $castsTemp, $temp);
        // dd($temp);
        Storage::disk('local_root')->put(('app/'.$this->argument('ModelName', '').'.php'), $temp);
    }
}
