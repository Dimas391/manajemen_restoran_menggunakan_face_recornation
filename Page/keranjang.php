<?php
// Data produk dalam bentuk array
$products = [
    ['name' => 'BBQ', 'image' => '..\assets\img\beef\bbq_beef.jpg', 'quantity' => 1, 'price' => 25000],
    ['name' => 'CGrill Beef', 'image' => '..\assets\img\beef\grill_beef.jpeg', 'quantity' => 1, 'price' => 20000],
    ['name' => 'Potato Curry', 'image' => '..\assets\img\vegan\satay-sweet-potato-curry-17cc62d.webp', 'quantity' => 1, 'price' => 23000],
    ['name' => 'Roast Beef', 'image' => '..\assets\img\beef\roast beef.jpeg', 'quantity' => 2, 'price' => 45000],
    ['name' => 'Chicken Wings', 'image' => '..\assets\img\chicken\wigs.jpeg', 'quantity' => 1, 'price' => 100000],
    ['name' => 'Butternut Bquash', 'image' => '..\assets\img\vegan\Butternut-squash.webp', 'quantity' => 1, 'price' => 18000],
];

// Hitung total harga
$totalPrice = 0;
foreach ($products as $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="..\assets\style\keranjang.css">
    <script>
        function updateQuantity(index, increment) {
            const quantitySpan = document.getElementById('quantity-' + index);
            let quantity = parseInt(quantitySpan.innerText);
            if (increment) {
                quantity++;
            } else if (quantity > 0) {
                quantity--;
            }
            quantitySpan.innerText = quantity;
            updateTotal();
        }

        function updateTotal() {
            const products = <?= json_encode($products) ?>;
            let total = 0;
            products.forEach((product, index) => {
                const quantity = parseInt(document.getElementById('quantity-' + index).innerText);
                total += product.price * quantity;
            });
            document.getElementById('total-price').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</head>
<body>
    <div class="cart-container">
    <h2>Keranjang</h2>
    <table> 
        <tbody>
            <?php foreach ($products as $index => $product) : ?>
                <tr class="<?= $index == 3 ? 'highlight' : '' ?>">
                    <td>
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                        <?= $product['name'] ?>
                    </td>
                    <td class="quantity-control">
    <button onclick="updateQuantity(<?= $index ?>, false)">-</button>
    <span id="quantity-<?= $index ?>"><?= $product['quantity'] ?></span>
    <button onclick="updateQuantity(<?= $index ?>, true)">+</button>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        <div class="total-container">
    <div class="total-section">
        <span>Total</span>
        <span id="total-price" class="total-price">Rp <?= number_format($totalPrice, 0, ',', '.') ?></span>
    </div>
    <div>
        <button class="btn-next">
            <span class="icon-check"></span> Next Step
        </button>
    </div>
</div>



    </div>
</body>
</html>