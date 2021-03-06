<?php

include_once(__DIR__ . "/php/includes/bootstrap.include.php");
require_once(__DIR__ . "/classes/Db.php");
require_once(__DIR__ . "/classes/User.php");
require_once(__DIR__ . "/classes/Lists.php");

$user = new classes\User($_SESSION['user']);
$list = new classes\Lists();
$taskClass = new classes\Task();

$lists = $list->getLists($user);
$users = $user->getUsers($user);


if(!empty($_POST['create_task'])){
    $user = new classes\User($_SESSION['user']);

    session_status();
    $_SESSION['list_id'] = $_POST['list_id'];
    header('Location: php/tasks/create_task.php');
}

if(!empty($_POST['delete_task'])){
    $user = new classes\User($_SESSION['user']);
    $taskClass = new classes\Task();
    $taskClass->deleteTask($user, $_POST['task_id']);
}

if(!empty($_POST['delete_list'])){
    $user = new classes\User($_SESSION['user']);
    $list = new classes\Lists();
    $list->deleteList($user, $_POST['list_id']);
}

if(!empty($_POST['delete_upload'])){
    $user = new classes\User($_SESSION['user']);
    $taskClass = new classes\Task();
    $taskClass->deleteUpload($_POST['task_id']);
}

if(!empty($_POST['done_task'])){
    $user = new classes\User($_SESSION['user']);
    $taskClass = new classes\Task();
    $taskClass->doneTask($user, $_POST['task_id']);
}

if(!empty($_POST['todo_task'])){
    $user = new classes\User($_SESSION['user']);
    $taskClass = new classes\Task();
    $taskClass->toDoTask($user, $_POST['task_id']);
}

if(!empty($_GET['order_task'])){
    $user = new classes\User($_SESSION['user']);
    $taskClass = new classes\Task();
    $taskClass->orderTasks($user, $_POST['task_id']);
}

