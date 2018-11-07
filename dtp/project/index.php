<?php

class HomeController
{
	private $database;
	private $view;

	public function __construct(Database $database, View $view)
	{

		$this->database = $database;
		$this->view = $view;					
	}

	public function index()
	{
		// $database = new Database;
		$tasks = $this->database->all();
		$this->view->render('index', ['myTasks'	=>	$tasks]);
	}
		
	public function show($id)
	{
		$this->database->find($id);
	}
}