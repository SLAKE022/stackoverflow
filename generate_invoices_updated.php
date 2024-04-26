<?php
include "../includes/header.php";
include "../config/config.php";

// Ellenőrizzük, hogy van-e vevője a felhasználónak
$hasCustomers = false;
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $sql = "SELECT COUNT(*) as count FROM vevok WHERE user_id = :user_id";
    $stmt_hashCustomer = $conn->prepare($sql);
    $stmt_hashCustomer->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt_hashCustomer->execute();
    $result_hashCustomer = $stmt_hashCustomer->fetch(PDO::FETCH_ASSOC);
    if ($result_hashCustomer && $result_hashCustomer['count'] > 0) {
        $hasCustomers = true;
    }
}

/*function listProductsDropdown($conn, $userId) {
    $sql = "SELECT * FROM termekek WHERE user_id = :user_id";
    $stmt_listproduct = $conn->prepare($sql);
    $stmt_listproduct->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt_listproduct->execute();
    $dropdown ='';
    while ($row = $stmt_listproduct->fetch(PDO::FETCH_ASSOC)) {
        $dropdown .= '<option value="'.$row['id'].'">'.$row['nev'].'</option>';
    }
    return $dropdown;
}*/
if(isset($_GET['id'])){
    $id = $_GET['id'];
    if(isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $sql = "SELECT * FROM vevok WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result_customerSubmit = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result_customerSubmit) {
        } else {
            echo "Nem található vevő az adott azonosítóval a felhasználóhoz.";
        }
    } else {
        echo "A lekérdezés végrehajtásához be kell jelentkezni.";
    }
}
if(isset($_POST['submit'])) {
    $stmt = $conn->query("SELECT MAX(id) AS max_id FROM arajanlat");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $invoice_id = $result['max_id']+1;
    $userid = $_SESSION['id'];
    // Get the number of rows in the table
    //$numRows = $_POST["product_name"];

    foreach ($_POST['product_name'] as $key => $value) {
        echo "<script>alert('$value');</script>";
    }
    // Prepare the SQL statement
    //$stmt = $conn->prepare("INSERT INTO termekek (user_id, arajanlat_id, nev, netto_ar, afa, teljes_ar, egyseg, megjegyzes, darab)
    //                           VALUES (:user_id, :arajanlat_id, :nev, :netto_ar, :afa, :teljes_ar, :egyseg, :megjegyzes, :darab)");
/*
    // Loop through each row of the table
    for ($i = 0; $i < $numRows; $i++) {
        $test = $_POST["product_name"][$i];
        echo "<script>alert('$test');</script>";
        // Bind parameters
        /*$stmt->bindParam(':user_id', $invoiceid);
        $stmt->bindParam(':arajanlat_id', $userid);
        $stmt->bindParam(':nev', $_POST['name'][$i]);
        $stmt->bindParam(':netto_ar', $_POST['n_price'][$i]);
        $stmt->bindParam(':afa', $_POST['tax'][$i]);
        $stmt->bindParam(':teljes_ar', $_POST['f_price'][$i]);
        $stmt->bindParam(':egyseg', $_POST['unit'][$i]);
        $stmt->bindParam(':megjegyzes', $_POST['comment'][$i]);
        $stmt->bindParam(':darab', $_POST['qty'][$i]);

        // Execute the statement
        $stmt->execute();
    }*/
}

?>

<form class="form-control mt-5" method="POST" action="generate_invoices.php" id="inser_form">

