<?php


namespace App\Controllers;



use App\Services\ImageManager;
use Aura\SqlQuery\QueryFactory;
use Respect\Validation\Validator;
use Respect\Validation\Validator as v;
use PDO;

class PhotosController extends Controller
{

    private $validator;
    private $imageManager;
    private $queryFactory;
    private $pdo;

    public function __construct(Validator $validator, ImageManager $imageManager, QueryFactory $queryFactory, PDO $pdo)
    {
        parent::__construct();
        $this->validator = $validator;
        $this->imageManager = $imageManager;
        $this->queryFactory = $queryFactory;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $this->checkLogged();

        $id = $this->auth->getUserId();
        $photos = $this->database->getWhere('photos', 'user_id', $id, 8);

        echo $this->view->render('photos/index', [
            'photos'   =>  $photos
       ]);
    }

    public function create()
    {
        $this->checkLogged();
        $categories = $this->database->all('categories', 'none');

        echo $this->view->render('photos/create', ['categories'  =>  $categories]);
    }


    public function store()
    {
        $this->checkLogged();
        $validator = v::key('title', v::stringType()->notEmpty())
            ->key('description', v::stringType()->notEmpty())
            ->key('category_id', v::intVal())
            ->keyNested('image.tmp_name', v::image());

        $this->validate($validator);
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

    public function show($id)
    {
        $this->checkLogged();
        $photo = $this->database->find('photos', $id);
        $user = $this->database->find('users', $photo['user_id']);
        $userImages = $this->database->whereAll('photos', 'user_id', $user['id'], 4);

        echo $this->view->render('photos/show', [
            'photo' =>  $photo,
            'user'  =>  $user,
            'userImages'    =>  $userImages
        ]);
    }

    public function edit($id)
    {
        $this->checkLogged();
        $photo = $this->database->find('photos', $id);
        if($photo['user_id'] != $this->auth->getUserId()) {
            flash()->error(['Можно редактировать только свои фотографии.']);
            return redirect('/photos');
        }

        $categories = $this->database->all('categories', 'none');
        echo $this->view->render('photos/edit', ['photo' =>  $photo, 'categories'    =>  $categories]);
    }

    public function update($id)
    {
        $validator = v::key('title', v::stringType()->notEmpty())
            ->key('description', v::stringType()->notEmpty())
            ->key('category_id', v::intVal())
            ->keyNested('image.tmp_name', v::optional(v::image()));

        $this->validate($validator);
        $photo = $this->database->find('photos', $id);

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
        $this->checkLogged();
        $photo = $this->database->find('photos', $id);
        if($photo['user_id'] != $this->auth->getUserId()) {
            flash()->error(['Можно редактировать только свои фотографии.']);
            return redirect('/photos');
        }
        $this->imageManager->deleteImage($photo['image']);
        $this->database->delete('photos', $id);

        return back();
    }

    public function download($id)
    {
        $photo = $this->database->find('photos',$id);
        echo $this->view->render('photos/download', [
            'photo' =>  $photo
        ]);
    }










    private function validate($validator)
    {
        try {
            $validator->assert(array_merge($_POST, $_FILES));

        } catch (ValidationException $exception) {
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