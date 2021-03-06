<?php
$page_title = "Car List";
include_once "header.php";


echo "<div class='left-button-margin'>";
echo "<a href='search_product.php' class='btn btn-primary pull-left'>Search Product</a>";
echo "</div>";
echo "<div class='right-button-margin'>";
echo "<a href='create_product.php' class='btn btn-info pull-right'>Create Product</a>";
echo "</div>";

// page given in URL parameter, default page is one
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// set number of records per page
$records_per_page = 10;

// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * $page) - $records_per_page;

// include database and object files
include_once 'config/database.php';
include_once 'objects/product.php';
include_once 'objects/category.php';


// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// query products
$stmt = $product->readAll($page, $from_record_num, $records_per_page);
$num = $stmt->rowCount();

// display the products if there are any
if ($num > 0) {

    $category = new Category($db);

    echo "<table class='table table-hover table-responsive table-bordered'>";
    echo "<tr>";
    echo "<th>Product</th>";
    echo "<th>Price</th>";
    echo "<th>Description</th>";
    echo "<th>Category</th>";
    echo "<th>Actions</th>";
    echo "</tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        echo "<tr>";
        echo "<td>{$name}</td>";
        echo "<td>{$price}</td>";
        echo "<td>{$description}</td>";
        echo "<td>";
        $category->id = $category_id;
        $category->readName();
        echo $category->name;
        echo "</td>";

        echo "<td>";
        // edit and delete button is here
        echo "<a href='read_product.php?id={$id}' class='btn left-margin'>Read</a>";
        echo "<a href='update_product.php?id={$id}' class='btn btn-success left-margin'>Edit</a>";
        echo "<a delete-id='{$id}' class='btn btn-danger delete-object'>Delete</a>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // paging buttons here
    include_once 'paging_product.php';

    // paging buttons will be here
} else {// tell the user there are no products
    echo "<div>No products found.</div>";
}
?>

<script>
    $(document).on('click', '.delete-object', function () {

        var id = $(this).attr('delete-id');
        var q = confirm("Are you sure?");

        if (q == true) {
            $.post('delete_product.php', {
                object_id: id
            }, function (data) {
                location.reload();
            }).fail(function () {
                alert('Unable to delete.');
            });
        }
        return false;
    });
</script>

<?php
include_once "footer.php";
?>