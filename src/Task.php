<?php
    class Task
    {
        private $description;
        private $category_id;
        private $id;

        function __construct($description, $id = null, $category_id)
        {
            $this->description = $description;
            $this->id = $id;
            $this->category_id = $category_id;
        }

        function setDescription($new_description)
        {
            $this->description = (string) $new_description;
        }

        function getDescription()
        {
            return $this->description;
        }

        function getId()
        {
            return $this->id;
        }

        function setId($new_id)
        {
            $this->id = (int) $new_id;
        }

        function setCategoryId($new_category_id)
        {
            $this->category_id = (int) $new_category_id;
        }

        function getCategoryId()
        {
            return $this->category_id;
        }

        function save()
        {
            //this line calls query on the global variable DB and stores it in "statement" and puts task information into the tasks table in to_do database
            //??where is the first place that DB is referenced/instantiated??
            //??what does query mean exactly...put this string in sql and run
            //?? how is $GLOBALS['DB'] related to "$DB" in app.php, TaskTest.php ??
            $statement = $GLOBALS['DB']->query("INSERT INTO tasks (description, category_id) VALUES ('{$this->getDescription()}', {$this->getCategoryId()}) RETURNING id;");
            //puts the info from the table stored in $statement into an associative array called $result
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            //takes the value for the 'id' key in $result and sets that as the id for whatever object save is called on
            $this->setId($result['id']);
        }

        static function getAll()
        {
            //$returned_tasks holds an array of associative arrays, containing all info from the tasks table
            $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
            $tasks = array();
            //step thru each associative array in the $returned_tasks in order to build a new object
            foreach($returned_tasks as $task) {
                //holds the value of the description key
                $description = $task['description'];
                //hold the value of the id key
                $id = $task['id'];
                //holds the value of the category_id key
                $category_id = $task['category_id'];
                //create a new Task object using the above info
                $new_task = new Task($description, $id, $category_id);
                //push that object into a new array
                array_push($tasks, $new_task);
            }
            //send back that final array of Task objects
            return $tasks;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM tasks *;");
        }

        static function find($search_id)
        {
            $found_task = null;
            $tasks = Task::getAll();
            foreach($tasks as $task) {
                $task_id = $task->getID();
                if ($task_id == $search_id) {
                    $found_task = $task;
                }
            }
            return $found_task;
        }
    }
?>
