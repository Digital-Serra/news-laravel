<?php

namespace DigitalSerra\NewsLaravel\Http\Requests;

use App\Http\Requests\Request;

class NewsFormRequest extends Request
{
    protected $redirect;

    public function __construct()
    {
        $this->redirect = route('news.add');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title'=>'required|unique:news|min:3',
            'body'=>'required|min:5',
            'published'=>'required'
        ];

        if($this->file('images') != null){
            foreach($this->file('images') as $key => $val)
            {
                $rules['images.'.$key] = 'image';
            }
        }
        return $rules;
    }

    /**
         * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title'=>'título',
            'body'=>'descrição',
            'published'=>'publicar',
            'images[]'=>'imagens',
            'images'=>'imagem'
        ];
    }
}
