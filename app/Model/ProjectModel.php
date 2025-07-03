<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model {

	 protected $table = 'MD_PROJECT';
         protected $primaryKey = 'PROJECT_NO_CHAR';
//         protected $dateFormat = 'Y-m-d H:i:s';
         protected $fillable =[
            'PROJECT_NO_CHAR',
            'ID_COMPANY_INT',
            'PROJECT_ALIAS',
            'ID_BUSINESS_INT',
            'PROJECT_NAME',
            'PROJECT_CODE',
            'PREFIX_DEBTOR',
            'PROJECT_ACTIVE_CHAR',
            'PROJECT_DESC',
            'MONTH_PERIOD',
            'YEAR_PERIOD',
            'VA_MANDIRI',
            'PROJECT_ADDRESS',
            'PROJECT_KEL',
            'PROJECT_KEC',
            'PROJECT_KOTA',
            'PROJECT_PROPINSI',
            'PROJECT_LUAS',
             'GM_NAME',
             'DIR_OPS_MAIL',
             'GM_MAIL',
             'DIR_OPS_NAME',
             'DIRUT_NAME',
             'CHIEF_FINANCE',
             'created_at',
             'updated_at',
             'PPNBM_NUM'
            ];

        public function scopeProject($query, $project)
        {
                return $query->where('PROJECT_CODE', $project);
        }

        public function scopeProjectID($query, $project)
        {
                return $query->where('PROJECT_NO_CHAR', $project);
        }

         public function scopeProjectPrefix($query, $project)
        {
                return $query->where('PREFIX_DEBTOR', $project);
        }
}
