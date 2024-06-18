<?php session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
} ?>
<style>
    .card {
        cursor: pointer;
    }

    .card:hover {
        background-color: #f8f9fa;
    }

    .qty-input {
        display: flex;
        align-items: center;
    }

    .categories-row,
    .drinks-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .category-card,
    .drink-card {
        flex: 0 0 calc(20% - 20px);
        margin: 10px;
    }

    @media (max-width: 768px) {

        .category-card,
        .drink-card {
            flex: 1 0 calc(33.33% - 20px);
        }
    }

    .drink-card {
        border: none !important;
        --bs-card-border-color: transparent !important;
    }

    @media (max-width: 768px) {
        .drink-card {
            border: none !important;
            --bs-card-border-color: transparent !important;
        }
    }

    .card-title {
        font-size: 12px;
    }

    .drink-image {
        width: 150px;
        height: 270px;
        object-fit: cover;
        max-width: 120px;
        max-height: 270px;
    }

    .list-group-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }

    .item-name,
    .item-price {
        flex: 1;
    }

    .qty-input,
    .delete-btn {
        margin-left: auto;
    }

    .delete-btn {
        cursor: pointer;
        color: red;
        margin-left: 10px;
    }
</style>

<?php
$errors = [];
$success = "";

// Database connection
require("../controller/connection.php");

// Fetch categories from the database
$categories = [];
$stmt = $pdo->query("SELECT id, name FROM category WHERE status=1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}

// Fetch drinks from the database
$drinks = [];
$stmt = $pdo->prepare("SELECT id, name, price, image, cat_id FROM drink WHERE status=1");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $drinks[] = $row;
}

// Process order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['process_order'])) {
    $payment_type = $_POST['payment_type'];
    $total_payment = $_POST['total_payment'];
    $change = $_POST['change'];
  
    $total = 0;
    $orderDetails = []; // Array to store all order details

    // Validation
    if (!$payment_type) {
        $errors[] = "Payment type is required";
    }
    if (!$total_payment) {
        $errors[] = "Total payment is required";
    }
    if ($change === null || $change === '') { // Check if change is not set or empty
        $errors[] = "Change is required";
    }

    // Loop through the selected drinks and accumulate the total
    foreach ($_POST['qty'] as $drink_id => $qty) {
        if ($qty > 0) {
            $stmt = $pdo->prepare("SELECT price, cat_id FROM drink WHERE id = :drink_id");
            $stmt->bindValue(':drink_id', $drink_id);
            $stmt->execute();
            $drink = $stmt->fetch(PDO::FETCH_ASSOC);
            $item_total = $drink['price'] * $qty;
            $total += $item_total;

            // Store drink details for the order
            $orderDetails[] = [
                'drink_id' => $drink_id,
                'price' => $drink['price'],
                'qty' => $qty,
                'total' => $item_total
            ];
        }
    }

    // Check for errors before proceeding
    if (empty($errors)) {
        // Function to generate a unique numeric ID of a given length
        function generateUniqueNumericId($length = 10) {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $cus_id = generateUniqueNumericId(); // Unique numeric customer ID
        $invoice_id = generateUniqueNumericId(); // Unique numeric invoice ID
        $sale_date = date('Y-m-d'); // Current date

        // Begin a transaction for atomicity
        $pdo->beginTransaction();

        try {
            // Insert all items in one go
            foreach ($orderDetails as $orderItem) {
                $stmt = $pdo->prepare("INSERT INTO sale (invoice_id, cus_id, drink_id, price, qty, total, total_payment, `change`, payment_type, sale_date) VALUES (?,?,?,?,?,?,?,?,?,?)");
$stmt->execute([$invoice_id, $cus_id, $orderItem['drink_id'], $orderItem['price'], $orderItem['qty'], $orderItem['total'], $total_payment, $change, $payment_type, $sale_date]);

            }

            // Commit the transaction
            $pdo->commit();
            $success = "Order processed successfully.";
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $pdo->rollBack();
            $errors[] = "An error occurred while processing the order: " . $e->getMessage();
        }
    }
}
?>

