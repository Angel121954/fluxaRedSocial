<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'accepted_if'          => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo :attribute debe ser una fecha igual o posterior a :date.',
    'alpha'                => 'El campo :attribute solo debe contener letras.',
    'alpha_dash'           => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num'            => 'El campo :attribute solo debe contener letras y números.',
    'array'                => 'El campo :attribute debe ser un arreglo.',
    'ascii'                => 'El campo :attribute solo debe contener caracteres alfanuméricos de un solo byte y símbolos.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo :attribute debe ser una fecha igual o anterior a :date.',
    'between'              => [
        'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file'    => 'El campo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string'  => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación del campo :attribute no coincide.',
    'current_password'     => 'La contraseña es incorrecta.',
    'date'                 => 'El campo :attribute debe ser una fecha válida.',
    'date_equals'          => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format'          => 'El campo :attribute no coincide con el formato :format.',
    'decimal'              => 'El campo :attribute debe tener :decimal decimales.',
    'declined'             => 'El campo :attribute debe ser rechazado.',
    'declined_if'          => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different'            => 'El campo :attribute y :other deben ser diferentes.',
    'digits'               => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between'       => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'El campo :attribute tiene dimensiones de imagen no válidas.',
    'distinct'             => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with'      => 'El campo :attribute no puede terminar con uno de los siguientes valores: :values.',
    'doesnt_start_with'    => 'El campo :attribute no puede comenzar con uno de los siguientes valores: :values.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with'            => 'El campo :attribute debe terminar con uno de los siguientes valores: :values.',
    'enum'                 => 'El valor seleccionado para :attribute no es válido.',
    'exists'               => 'El valor seleccionado para :attribute no es válido.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute debe tener un valor.',
    'gt'                   => [
        'array'   => 'El campo :attribute debe tener más de :value elementos.',
        'file'    => 'El campo :attribute debe pesar más de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string'  => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte'                  => [
        'array'   => 'El campo :attribute debe tener :value elementos o más.',
        'file'    => 'El campo :attribute debe pesar :value kilobytes o más.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string'  => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'image'                => 'El campo :attribute debe ser una imagen.',
    'in'                   => 'El valor seleccionado para :attribute no es válido.',
    'in_array'             => 'El campo :attribute no existe en :other.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'ip'                   => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4'                 => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El campo :attribute debe ser una cadena JSON válida.',
    'lowercase'            => 'El campo :attribute debe estar en minúsculas.',
    'lt'                   => [
        'array'   => 'El campo :attribute debe tener menos de :value elementos.',
        'file'    => 'El campo :attribute debe pesar menos de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string'  => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte'                  => [
        'array'   => 'El campo :attribute no debe tener más de :value elementos.',
        'file'    => 'El campo :attribute debe pesar :value kilobytes o menos.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string'  => 'El campo :attribute debe tener :value caracteres o menos.',
    ],
    'mac_address'          => 'El campo :attribute debe ser una dirección MAC válida.',
    'max'                  => [
        'array'   => 'El campo :attribute no debe tener más de :max elementos.',
        'file'    => 'El campo :attribute no debe pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string'  => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'max_digits'           => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes'                => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
        'file'    => 'El campo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits'           => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing'              => 'El campo :attribute debe estar ausente.',
    'missing_if'           => 'El campo :attribute debe estar ausente cuando :other es :value.',
    'missing_unless'       => 'El campo :attribute debe estar ausente a menos que :other sea :value.',
    'missing_with'         => 'El campo :attribute debe estar ausente cuando :values está presente.',
    'missing_with_all'     => 'El campo :attribute debe estar ausente cuando :values están presentes.',
    'multiple_of'          => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in'               => 'El valor seleccionado para :attribute no es válido.',
    'not_regex'            => 'El formato del campo :attribute no es válido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'password'             => [
        'letters'       => 'El campo :attribute debe contener al menos una letra.',
        'mixed'         => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers'       => 'El campo :attribute debe contener al menos un número.',
        'symbols'       => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El :attribute ingresado ha aparecido en una filtración de datos. Por favor elige uno diferente.',
    ],
    'present'              => 'El campo :attribute debe estar presente.',
    'prohibited'           => 'El campo :attribute está prohibido.',
    'prohibited_if'        => 'El campo :attribute está prohibido cuando :other es :value.',
    'prohibited_unless'    => 'El campo :attribute está prohibido a menos que :other esté en :values.',
    'prohibits'            => 'El campo :attribute prohíbe que :other esté presente.',
    'regex'                => 'El formato del campo :attribute no es válido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_array_keys'  => 'El campo :attribute debe contener entradas para: :values.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values está presente.',
    'same'                 => 'El campo :attribute y :other deben coincidir.',
    'size'                 => [
        'array'   => 'El campo :attribute debe contener :size elementos.',
        'file'    => 'El campo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string'  => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with'          => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values.',
    'string'               => 'El campo :attribute debe ser una cadena de texto.',
    'timezone'             => 'El campo :attribute debe ser una zona horaria válida.',
    'unique'               => 'El valor del campo :attribute ya está en uso.',
    'uploaded'             => 'El campo :attribute no se pudo cargar.',
    'uppercase'            => 'El campo :attribute debe estar en mayúsculas.',
    'url'                  => 'El campo :attribute debe ser una URL válida.',
    'ulid'                 => 'El campo :attribute debe ser un ULID válido.',
    'uuid'                 => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Mensajes personalizados por atributo y regla.
    | Convención: 'attribute.rule' => 'mensaje'
    |
    */

    'custom' => [
        'ended_at' => [
            'after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'required_if'    => 'La fecha de fin es obligatoria cuando el trabajo no es actual.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Nombres legibles para los atributos en los mensajes de error.
    |
    */

    'attributes' => [
        'name'            => 'nombre',
        'email'           => 'correo electrónico',
        'password'        => 'contraseña',
        'username'        => 'nombre de usuario',
        'company'         => 'empresa',
        'position'        => 'cargo',
        'location'        => 'ubicación',
        'started_at'      => 'fecha de inicio',
        'ended_at'        => 'fecha de fin',
        'current'         => 'trabajo actual',
        'description'     => 'descripción',
        'title'           => 'título',
        'phone'           => 'teléfono',
        'address'         => 'dirección',
        'city'            => 'ciudad',
        'country'         => 'país',
        'avatar'          => 'foto de perfil',
        'image'           => 'imagen',
        'file'            => 'archivo',
        'size'            => 'tamaño',
        'type'            => 'tipo',
        'date'            => 'fecha',
        'url'             => 'enlace',
        'message'         => 'mensaje',
        'subject'         => 'asunto',
        'content'         => 'contenido',
        'body'            => 'cuerpo',
        'first_name'      => 'nombre',
        'last_name'       => 'apellido',
        'birth_date'      => 'fecha de nacimiento',
        'gender'          => 'género',
        'bio'             => 'biografía',
        'website'         => 'sitio web',
        'linkedin'        => 'LinkedIn',
        'github'          => 'GitHub',
        'twitter'         => 'Twitter',
    ],

];
