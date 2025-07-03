<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\Marketing\LeaseAgreement;

class available_sewa extends Command
{
    function __construct(LeaseAgreement $leaseAgreement) {
        parent::__construct();
        $this->leaseagreement = $leaseAgreement;
    }

    protected $signature = 'availableSewa';

    public function handle()
    {
        $this->leaseagreement->updateExpiredUnit();
    }

}
