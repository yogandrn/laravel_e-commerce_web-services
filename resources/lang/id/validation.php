<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'Tautan :attribute tidak valid',
    'after' => ':attribute harus setelah :date.',
    'after_or_equal' => ':attribute harus setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung (-) dan underscore (_).',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus sebelum :date.',
    'before_or_equal' => ':attribute harus sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => 'Nilai :attribute harus di rentang :min sampai :max.',
        'file' => 'Ukuran file :attribute harus di rentang :min sampai :max KB.',
        'string' => ':attribute hanya boleh :min sampai :max karakter.',
        'array' => 'Jumlah item :attribute harus di rentang :min sampai :max items.',
    ],
    'boolean' => ':attribute hanya boleh bernilai benar atau salah',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai',
    'current_password' => 'Password tidak sesuai!',
    'date' => ':attribute harus berupa tanggal yang valid',
    'date_equals' => ':attribute harus berupa tanggal yang bernilai :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => ':attribute harus terdiri dari :digits digit.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => ':attribute mempunyai nilai yang terduplikasi',
    'email' => 'Format email tidak valid.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attributeharus berupa file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute harus berupa file gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute harus berupa angka',
    'ip' => ':attribute harus berupa IP address.',
    'ipv4' => ':attribute harus berupa IPv4 address.',
    'ipv6' => ':attribute harus berupa IPv6 address.',
    'json' => ':attribute harus berupa JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => ':attribute harus berupa MAC address yang valid',
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'file' => 'Ukuran file :attribute tidak boleh lebih dari :max KB.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'array' => 'Jumlah item :attribute tidak boleh lebih dari :max.',
    ],
    'mimes' => 'Jenis file :attribute yang diperbolehkan hanya type: :values.',
    'mimetypes' => 'Jenis file :attribute hanya boleh type: :values.',
    'min' => [
        'numeric' => ':attribute minimal adalah :min.',
        'file' => 'Ukuran file :attribute minimal :min KB.',
        'string' => ':attribute harus terdiri dari minimal :min karakter.',
        'array' => 'Jumlah item :attribute minimal :min.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'Password tidak sesuai.',
    'present' => ':attribute harus berupa waktu sekarang',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute tidak boleh kosong.',
    // 'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => ':attribute harus bernilai :size.',
        'file' => 'Ukuran file :attribute harus :size KB.',
        'string' => ':attribute harus :size karakter.',
        'array' => 'Jumlah item :attribute harus :size.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => ':attribute harus berisi teks',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => ':attribute harus berisi URL yang valid',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
