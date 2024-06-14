<!-- Left Side: Categories -->
<div class="col-xl-3 mt-4">
    <h2>Select Category</h2>
    <ul class="list-group">
        <?php foreach ($categorys as $cat) : ?>
            <li class="list-group-item">
                <a href="?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Right Side: Selected Drink and Order -->
<div class="col-xl-9 mt-4">
    <h2>Order Details</h2>
    <!-- Display Selected Drink -->
    <?php if ($selectedDrink) : ?>
        <div class="card mb-3">
            <img src="<?php echo $selectedDrink['image']; ?>" class="card-img-top" alt="Drink Image">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($selectedDrink['name']); ?></h5>
                <p class="card-text">Price: $<?php echo htmlspecialchars($selectedDrink['price']); ?></p>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="qty">Quantity:</label>
                        <input type="number" class="form-control" id="qty" name="qty" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-success">Add to Cart</button>
                </form>
            </div>
        </div>
    <?php else : ?>
        <p>Please select a drink from the left side.</p>
    <?php endif; ?>

    <!-- Payment Section -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment</h5>
            <p>Total: $<?php echo $total; ?></p>
            <form action="" method="post">
                <div class="form-group">
                    <label for="payment">Payment Method:</label>
                    <select class="form-control" id="payment" name="payment">
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Pay</button>
            </form>
        </div>
    </div>
</div>
