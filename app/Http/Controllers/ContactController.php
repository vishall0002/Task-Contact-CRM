<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCustomFieldValue;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * List all contacts
     */
    // public function index()
    // {
    //     $contacts = Contact::latest()->get();

    //     $customFields = CustomField::where('status', 'active')->get();

    //     return view('contacts.index', compact('contacts', 'customFields'));
    // }

    public function index(Request $request)
    {
        $contacts = Contact::with([
                'customFields.customField',
                'mergedContacts.secondaryContact.customFields.customField'
            ])
            ->when($request->filled('name'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->filled('email'), function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            })
            ->when($request->filled('phone'), function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            })

            //  ->when($request->filled('gender'), function ($q) use ($request) {
            //     $q->where('gender', 'like', '%' . $request->gender . '%');
            // })
               ->when($request->filled('gender'), function ($q) use ($request) {
                $q->where('gender', 'like', '%' . $request->gender . '%');
            })
            ->latest()
            ->get();

        $customFields = CustomField::where('status', 'active')->get();

        // echo "<pre>";
        // print_r($contacts->toArray());die;
        return view('contacts.index', compact('contacts', 'customFields'));
    }


    // public function getCustomFields($id)
    // {
    //     return ContactCustomFieldValue::where('contact_id', $id)->get();
    // }

       

    public function getCustomFields($id)
    {
        return ContactCustomFieldValue::with('customField:id,name,type')
            ->where('contact_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'custom_field_id' => $item->custom_field_id,
                    'field_name'      => $item->customField->name ?? null,
                    'field_type'      => $item->customField->type ?? null,
                    'field_value'     => $item->field_value,
                ];
            });
    }


    /**
     * Store new contact
     */
    public function store(Request $request)
    {
        // print_r($request);die;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();

        try {

            // Handle profile image
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('contacts/profile', 'public');
            }

            // Handle additional file
            $additionalFilePath = null;
            if ($request->hasFile('additional_file')) {
                $additionalFilePath = $request->file('additional_file')->store('contacts/files', 'public');
            }

            // Create contact
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'profile_image' => $profileImagePath,
                'additional_file' => $additionalFilePath,
                'created_by' => auth()->id(),
            ]);

            // Save custom fields
            if ($request->has('custom_fields')) {
                foreach ($request->custom_fields as $fieldId => $value) {

                    // Check if this is a file input
                    if ($request->hasFile("custom_fields.$fieldId")) {
                        $value = $request->file("custom_fields.$fieldId")->store('contacts/custom_fields', 'public');
                    }

                    ContactCustomFieldValue::create([
                        'contact_id' => $contact->id,
                        'custom_field_id' => $fieldId,
                        'field_value' => $value,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => $contact,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single contact (for edit modal)
     */
    public function show($id)
    {
        $contact = Contact::with('customFields')->findOrFail($id);

        return response()->json($contact);
    }

    /**
     * Update contact
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contacts,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
        ]);

        DB::beginTransaction();

        try {
            $contact = Contact::findOrFail($request->id);

            /**
             * IMPORTANT:
             * Update = REPLACE primary value
             * Merge = APPEND values
             */
            $contact->update([
                'name' => $request->name,
                'email' => $request->email,   // ✅ replace
                'phone' => $request->phone,   // ✅ replace
                'gender' => $request->gender,
                'profile_image' => $request->profile_image ?? $contact->profile_image,
                'additional_file' => $request->additional_file ?? $contact->additional_file,
                'updated_by' => auth()->id(),
            ]);

            /**
             * Custom fields:
             * Edit form decides final value
             */
            if ($request->has('custom_fields')) {
                foreach ($request->custom_fields as $fieldId => $value) {
                    ContactCustomFieldValue::updateOrCreate(
                        [
                            'contact_id' => $contact->id,
                            'custom_field_id' => $fieldId,
                        ],
                        [
                            'field_value' => $value,
                            'updated_by' => auth()->id(),
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete contact(s)
     */
    public function delete(Request $request)
    {
        Contact::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Contact(s) deleted successfully',
        ]);
    }
}
