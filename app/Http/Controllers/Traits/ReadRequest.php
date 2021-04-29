<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait ReadRequest
{
    /**
     * Get Data from Request by request_key
     * 
     * @param Request $request
     * @param array $database_keys
     * @param array $request_keys
     * 
     * @return array
     */
    private function getData( Request $request, $database_keys = [], $request_keys = [] )
    {
        $ret = [];
        foreach ($database_keys as $index => $key) {
            $request_key = $request_keys[$index];
            $ret[$key] = $request->$request_key;
        }
        return $ret;
    }
}
