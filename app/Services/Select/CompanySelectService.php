<?php

namespace App\Services\Select;

use Illuminate\Support\Facades\DB;

class CompanySelectService
{
    public function getAllCompanies()
    {
       $companies = DB::table('vehicles')
    ->selectRaw('MIN(company_name) as value, company_name as label')
    ->groupBy('company_name')
    ->get();


        return $companies;
    }

}
