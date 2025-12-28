<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactMerge;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ContactCustomFieldValue;

class ContactMergeController extends Controller
{




public function preview(Request $request)
{
    $request->validate([
        'master_id'   => 'required|exists:contacts,id',
        'contact_ids' => 'required|array|min:2|max:2'
    ]);

    // Master
    $master = Contact::with('customFields.customField')
        ->findOrFail($request->master_id);

    // Secondary (exclude master)
    $secondaryId = collect($request->contact_ids)
        ->reject(fn ($id) => $id == $request->master_id)
        ->first();

    $secondary = Contact::with('customFields.customField')
        ->findOrFail($secondaryId);

    /**
     * Key by custom_field_id (NOT field_key)
     */
    $masterFields = $master->customFields->keyBy('custom_field_id');
    $secondaryFields = $secondary->customFields->keyBy('custom_field_id');

    // Union of all custom field IDs
    $allFieldIds = $masterFields->keys()
        ->merge($secondaryFields->keys())
        ->unique();

    // Build comparison array
    $customFields = $allFieldIds->map(function ($fieldId) use ($masterFields, $secondaryFields) {

        $masterField = $masterFields->get($fieldId);
        $secondaryField = $secondaryFields->get($fieldId);

        return [
            'id'         => $fieldId,
            'label'      => $masterField?->customField?->label
                            ?? $secondaryField?->customField?->label
                            ?? 'Unknown',
            'master'     => $masterField?->field_value,
            'secondary'  => $secondaryField?->field_value,
        ];
    });

    return view(
        'contacts.merge-preview',
        compact('master', 'secondary', 'customFields')
    );
}



public function confirm(Request $request)
{
    DB::beginTransaction();

    try {

        // 1️⃣ Validation
        $validator = \Validator::make($request->all(), [
            'master_id'    => 'required|different:secondary_id|exists:contacts,id',
            'secondary_id' => 'required|exists:contacts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $master = Contact::with('customFields')->find($request->master_id);
        $secondary = Contact::with('customFields')->find($request->secondary_id);

        if (!$master || !$secondary) {
            return response()->json([
                'status'  => false,
                'message' => 'Contact not found'
            ], 404);
        }

        // Prevent double merge
        if ($secondary->merged_into) {
            return response()->json([
                'status'  => false,
                'message' => 'This contact is already merged.'
            ], 409);
        }

        /**
         * 2️⃣ STORE SECONDARY CONTACT SNAPSHOT
         * ✅ THIS WAS MISSING (main bug)
         */
        ContactMerge::create([
            'master_contact_id'    => $master->id,
            'secondary_contact_id' => $secondary->id,
            'name'                 => $secondary->name,
            'email'                => $secondary->email,
            'phone'                => $secondary->phone,
            'gender'               => $secondary->gender,
            'profile_image'        => $secondary->profile_image,
            'additional_file'      => $secondary->additional_file,
            'merged_by'            => auth()->id(),
        ]);

        /**
         * 3️⃣ FILL ONLY EMPTY MASTER FIELDS
         * ❌ NO EMAIL / PHONE MERGE
         */
        foreach (['name', 'gender'] as $field) {
            if (empty($master->$field) && !empty($secondary->$field)) {
                $master->$field = $secondary->$field;
            }
        }

        $master->save();

        /**
         * 4️⃣ CUSTOM FIELDS
         */
        $useOnlyMaster = $request->boolean('merge_custom_fields');

        // If checkbox is NOT checked → merge custom fields
        if (!$useOnlyMaster) {

            foreach ($secondary->customFields as $secondaryField) {

                $masterField = ContactCustomFieldValue::where('contact_id', $master->id)
                    ->where('custom_field_id', $secondaryField->custom_field_id)
                    ->first();

                // Master missing → copy
                if (!$masterField) {
                    ContactCustomFieldValue::create([
                        'contact_id'      => $master->id,
                        'custom_field_id' => $secondaryField->custom_field_id,
                        'field_value'     => $secondaryField->field_value,
                    ]);
                    continue;
                }

                // Both exist → append values
                if (
                    !empty($secondaryField->field_value) &&
                    $secondaryField->field_value !== $masterField->field_value
                ) {
                    $values = array_unique(array_map(
                        'trim',
                        explode(' | ', $masterField->field_value)
                    ));

                    if (!in_array($secondaryField->field_value, $values)) {
                        $values[] = $secondaryField->field_value;
                    }

                    $masterField->update([
                        'field_value' => implode(' | ', $values),
                    ]);
                }
            }
        }

        /**
         * 5️⃣ MARK SECONDARY AS MERGED
         */
        // $secondary->update([
        //     'merged_into' => $master->id,
        // ]);

        $secondary->update([
            'merged_into' => $master->id,
            'is_merged'   => 1,
        ]);

        $master->update([
            'is_merged' => 1,
        ]);

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Contacts merged successfully'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        // return response()->json([
        //     'status'  => false,
        //     'message' => 'Something went wrong while merging contacts.',
        //     'debug'   => config('app.debug') ? $e->getMessage() : null
        // ], 500);
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ], 500);

    }
}







}