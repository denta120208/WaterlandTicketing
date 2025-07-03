<?php


namespace App\Model\Marketing;

use Illuminate\Database\Eloquent\Model;

class LeaseAggrement extends Model{
    protected $table = 'PSM_TRANS';
    protected $primaryKey = 'PSM_TRANS_NOCHAR';
    // protected $dateFormat = 'Y-m-d H:i:s.u0';
    protected $timestamp = false;

    protected $fillable =[
        'PSM_TRANS_ID_INT',
        'PSM_TRANS_NOCHAR',
        'LOI_TRANS_NOCHAR',
        'SKS_TRANS_NOCHAR',
        'LOT_STOCK_ID_INT',
        'LOT_STOCK_NO',
        'DEBTOR_ACCT_CHAR',
        'SHOP_NAME_CHAR',
        'PSM_CATEGORY_ID_INT',
        'MD_TENANT_ID_INT',
        'PSM_TRANS_TYPE',
        'MD_SALES_TYPE_ID_INT',
        'PSM_TRANS_BOOKING_DATE',
        'PSM_TRANS_SCHEDULE_DATE',
        'PSM_TRANS_START_DATE',
        'PSM_TRANS_END_DATE',
        'PSM_TRANS_FREQ_NUM',
        'PSM_TRANS_FREQ_DAY_NUM',
        'PSM_TRANS_TIME_PERIOD_SCHED',
        'PSM_TRANS_RENT_NUM',
        'PSM_TRANS_SC_NUM',
        'PSM_TRANS_DESCRIPTION',
        'PSM_TRANS_DISKON_NUM',
        'PSM_TRANS_DISKON_PERSEN',
        'PSM_TRANS_NET_BEFORE_TAX',
        'PSM_TRANS_PPN',
        'PSM_TRANS_PRICE',
        'PSM_TRANS_UNEARN',
        'PSM_TRANS_DP_PERSEN',
        'PSM_TRANS_DP_NUM',
        'PSM_TRANS_DP_PERIOD',
        'PSM_TRANS_DEPOSIT_MONTH',
        'PSM_TRANS_DEPOSIT_TYPE',
        'PSM_TRANS_DEPOSIT_NUM',
        'PSM_TRANS_DEPOSIT_DATE',
        'PSM_INVEST_NUM',
        'PSM_INVEST_RATE',
        'PSM_MIN_AMT',
        'PSM_REVENUE_LOW_NUM',
        'PSM_REVENUE_LOW_RATE',
        'PSM_REVENUE_HIGH_NUM',
        'PSM_REVENUE_HIGH_RATE',
        'PSM_TRANS_STATUS_INT',
        'PSM_TRANS_GENERATE_BILLING',
        'PSM_TRANS_BILLING_INT',
        'PSM_TRANS_DP_BILLING_INT',
        'PSM_TRANS_GRASS_TYPE',
        'PSM_TRANS_GRASS_PERIOD',
        'PSM_TRANS_GRASS_DATE',
        'PSM_TRANS_VA',
        'PSM_BANK_GARANSI',
        'PSM_BANK_GARANSI_NOCHAR',
        'INVOICE_UTIL_CHAR',
        'PSM_TRANS_EXP_STATUS_INT',
        'IS_REVENUE_SHARING',
        'IS_AMORTIZATION',
        'PSM_TRANS_REQUEST_CHAR',
        'PSM_TRANS_REQUEST_DATE',
        'PSM_TRANS_APPR_CHAR',
        'PSM_TRANS_APPR_DATE',
        'PSM_TRANS_CANCEL_CHAR',
        'PSM_TRANS_CANCEL_DATE',
        'PROJECT_NO_CHAR',
    ];
}
