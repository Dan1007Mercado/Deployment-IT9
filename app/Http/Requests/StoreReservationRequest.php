<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Guest;

class StoreReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                function ($attribute, $value, $fail) {
                    $existingGuest = Guest::where('email', $value)->first();
                    
                    if ($existingGuest) {
                        $existingName = strtolower(trim(
                            $existingGuest->first_name . ' ' . $existingGuest->last_name
                        ));
                        
                        $newName = strtolower(trim(
                            $this->input('first_name') . ' ' . $this->input('last_name')
                        ));
                        
                        if ($existingName !== $newName) {
                            $fail('This email is already registered to ' . 
                                  $existingGuest->first_name . ' ' . $existingGuest->last_name);
                        }
                    }
                }
            ],
            'contact_number' => 'required|string|max:20',
            // ... other rules ...
        ];
    }

    public function messages()
    {
        return [
            'email.custom' => 'This email is already registered to a different person.',
        ];
    }
}