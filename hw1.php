<?php
/** HOMEWORK 1 - Oleksii Strapchev

 * Attempts creating a PDO object that allows connecting to the SQL server database.
 * Outputs connected message if succeeded.
 * Outputs error message embedded into the $e Exception if not succesful.
 */
include './config.php';


try{
  $pdo = new PDO ('mysql:host=localhost;dbname=employees',$user,$pass);
  echo "Connected!";
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOexception $e)
{
  echo $e->getMessage();
  die();
}

echo "attempting";
getTopPaid($pdo, 5);

/**
 * Queries the database using the $pdo object. The query has been developed according to the requirements in the assignment specification.
 * Unfortunately there is still a slight issue since an employee may appear twice if he has two titles.
 * @param  [pdo] $pdo    Passes PDO object into the function;
 * @param  [int] $number Sets the number of employees to return.
 * @return [null]         Null;
 */
function getTopPaid($pdo, $number)
{
  echo "Getting top: " . $number;

  $query = $pdo->prepare('SELECT titles.title, employees.first_name, employees.last_name, salaries.salary, departments.dept_name
  FROM salaries
  JOIN employees ON salaries.emp_no = employees.emp_no
  JOIN dept_emp ON dept_emp.emp_no = employees.emp_no
  JOIN departments ON dept_emp.dept_no = departments.dept_no
  JOIN titles ON titles.emp_no = (
      SELECT titles.emp_no FROM titles
      WHERE titles.emp_no = employees.emp_no
      ORDER BY titles.from_date DESC
      LIMIT 1)
  WHERE salaries.to_date > CURRENT_DATE
  ORDER BY salaries.salary DESC
  LIMIT ?');

$query->bindParam(1, $number, PDO::PARAM_INT);
$query->execute();
echo "Query OK";
  //$query->bindParam(':limiter', $number);

  //$query->execute();

  /**
   * Returns an array [5] with the query output rows as the array elements.
   */
   $result = $query->fetchAll(PDO::FETCH_ASSOC);
   echo '<pre>', print_r($result), '/<pre>';
   /**
    * Encodes the JSON representation of the query output (result)
    * Writes the JSON representation into a file 'toppaid.json'
    */
   try {
     $json = json_encode($result);
     $json_data = json_encode($result);
     file_put_contents('toppaid.json', $json_data);
     echo "Export succesful";
   } catch (Exception $e) {
     echo $e->getMessage();
     die();
   }
}

$result = null;
$pdo = null;
?>