<?php if ($hasCustomers) : ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
                <h4 class="text-center mt-3">Árajánlat létrehozás</h4>
                <div class="mb-3">
                    <label for="customer" class="form-label">Vevő</label>
                    <input type="text" name="customer_name" class="form-control" id="" value="<?php echo $result_customerSubmit["cegnev"]?>">
                </div>
                <div class="col">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Ajánlatszám</label>
                    <div class="">
                        <input type="email" name="email" class="form-control" id="" value="">
                    </div>
                </div>
                <div class="col">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Keletkezésidátum</label>
                    <div class="">
                        <input type="email" name="email" class="form-control" id="" value="">
                    </div>
                </div>
                <div class="col">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Lejáratidátum</label>
                    <div class="">
                        <input type="password" name="password" class="form-control" id="inputPassword">
                    </div>
                </div>
        </div>
        <hr>
        <!-- shipping details -->
        <?php if (isset($_GET['id'])) : ?>
            <div class="row">
                <h4 class="text-center mt-3">Szállítási cím</h4>
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Irányítószám</label>
                    <div class="">
                        <input type="text" name="postcode" class="form-control" id="" value="<?php echo $result_customerSubmit["iranyitoszam"]?>">
                    </div>
                </div>
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Város</label>
                    <div class="">
                        <input type="text" name="city" class="form-control" id="" value="<?php echo $result_customerSubmit["varos"]?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Utca</label>
                    <div class="">
                        <input type="text" name="street" class="form-control" id="" value="<?php echo $result_customerSubmit["utca"]?>">
                    </div>
                </div>
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Házszám</label>
                    <div class="">
                        <input type="text" name="street_number" class="form-control" id="" value="<?php echo $result_customerSubmit["hazszam"]?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Telefonszám</label>
                    <div class="">
                        <input type="text" name="phone_number" class="form-control" id="" value="<?php echo $result_customerSubmit["telefonszam"]?>">
                    </div>
                </div>
                <div class="col">
                    <label for="" class="col-sm-2 col-form-label">Megjegyzés</label>
                    <div class="">
                        <input type="text" name="tax_number" class="form-control" id="" value="">
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end of shipping details -->
        <div class="table-responsive">
            <table class="table table-bordered" id="item_table">
                <thead>
                <tr>
                    <th>Név</th>
                    <th>Darabszám</th>
                    <th>Egység</th>
                    <th>Áfa</th>
                    <th>Nettó Ár</th>
                    <th>Teljes Ár</th>
                    <th>Megjegyzés</th>
                    <th><button type="button" name="add" class="btn btn-success btn-xs add" onclick="btnAdd()">Új sor<span class="glyphicon glyphicon-plus"></span></button></th>
                </tr>
                </thead>
                <tbody id="Tbody">
                    <tr id="Trow" class="d-none"">

                    <td><input type="text" class="form-control" name="product_name[]" onchange="Calc(this)"></td>
                    <td><input type="number" class="form-control text-end" name="qty[]" onchange="Calc(this)"></td>
                    <td><input type="text" class="form-control text-end" name="unit[]" onchange="Calc(this)"></td>
                    <td><input type="number" class="form-control text-end" name="tax[]" onchange="Calc(this)"></td>
                    <td><input type="number" class="form-control text-end" name="n_price[]" onchange="Calc(this)"></td>
                    <td><input type="number" class="form-control text-end" name="f_price[]" ></td>
                    <td><input type="text" class="form-control text-end" name="comment"></td>
                    <td><button type="button" class="btn btn-danger btn-xs delete" onclick="btnDelete(this)">Törlés</button></td>
                    </tr>

                <div align="center">

                </div>
                </tbody>

            </table>
            <div class="row">
                <div class="col-8">
                    <div class="input-group mb-3"><span class="input-group-text"  id="basic-addon1">Termékek száma</span>
                    <input type="number" class="form-control"  id="count_products"  name="count_products"></div>
                </div>
                <div class="col-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"  id="basic-addon1">Nettó ára</span>
                        <input type="number" class="form-control"  id="fnTotal"  name="fnTotal" >
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"  id="basic-addon1">Áfa</span>
                        <input type="number" class="form-control" id="ftax" name="ftax" >
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"  id="basic-addon1">Teljes összeg</span>
                        <input type="number" class="form-control" id="fTotal" name="fTotal" >
                    </div>
                </div>
            </div>
        <button name="submit" class="w-100 btn btn-lg btn-primary mt-4 mb-4" type="submit" >Árajánlat felvitele</button>
    </div>
    </div>
    <div class="container">
        <?php else : ?>
            <!-- Ha nincs vevő, jelenítsük meg a vevő felvételi linket -->
            <p><a href="http://localhost/Szakdolgozat/customers/generate_customer.php">Vevőt itt tudsz hozzá létrehozni</a>.</p>
        <?php endif; ?>
    </div>
    </div>
</form>

    <script>
        var row =0;
        function btnAdd() {
            row++;
            document.getElementById("count_products").value = row;
            var v = $('#Trow').clone().appendTo('#Tbody');
            $(v).find("input").val('');
            $(v).removeClass("d-none");
        }

        function btnDelete(v){
            row--;
            document.getElementById("count_products").value = row;
            $(v).parent().parent().remove();
            GetTotals();
        }

        function Calc(v){
            var index = $(v).parent().parent().index();

            var qty = document.getElementsByName("qty[]")[index].value;
            var tax = document.getElementsByName("tax[]")[index].value;
            var n_price = document.getElementsByName("n_price[]")[index].value;
            var f_price = n_price*(1+(tax/100))*qty;
            document.getElementsByName("f_price[]")[index].value = f_price;
            GetTotals();
        }

        function GetTotals(){
            var sum_f = 0;
            var sum_n = 0;

            var f_price = document.getElementsByName("f_price[]");
            var n_price = document.getElementsByName("n_price[]");


            var qty = document.getElementsByName("qty[]");


            for(var i=0; i<f_price.length; i++){
                //total ar szamitas
                var total_f = f_price[i].value;
                sum_f = +(sum_f) + + (total_f);
            }
            for(var i=0; i<n_price.length; i++){
                //total netto ar szamitas
                var total_n = n_price[i].value * qty[i].value;
                sum_n = +(sum_n) + + (total_n);
            }
            //teljes ar
            document.getElementById("fTotal").value = sum_f;
            //netto ar
            document.getElementById("fnTotal").value = sum_n;
            //afa tartalom
            document.getElementById("ftax").value = (sum_f - sum_n);
        }
    </script>
<?php require "../includes/footer.php"; ?>
