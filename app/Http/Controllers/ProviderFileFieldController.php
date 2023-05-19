<?php

namespace App\Http\Controllers;

use App\Models\ProviderFileField;
use Illuminate\Http\Request;

class ProviderFileFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $providerFileField=ProviderFileField::where('provider_id', (int)$id)->first();
        if ($providerFileField != null) {
            $providerMapField = $providerFileField->field_maps;

            $fieldArray = [];
            $data=(json_decode($providerMapField));
            foreach ($data as $key => $value) {
                $data[$key] = (array)$value;
                $fieldArray[$data[$key]['applicationField']] = $data[$key]['reportField'];
            }
            $providerFileField=collect($fieldArray);

            return response()->json($providerFileField);
        } else {
            return response()->json(null);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProviderFileFieldRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $fieldMaps = [];
        foreach (json_decode($request->fieldMaps) as $value) {
            if (!empty($value->applicationField) && !empty($value->reportField)) {
                $fieldMaps[] = $value;
            }
        }

        $providerFileField = ProviderFileField::updateOrCreate(
            ['provider_id' =>  request('provider_id')],
            ['field_maps' => json_encode($fieldMaps)]
        );

        return response()->json($providerFileField);
    }
}
