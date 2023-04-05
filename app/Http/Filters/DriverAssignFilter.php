<?php

namespace App\Http\Filters;
use App\Models\Util;
use App\Models\Stores\StoreAddress;
use DB;
use App\Models\Driver\UserDriver;

class DriverAssignFilter
{
    public function apply($query, $request)
    {

        $store_address =  StoreAddress::where('store_id', $request->store_id)->first();
        $locationFilter = "( ? * acos( cos( radians(?) ) * cos( radians( last_latitude ) ) * cos( radians( last_longitude ) - radians(?)) + sin( radians(?) ) * sin( radians( last_latitude ) ) )  )  AS distance";

        $radius = config('app_settings.drivers_receive_order_distance.value');
        $limit = config('app_settings.drivers_closest_value.value');

        $request->attempt = $request->attempt-1;
        $skip = $limit*$request->attempt;

        $acos = config('app_settings.radians_acos.value');

        $latitude = $store_address->latitude;
        $longitude = $store_address->longitude;

        // Location Filter with lat long

        $driver = UserDriver::orderBy('id','ASC');
        $driver->selectRaw('*,'.$locationFilter, [$acos,$latitude, $longitude, $latitude]);
        $query->join(DB::raw("({$driver->toSql()}) AS address"),
        function($join)
        {
            $join->on('address.id','user_drivers.id');
        })->mergeBindings($driver->getQuery());
        // close
        Util::RadiusFinder($query);
        $query->where('user_drivers.is_live',true);
        $query->where('user_drivers.account_status','approved');
        $query->orderby('distance');
        return $query->skip($skip)->take($limit);
    }
}
