<?php

namespace FluentForm\App\Models;

use FluentForm\Framework\Support\Arr;

class FormMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fluentform_form_meta';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * A formMeta is owned by a form.
     *
     * @return \FluentForm\Framework\Database\Orm\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public static function prepare($attributes, $predefinedForm)
    {
        $formMeta = [];

        $formMeta[] = [
            'meta_key' => 'formSettings',
            'value'    => json_encode(Form::getFormsDefaultSettings()),
        ];

        $formMeta[] = [
            'meta_key' => 'template_name',
            'value'    => Arr::get($attributes, 'predefined'),
        ];

        if (isset($predefinedForm['notifications'])) {
            $formMeta[] = [
                'meta_key' => 'notifications',
                'value'    => json_encode($predefinedForm['notifications']),
            ];
        }

        return $formMeta;
    }

    public static function store(Form $form, $formMeta)
    {
        foreach ($formMeta as $meta) {
            $meta['value'] = trim(preg_replace('/\s+/', ' ', $meta['value']));

            $form->formMeta()->create([
                'meta_key' => $meta['meta_key'],
                'value'    => $meta['value'],
            ]);
        }
    }
}