if (!empty($_POST['uploadFile'])) {
    $task_id = $_POST['task_id'];

    try {
        
      $taskClass->saveUpload($task_id);
    } catch (\Throwable $th) {
      $error = $th->getMessage();
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="./img/favicon.png" type="image/png">

    <title>Checkmate | Home</title>
</head>

<body>

<header>
<h2>Home </h2>

<a class="logout" href="./php/auth/logout.php">LOG OUT</a>

<a class="headerbutton" href="./php/lists/create_list.php">MAKE LIST</a>





    <?php if($user->getIsAdmin() == 1){ ?>

        <a class="headerbutton" href="./statistics.php">STATISTICS</a>

        <a class="headerbutton" href="./php/admins/create_admin.php">MAKE ADMIN</a>


    <?php } ?>


</header>

<?php
        if(count($lists) <= 0){ ?>


            <img class="placeholder" src="./img/placeholder.png" alt="placeholder">


        <?php } else { ?>

<ul class="row col-md-12">

<?php

foreach ($lists as $list) :?>

    <div id="list-decoration-search" class="col-md-4">
            <div class="container">
                <div class="card h-100 breed">
                        <div class="card-body">
                            <h5 class="card-title"><strong><?= htmlspecialchars($list->title); ?></strong></h5>
                            <p class="card-text"><?= htmlspecialchars($list->description); ?></p>

                            <p class="card-text"><strong>Deadline:  </strong><?= htmlspecialchars($list->deadline);?></p>
                            
                            <p class="status"><strong>TO DO </strong></p>


                    <?php $tasks = $taskClass->getTasks($user, $list->id);

                    foreach ($tasks as $task) :
                    ?>

                        <?php if($task->status == 'to do'){ ?>

                            <div class="task">
                            <h5 class="card-title"><strong><?= htmlspecialchars($task->title); ?></strong></h5>
                            <p class="card-text"><strong>Geplande uren:  </strong><?= htmlspecialchars($task->hours); ?></p>
                            <p class="card-text"><strong>Tegen:  </strong><?= htmlspecialchars($task->deadline);?></p>

                            <p class="timer">
                            <?php
                            $datetime1 = new DateTime();
                            $datetime2 = new DateTime($task->deadline);
                            $interval = $datetime1->diff($datetime2);
                            echo $interval->format('<strong> Deadline binnen </strong> %d dag(en) %h uur');

                            ?>
                            </p>

                            <p class="card-text"><strong>File:  </strong><?= htmlspecialchars($task->upload);?></p>


                            <form action="" method="post">
                                <input type="hidden" name="task_id"
                                        value="<?= htmlspecialchars($task->id);?>" />
                                <input id="uploaddelete" type="submit" name="delete_upload"
                                        value="Verwijder File" />
                            </form>

                            <form enctype="multipart/form-data" action="" method="POST">
                                <input type="hidden" name="task_id" value="<?= $task->id;?>" />
                                <input class="upload" type="file" id="upload" name="upload" capture="camera" required />
                                <input class="uploadbutton" type="submit" value="Upload" name="uploadFile" />
                            </form>

                            <form action="" method="post">
                                    <input type="hidden" name="task_id"
                                        value="<?= htmlspecialchars($task->id);?>" placeholder="naam" />
                                    <input id="task" type="submit" name="delete_task"
                                        value="Verwijder Taak" />
                            </form>

                            <form action="" method="post">
                                    <input type="hidden" name="task_id"
                                        value="<?= htmlspecialchars($task->id);?>" placeholder="naam" />
                                    <input id="doneTask" type="submit" name="done_task"
                                        value="Done" />
                            </form>

                            </div>
                        <?php } ?>

                    <?php endforeach ?>


                    <p class="status"><strong>DONE </strong></p>

                    <?php $tasks = $taskClass->getTasks($user, $list->id);

                        foreach ($tasks as $task) :
                        ?>

                            <?php if($task->status == 'done'){ ?>

                                <div class="task">
                                <h5 class="card-title"><strong><?= htmlspecialchars($task->title); ?></strong></h5>
                                <p class="card-text"><strong>Geplande uren:  </strong><?= htmlspecialchars($task->hours); ?></p>

                                <p class="card-text"><strong>Tegen:  </strong><?= htmlspecialchars($task->deadline);?></p>
                                
                                <p class="timer">
                                <?php 
                                $datetime1 = new DateTime();
                                $datetime2 = new DateTime($task->deadline);
                                $interval = $datetime1->diff($datetime2);
                                echo $interval->format('<strong> Deadline binnen </strong> %d dag(en) %h uur');
                                ?>
                                </p>

                                <p class="card-text"><strong>File:  </strong><?= htmlspecialchars($task->upload);?></p>



                                <form action="" method="post">
                                        <input type="hidden" name="task_id"
                                            value="<?= htmlspecialchars($task->id);?>" />
                                        <input id="uploaddelete" type="submit" name="delete_upload"
                                            value="Verwijder File" />
                                </form>

                                <form enctype="multipart/form-data" action="" method="POST">
                                <input type="hidden" name="task_id" value="<?= $task->id;?>" />
                                <input class="upload" type="file" id="upload" name="upload" capture="camera" required />
                                <input class="uploadbutton" type="submit" value="Upload" name="uploadFile" />
                                </form>

                                <form action="" method="post">
                                        <input type="hidden" name="task_id"
                                            value="<?= htmlspecialchars($task->id);?>" placeholder="naam" />
                                        <input id="task" type="submit" name="delete_task"
                                            value="Verwijder Taak" />
                                </form>

                                <form action="" method="post">
                                        <input type="hidden" name="task_id"
                                            value="<?= htmlspecialchars($task->id);?>" placeholder="naam" />
                                        <input id="toDoTask" type="submit" name="todo_task"
                                            value="To Do" />
                                </form>

                                </div>
                            <?php } ?>

                        <?php endforeach ?>


                        
                    <form action="" method="post">
                        <div>
                            <input type="hidden" name="list_id"
                                   value="<?= htmlspecialchars($list->id);?>" placeholder="naam" />
                            <input id="task" type="submit" name="create_task"
                                   value="ADD TASK" />
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="list_id"
                                   value="<?= htmlspecialchars($list->id);?>" placeholder="naam" />
                            <input id="list" type="submit" name="delete_list"
                                   value="Verwijder Lijst" />
                    </form>
                </form>
                </div>
            </div>
            </div>
    </div>


<?php endforeach ?>
</ul>
<?php } ?>


<script src="/js/jquery.min.js"></script>


</body>

</html>
