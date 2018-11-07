<?php

namespace App\Controllers\Admin;


use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Respect\Validation\Validator;

class CategoriesController extends Controller
{
    private $validator;

    public function __construct(Validator $validator)
    {
        parent::__construct();
        $this->validator = $validator;
    }

    public function index()
    {
        $categories = $this->database->all('categories', 'none');

        echo $this->view->render('admin/categories/index',  compact('categories'));
    }

    public function create()
    {
        echo $this->view->render('admin/categories/create');
    }

    public function store()
    {
        $validator = v::key('title', v::stringType()->notEmpty());
        $this->validate($validator);

        $this->database->create('categories', $_POST);

        flash()->success(['Спасибо! Категория добавлена']);

        return back();
    }

    public function edit($id)
    {
        $category = $this->database->find('categories', $id);
        echo $this->view->render('admin/categories/edit', ['category'    =>  $category]);
    }

    public function update($id)
    {
        $validator = v::key('title', v::stringType()->notEmpty());
        $this->validate($validator);

        $this->database->update('categories', $id, $_POST);

        return redirect('/admin/categories');
    }

    public function delete($id)
    {
        $this->database->delete('categories', $id);
        return back();
    }

    private function validate($validator)
    {
        try {
            $validator->assert(array_merge($_POST, $_FILES));
        }

        catch(ValidationException $exception) {
            $exception->findMessages($this->getMessages());
            flash()->error($exception->getMessages());

            return back();
        }

    }
    private function getMessages()
    {
        return [
            'title' => 'Введите название',
            'description'   =>  'Введите описание',
            'category_id'   =>  'Выберите категорию',
            'image' =>  'Неверный формат картинки'
        ];
    }




}