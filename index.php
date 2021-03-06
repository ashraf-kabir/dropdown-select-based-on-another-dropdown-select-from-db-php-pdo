<?php
include 'config.php';

$sql1 = "SELECT id,dept_name FROM departments";
$query1 = $dbh->prepare($sql1);
$query1->execute();
$results1 = $query1->fetchAll(PDO::FETCH_OBJ);

if ($query1->rowCount() > 0) {
  foreach ($results1 as $result1) {
    $categories[] = array("id" => $result1->id, "val" => $result1->dept_name);
  }
}

$sql2 = "SELECT id, deptid, catname FROM task_categories";
$query2 = $dbh->prepare($sql2);
$query2->execute();
$results2 = $query2->fetchAll(PDO::FETCH_OBJ);

if ($query2->rowCount() > 0) {
  foreach ($results2 as $result2) {
    $subcats[$result2->deptid][] = array("id" => $result2->id, "val" => $result2->catname);
  }
}

$jsonCats = json_encode($categories);
$jsonSubCats = json_encode($subcats);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type='text/javascript'>
      <?php
        echo "var categories = $jsonCats; \n";
        echo "var subcats = $jsonSubCats; \n";
      ?>
      function loadCategories(){
        var select = document.getElementById("categoriesSelect");
        select.onchange = updateSubCats;
        for(var i = 0; i < categories.length; i++){
          select.options[i] = new Option(categories[i].val,categories[i].id);          
        }
      }
      function updateSubCats(){
        var catSelect = this;
        var catid = this.value;
        var subcatSelect = document.getElementById("subcatsSelect");
        subcatSelect.options.length = 0; //delete all options if any present
        for(var i = 0; i < subcats[catid].length; i++){
          subcatSelect.options[i] = new Option(subcats[catid][i].val,subcats[catid][i].id);
        }
      }
    </script>
  </head>

  <body onload='loadCategories()'>
  
  <form method="post">
    <select name="cat" id='categoriesSelect'>
    </select>

    <select name="subcat" id='subcatsSelect'>
    </select>
    <button type="submit" name="submit">Submit</button>
  </form>
  <?php
  if (isset($_POST['submit'])) {
    $cat = $_POST['cat'];
    $subcat = $_POST['subcat'];
    $sql3 = "INSERT INTO `testcatsubcat`(`cat`,`subcat`) VALUES(:cat,:subcat)";
    $query3 = $dbh->prepare($sql3);
    $query3->bindParam(':cat', $cat, PDO::PARAM_STR);
    $query3->bindParam(':subcat', $subcat, PDO::PARAM_STR);
    $query3->execute();
    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
      echo "<script>alert('Info ADDED successfully');document.location = 'index.php';</script>";
    } else {
      echo "<script>alert('Something went wrong');</script>";
    }
  }
  ?>
  </body>
</html>