<?php include('head.php') ?>
<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Categories -->
        <?php include('sidebar_manager.php') ?>
        <div class="col-md-5 mt-4">
            <h2>Categories</h2>
            <div class="categories-row">
                <?php foreach ($categories as $category) : ?>
                    <div class="category-card card" onclick="filterDrinks(<?php echo $category['id']; ?>)">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="drinks-row" id="drink-list">
                <?php if (!empty($drinks)) { ?>
                    <?php foreach ($drinks as $drink) : ?>
                        <div class="drink-card card drink-item" data-cat-id="<?php echo $drink['cat_id']; ?>">
                            <img src="<?php echo $drink['image']; ?>" class="card-img-top drink-image" alt="<?php echo htmlspecialchars($drink['name']); ?>" data-name="<?php echo htmlspecialchars($drink['name']); ?>" data-price="<?php echo $drink['price']; ?>" data-id="<?php echo $drink['id']; ?>">
                        </div>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <p>No drinks available for this category.</p>
                <?php } ?>
            </div>
        </div>
        <!-- Cart Items -->
        <div class="col-md-4 mt-4">
            <h2>Cart Items</h2>
            <?php if ($success) { ?>
                <!-- Bootstrap success alert -->
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php } ?>
            <?php if (!empty($errors)) { ?>
                <!-- Bootstrap error alert -->
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $error) {
                        echo $error . "<br>";
                    } ?>
                </div>
            <?php } ?>
            <!-- Cart form -->
            <form method="POST" action="">
                <div class="list-group" id="selected_drinks">
                    <!-- Selected drinks will be displayed here -->
                </div>

                <div class="mt-3">
                    <p>Subtotal: $<span id="subtotal_price">0.00</span></p>
                    <p>Total: $<span id="total_price">0.00</span></p>
                    <div class="form-group">
                        <label for="payment_type">Payment Type:</label>
                        <select name="payment_type" id="payment_type" class="form-control" style="width: 50%;">
                            <option value="cash">Cash</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                        </select>
                    </div>
                    <input type="hidden" name="process_order" value="1">
                    <div class="form-group">
                        <label for="total_payment">Total Payment:</label>
                        <input type="text" name="total_payment" id="total_payment" class="form-control" style="width: 50%;" required>
                    </div>
                    <div class="form-group">
                        <label for="change">Change:</label>
                        <input type="text" name="change" id="change" class="form-control" style="width: 50%;" readonly>
                    </div>
                    <button class="btn btn-primary btn-block mt-4" type="submit">Place Order</button>
                    <button class="btn btn-secondary btn-block mt-4" type="button" onclick="printInvoice('<?php echo $invoice_id; ?>')">Print Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JavaScript for handling click event on drink image
    document.querySelectorAll('.drink-image').forEach(item => {
        item.addEventListener('click', event => {
            const drinkName = item.dataset.name;
            const drinkPrice = item.dataset.price;
            const drinkId = item.dataset.id;
            const existingItem = document.querySelector(`#selected_drinks [data-id="${drinkId}"]`);

            if (existingItem) {
                const quantityElement = existingItem.querySelector('.quantity mx-3');
                let quantity = parseInt(quantityElement.innerText);

                quantity++;
                quantityElement.innerText = quantity;
                document.querySelector(`input[name="qty[${drinkId}]"]`).value = quantity; // Update hidden input field
            } else {
                // Create new item
                const newItem = document.createElement('div');
                newItem.classList.add('list-group-item', 'd-flex', 'align-items-center');
                newItem.dataset.id = drinkId;

                const itemName = document.createElement('span');
                itemName.classList.add('item-name', 'flex-grow-1', 'mr-2');
                itemName.innerText = drinkName;

                const itemPrice = document.createElement('span');
                itemPrice.classList.add('item-price', 'mr-3');
                itemPrice.innerText = `$${drinkPrice}`;

                const quantityContainer = document.createElement('div');
                quantityContainer.classList.add('qty-input');

                const minusBtn = document.createElement('span');
                minusBtn.classList.add('btn', 'btn-secondary', 'btn-sm', 'mr-1'); // Adjusted margin here
                minusBtn.innerText = '-';
                minusBtn.onclick = () => updateQuantity(minusBtn, -1);

                const quantityElement = document.createElement('span');
                quantityElement.classList.add('quantity', 'mx-2', 'mr-2');
                quantityElement.innerText = '1';

                const plusBtn = document.createElement('span');
                plusBtn.classList.add('btn', 'btn-secondary', 'btn-sm', 'ml-1'); // Adjusted margin here
                plusBtn.innerText = '+';
                plusBtn.onclick = () => updateQuantity(plusBtn, 1);

                quantityContainer.appendChild(minusBtn);
                quantityContainer.appendChild(quantityElement);
                quantityContainer.appendChild(plusBtn);

                const deleteBtn = document.createElement('span');
                deleteBtn.classList.add('delete-btn', 'ml-2');
                deleteBtn.innerHTML = '&times;';
                deleteBtn.onclick = () => removeItem(newItem);

                newItem.appendChild(itemName);
                newItem.appendChild(itemPrice);
                newItem.appendChild(quantityContainer);
                newItem.appendChild(deleteBtn);

                document.getElementById('selected_drinks').appendChild(newItem);

                // Update hidden input field
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `qty[${drinkId}]`;
                hiddenInput.value = 1;
                document.getElementById('selected_drinks').appendChild(hiddenInput);
            }

            // Calculate subtotal and total price
            calculatePrices();
        });
    });

    function removeItem(item) {
        const itemId = item.dataset.id;
        item.remove(); // Remove the item from the DOM

        // Also, update the hidden input field quantity
        document.querySelector(`input[name="qty[${itemId}]"]`).value = 0;

        // Recalculate prices
        calculatePrices();
    }

    function filterDrinks(cat_id) {
        document.querySelectorAll('.drink-item').forEach(item => {
            if (item.getAttribute('data-cat-id') == cat_id) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('.drink-item').forEach(item => {
        item.addEventListener('click', event => {
            const cat_id = item.getAttribute('data-cat-id');
            // Now you have the catId value which you can use as needed
            console.log(cat_id);
            // Your other logic here...
        });
    });

    function calculatePrices() {
        let subtotal = 0;
        document.querySelectorAll('.list-group-item').forEach(item => {
            const price = parseFloat(item.querySelector('.item-price').textContent.substring(1));
            const quantity = parseInt(item.querySelector('.quantity').innerText);
            subtotal += price * quantity;
        });
        document.getElementById('subtotal_price').textContent = subtotal.toFixed(2);
        document.getElementById('total_price').textContent = subtotal.toFixed(2); // Assuming no taxes or shipping for simplicity
    }

    // Function to update the change
    function updateChange() {
        const total_price = parseFloat(document.getElementById('total_price').textContent);
        const total_payment = parseFloat(document.getElementById('total_payment').value);

        if (!isNaN(total_payment)) {
            document.getElementById('change').value = (total_payment - total_price).toFixed(2);
        }
    }

    // Event listener for total payment input to calculate change dynamically
    document.getElementById('total_payment').addEventListener('input', updateChange);

    // Event listener for form submission
    document.querySelector('button[type="submit"]').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the form from submitting
        const total_price = parseFloat(document.getElementById('total_price').textContent);
        const total_payment = parseFloat(document.getElementById('total_payment').value);

        document.getElementById('total_payment').value = total_payment;
        document.getElementById('change').value = (total_payment - total_price).toFixed(2); // Ensure two decimal places for change

        // Now, submit the form
        this.form.submit();
    });

    function updateQuantity(button, change) {
        const quantityElement = button.parentElement.querySelector('.quantity');
        let quantity = parseInt(quantityElement.innerText) + change;
        if (quantity >= 1) {
            quantityElement.innerText = quantity;

            const drinkId = button.closest('.list-group-item').dataset.id;
            document.querySelector(`input[name="qty[${drinkId}]"]`).value = quantity;

            calculatePrices();
        }
    }

    function printInvoice(invoiceId) {
    const url = `print_invoice.php?invoice_id=${invoiceId}`;

    // Send an AJAX request to retrieve the invoice content
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Open a new window with the invoice content
            const printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
            // Print the invoice
            printWindow.print();
        })
        .catch(error => {
            console.error('Error generating invoice:', error);
        });
}

</script>