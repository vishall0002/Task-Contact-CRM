<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::latest()->get();

        return view('custom-fields.index', compact('fields'));
    }

    public function updateStatus(Request $request)
    {
        CustomField::whereIn('id', $request->ids)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        CustomField::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token'); // 🔥 remove token

        $request->validate([
            'label' => 'required',
            'name' => 'required|unique:custom_fields',
            'type' => 'required',
            'placeholder' => 'nullable',
            'status' => 'active',
        ]);

        $field = CustomField::create($data);

        return response()->json([
            'message' => 'Custom field added successfully',
            'data' => $field,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:custom_fields,id',
            'label' => 'required',
            'name' => 'required',
            'type' => 'required',
        ]);

        $field = CustomField::findOrFail($request->id);

        $field->update([
            'label' => $request->label,
            'name' => $request->name,
            'type' => $request->type,
            'placeholder' => $request->placeholder,
        ]);

        return response()->json(['success' => true]);
    }
}
