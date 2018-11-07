<?php


namespace App\Controllers;




use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;
use PDO;


class HomeController extends Controller
{



    private $queryFactory;
    private $pdo;


    public function __construct(QueryFactory $queryFactory, PDO $pdo)
    {
        parent::__construct();

        $this->queryFactory = $queryFactory;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 20;

        $photos = $this->database->getPaginatedAll('photos', $page, $perPage);
        $paginator = paginate(
            $this->database->getAll('photos'),
            $page,
            $perPage,
            "/?page=(:num)"
        );

        echo $this->view->render('home',[
            'photos'   =>  $photos,
            'paginator'    =>  $paginator
        ]);

//        $photos = $this->database->all("photos", 50);
//
//        echo $this->view->render('home', compact('photos'));
    }

    public function photo($id)
    {
        $photo = $this->database->find("photos", $id);
        $user = $this->database->find('users', $photo['user_id']);
        $userImages = $this->database->whereAll('photos', 'user_id', $user['id'], 4);



        echo $this->view->render('photo', [
            'photo'   =>  $photo,
            'user'  =>  $user,
            'userImages' => $userImages
        ]);
    }

    public function user($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 8;
        $photos = $this->database->getPaginatedFrom('photos', 'user_id', $id, $page, $perPage);
        $paginator = paginate(
            $this->database->getCount('photos', 'user_id',$id),
            $page,
            $perPage,
            "/category/$id?page=(:num)"
        );
        $user = $this->database->find('users', $id);

        echo $this->view->render('category',[
            'photos'   =>  $photos,
            'paginator'    =>  $paginator,
            'user'  =>  $user
        ]);
//        $user = $this->database->find('users', $id);
//        $photos = $this->database->whereAll('photos', 'user_id', $user['id'], 4);
//
//
//        echo $this->view->render('user', [
//            'photos'   =>  $photos,
//            'user'  =>  $user
//        ]);
    }

    public function category($id)
  {
      $page = isset($_GET['page']) ? $_GET['page'] : 1;
      $perPage = 8;
      $photos = $this->database->getPaginatedFrom('photos', 'category_id', $id, $page, $perPage);
      $paginator = paginate(
          $this->database->getCount('photos', 'category_id',$id),
          $page,
          $perPage,
          "/category/$id?page=(:num)"
      );
      $category = $this->database->find('categories', $id);

      echo $this->view->render('category',[
          'photos'   =>  $photos,
          'paginator'    =>  $paginator,
          'category'  =>  $category
      ]);
//        $photos = $this->database->getWhere("photos", "category_id", $id, 'none');
//
//        echo $this->view->render('category', compact('photos'));

    }
}
