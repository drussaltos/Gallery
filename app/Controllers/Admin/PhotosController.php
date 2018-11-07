<?php

namespace App\Controllers\Admin;


use App\Services\ImageManager;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class PhotosController extends Controller
{
    private $validator;
    private $imageManager;

    public function __construct(Validator $validator, ImageManager $imageManager)
    {
        parent::__construct();
        $this->validator = $validator;
        $this->imageManager = $imageManager;
    }

    public function index()
    {
        $photos = $this->database->all('photos', 'none');

        echo $this->view->render('admin/photos/index', [ 'photos'   =>  $photos]);
    }

    public function edit($id)
    {
        $photo = $this->database->find('photos', $id);
        $categories = $this->database-> all('categories', 'none');

        echo $this->view->render('admin/photos/edit', [
            'photo'        =>  $photo,
            'categories'   =>  $categories
        ]);
    }

    public function create()
    {
        $categories = $this->database-> all('categories', 'none');

        echo $this->view->render('admin/photos/create',[
            'categories'   =>  $categories
        ]);
    }

    public function store()
    {
        $validator = v::key('title', v::stringType()->notEmpty())
            ->key('description', v::stringType()->notEmpty())
            ->key('category_id', v::intVal())
            ->keyNested('image.tmp_name', v::image());

        $this->validate($validator, array_merge($_POST, $_FILES), [
            'title' => 'Введите название',
            'description'   =>  'Введите описание',
            'category_id'   =>  'Выберите категорию',
            'image' =>  'Неверный формат картинки'
        ]);

        $image = $this->imageManager->uploadImage($_FILES['image']);
        $dimensions = $this->imageManager->getDimensions($image);
        $data = [
            "image" =>  $image,
            "title" =>  $_POST['title'],
            "description" =>  $_POST['description'],
            "category_id" =>  $_POST['category_id'],
            "user_id"   =>  $this->auth->getUserId(),
            "dimensions" =>  $dimensions,
            "date"  =>  time(),
        ];
        $this->database->create('photos', $data);

        flash()->success(['Спасибо! Картинка загружена']);

        return back();
    }

    public function update($id)
    {
        $validator = v::key('title', v::stringType()->notEmpty());
        $this->validate($validator, $_POST, [
            'title' => 'Введите название'
        ]);

        $photo = $this->database->find('photos',$id);

        $image = $this->imageManager->uploadImage($_FILES['image'], $photo['image']);
        $dimensions = $this->imageManager->getDimensions($image);

        $data = [
            "image" =>  $image,
            "title" =>  $_POST['title'],
            "description" =>  $_POST['description'],
            "category_id" =>  $_POST['category_id'],
            "user_id"   =>  $this->auth->getUserId(),
            "dimensions"    =>  $dimensions
        ];

        $this->database->update('photos', $id, $data);

        flash()->success(['Запись успешно обновлена']);

        return back();
    }

    public function delete($id)
    {
        $this->database->delete('photos', $id);
        return back();
    }








    private function validate($validator, $data, $message)
    {

        try {
            $validator->assert($data);

        } catch (ValidationException $exception) {
            flash()->error($exception->findMessages($message));

            return back();
        }
    }




}