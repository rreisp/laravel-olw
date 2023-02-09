<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = DB::table('sales AS s')
            ->join('sellers AS sl', 'sl.id', '=', 's.seller_id')
            ->join('clients AS cl', 'cl.id', '=', 's.client_id')
            ->join('companies AS cp', 'cp.id', '=', 'sl.company_id')
            ->join('addresses AS ad', 'ad.id', '=', 'cl.address_id')
            ->join('users AS us', 'us.id', '=', 'sl.user_id')
            ->join('users AS uc', 'uc.id', '=', 'cl.user_id')
            ->selectRaw("
            cp.name AS company,
            us.name AS seller,
            uc.name AS client,
            ad.city,
            ad.state,
            s.sold_at,
            s.status,
            s.total_amount,
            round(s.total_amount * cp.commission_rate / 100) AS commission
        ")->toSql();

        DB::statement("CREATE MATERIALIZED VIEW sales_commission_view AS $query");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP MATERIALIZED VIEW sales_commission_view');
    }
};
