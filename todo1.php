
<?php

if(!file_exists("tasks.json")){
    $filess = fopen("tasks.json",'w');
    fclose($filess);
}
    // mkdir("tasks/test.txt",0777,true);

function testInput($text){
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

function getTasks() {
    $jsonData = json_decode(file_get_contents('tasks.json'), true);
    if(empty($jsonData)){
        $jsonData = array();
    }
    return $jsonData;
}

function addTask($taskName) {
    $tasks = getTasks();
    $newTask = ['id' => uniqid(), 'name' => $taskName, 'completed' => false];
    $tasks[] = $newTask;
    saveTasks($tasks);
}

function deleteTask($taskId) {
    $tasks = getTasks();
    $tasks = array_filter($tasks, function ($task) use ($taskId) {
        return $task['id'] !== $taskId;
    });
    saveTasks($tasks);
}

function completeTask($taskId) {
    $tasks = getTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $taskId) {
            $task['completed'] = !$task['completed'];
        }
    }
    saveTasks($tasks);
}

function updateTask($taskId,$name) {
    $tasks = getTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $taskId) {
            $task['name'] = $name;
        }
    }
    saveTasks($tasks);
}

function saveTasks($tasks) {
    file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
}

$tasks = getTasks();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addTask'])) {
        $taskName = testInput($_POST['taskName']);
        $unique_id = testInput($_POST['unique_id']);
        if(empty($unique_id)){
            addTask($taskName);
            echo "<script>alert('Task Add Success'); location='todo1.php';</script>";
        }
        else{
            updateTask($unique_id,$taskName);
            echo "<script>alert('Task Update Success'); location='todo1.php';</script>";
        }

    } elseif (isset($_POST['deleteTask'])) {
        $taskId = testInput($_POST['taskId']);
        deleteTask($taskId);
        // echo "<script>alert('Task Delete Success');</script>";
    } elseif(isset($_POST['completeStatus'])){
        $uniqueId = testInput($_POST['uniqeId']);
        $status = testInput($_POST['status']);
        completeTask($uniqueId);
        // print_r($tasks);
        // die;

    }
    // header("Location: new_todo.php");
    // echo "<script>location='new_todo.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body {
    margin: 0;
    min-width: 250px;
    }

    /* Include the padding and border in an element's total width and height */
    * {
    box-sizing: border-box;
    }

    /* Remove margins and padding from the list */
    ul {
    margin: 0;
    padding: 0;
    }

    /* Style the list items */
    ul li {
    cursor: pointer;
    position: relative;
    padding: 12px 8px 12px 40px;
    list-style-type: none;
    background: #eee;
    font-size: 18px;
    transition: 0.2s;
    
    /* make the list items unselectable */
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    }

    /* Set all odd list items to a different color (zebra-stripes) */
    ul li:nth-child(odd) {
    background: #f9f9f9;
    }

    /* Darker background-color on hover */
    ul li:hover {
    background: #ddd;
    }

    /* When clicked on, add a background color and strike out text */
    ul li.checked {
    background: #888;
    color: #fff;
    text-decoration: line-through;
    }

    /* Add a "checked" mark when clicked on */
    ul li.checked::before {
    content: '';
    position: absolute;
    border-color: #fff;
    border-style: solid;
    border-width: 0 2px 2px 0;
    top: 10px;
    left: 16px;
    transform: rotate(45deg);
    height: 15px;
    width: 7px;
    }

    /* Style the close button */
    .close {
    position: absolute;
    right: 0;
    top: 0;
    padding: 12px 16px 12px 16px;
    }

    .close:hover {
    background-color: #f44336;
    color: white;
    }

    .editBtn {
    position: absolute;
    right: 46px;
    top: 0;
    padding: 12px 16px 12px 16px;
    }

    .editBtn:hover {
    background-color: green;
    color: white;
    }

    /* Style the header */
    .header {
    background-color: #38716a;
    padding: 30px 40px;
    color: white;
    text-align: center;
    }

    /* Clear floats after the header */
    .header:after {
    content: "";
    display: table;
    clear: both;
    }

    /* Style the input */
    input {
    margin: 0;
    border: none;
    border-radius: 0;
    width: 75%;
    padding: 10px;
    float: left;
    font-size: 16px;
    }

    /* Style the "Add" button */
    .addBtn {
    padding: 10px;
    width: 25%;
    background: #d9d9d9;
    color: #555;
    float: left;
    text-align: center;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
    border-radius: 0;
    outline:none;
    border:none;
    }

    .addBtn:hover {
    background-color: #bbb;
    }
    .cbox{
        width:100px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body>

<div id="myDIV" class="header">
  <h2 style="margin:5px">My To Do List</h2>
    <form action="todo1.php" method="post" onsubmit="return submitFun()">
        <input type="text" name="taskName" id="myInput" placeholder="Title..." />
        <input type="hidden" name="unique_id" value="0" id="unique_id" />
        <button type="submit" name="addTask" class="addBtn">Add</button>
    </form>
</div>

<ul id="myUL">
    <?php foreach ($tasks as $task){ ?>
        <li class="<?=($task['completed']) ? 'checked' : ''; ?>"> 
            <span class="text"><?php echo $task['name']; ?></span> 
            <input type="hidden" class="cbox todo_id" value="<?=$task['id']?>">
            <span class="editBtn" onclick="editlist(this,'<?=$task['id']?>')"><i class="fa fa-pencil"></i></span>
        <span class="close"><i class="fa fa-trash"></i></span> 
    </li>
    <?php } ?>
</ul>

<script>

// Click on a close button to hide the current list item
var close = document.getElementsByClassName("close");
var i;
for (i = 0; i < close.length; i++) {
  close[i].onclick = function() {
    var li = this.parentElement;
    var taskId = li.querySelector(".todo_id").value;
    // console.log(div);
    if(confirm("Are you sure you want to delete this task?"))
    $.ajax({
        type : 'POST',
        data : {
            deleteTask : '1',
            taskId,
        },
        success : function(respp){
            console.log(respp);
            li.style.display = "none";
        }
    });
  }
}

// Add a "checked" symbol when clicking on a list item
var list = document.querySelector('ul');
list.onclick = function(ev) {
  if (ev.target.tagName === 'LI') {
    var uniqeId = ev.target.querySelector(".todo_id").value;
    var status = ev.target.classList.contains("checked");
    console.log(status);
    $.ajax({
        type : 'POST',
        data : {
            completeStatus : 1,
            uniqeId
        },
        success : function(respp){
            // console.log("sudhir",respp);
            ev.target.classList.toggle('checked');
        }
    });
  }
}

// Create a new list item when clicking on the "Add" button
function submitFun(){
    var myInput = $("#myInput").val().trim();
    if(myInput == ""){
        alert("Please Enter your task");
        $("#myInput").focus();
        return false;
    }
}

function editlist(thiss,unique_id){
    // alert("jhruew");
    var text = thiss.parentElement.querySelector(".text").innerHTML;
    document.getElementById("myInput").value = text;
    document.getElementsByClassName("addBtn")[0].innerHTML = "Update";
    document.getElementById("unique_id").value=unique_id;

}
</script>

</body>
</html>
