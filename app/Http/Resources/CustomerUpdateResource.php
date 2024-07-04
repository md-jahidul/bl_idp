<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'msisdn' => $this->msisdn,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'gender' => $this->gender,
            'alternate_phone' => $this->alternate_phone,
            'is_password_set' => $this->is_password_set,
            'profile_image' => $this->profile_image_base64// ? config('filesystems.profile_image_path').$this->profile_image : null,
        ];
    }

    /**
     * Add additional filed to the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 'Success',
            'message' => 'Customer Updated successfully!',
        ];
    }
}
