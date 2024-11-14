<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beef Burger</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px; /* Added padding for mobile */
        }

        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%; /* Changed to 100% for mobile */
            max-width: 320px; /* Set max width */
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 20px;
            color: #555;
        }

        .burger-image {
            width: 100%;
            border-radius: 20px 20px 0 0;
        }

        .quantity-selector {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 15px 0;
        }

        .quantity-selector button {
            background-color: #a64af7;
            border: none;
            color: white;
            font-size: 20px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
        }

        .quantity-selector span {
            margin: 0 10px;
            font-size: 20px;
            color: #a64af7;
        }

        .product-title {
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-price {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }

        .ingredients {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping for mobile */
            justify-content: space-around;
            margin: 15px 0;
        }

        .ingredient {
            text-align: center;
            margin: 5px; /* Added margin for spacing */
        }

        .ingredient img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: #f5f5f5;
            padding: 5px;
        }

        .ingredient span {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #555;
        }

        .description {
            text-align: left;
            font-size: 12px;
            color: #777;
            margin: 15px 0;
        }

        .add-to-cart {
            background-color: #a64af7;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
        }

        .add-to-cart:hover {
            background-color: #8b39d6;
        }

        /* Media query for mobile adjustments */
        @media (max-width: 480px) {
            .product-title {
                font-size: 20px;
            }

            .product-price {
                font-size: 16px;
            }

            .quantity-selector span {
                font-size: 18px;
            }

            .description {
                font-size: 11px;
            }

            .add-to-cart {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a class="back-button" href="#">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img alt="Image of a beef burger with lettuce, tomato, and special sauce" class="burger-image" height="200" src="https://storage.googleapis.com/a1aa/image/pfwyr8d1cI3ZBintA95RbVxghjE7SJSGNyUQkUd1Lj8Lwo3JA.jpg" width="300" />
        <div class="quantity-selector">
            <button onclick="decreaseQuantity()">-</button>
            <span id="quantity">1</span>
            <button onclick="increaseQuantity()">+</button>
        </div>
        <div class="product-title">Beef Burger</div>
        <div class="product-price">Rp 30.000</div>
        <div class="ingredients">
            <div class="ingredient">
                <img alt="Beef" src="https://storage.googleapis.com/a1aa/image/qo3MM8yWFopgBRcfL2QKdbULYuZ3bpzoX1OdmBgulMcMwo3JA.jpg" />
                <span>Beef</span>
            </div>
            <div class="ingredient">
                <img alt="Lettuce" src="https://storage.googleapis.com/a1aa/image/GKBUxdZFrFqeZC5mqfddmj3A07di7LZrdi2ySbasvmUcgRvTA.jpg" />
                <span>Lettuce</span>
            </div>
            <div class="ingredient">
                <img alt="Olive Oil" src="https://storage.googleapis.com/a1aa/image/d0AyAzRSUCIrOF5nAMvWSNOWgiPMhkOJSzb5ZYATn3UFY07E.jpg" />
                <span>Olive Oil</span>
            </div>
            <div class="ingredient">
                <img alt="Egg" src="https://storage.googleapis.com/a1aa/image/xn8WH7kOSBKnHVI7Xkdhz94iCEqPi6fKlUKzPXNhMrlJwo3JA.jpg" />
                <span>Egg</span>
            </div>
            <div class="ingredient">
                <img alt="Tomato" src="https://storage.googleapis.com/a1aa/image/SkGS6Z3nJrooHt7FQbVO8K85Im9cGSN92UWNp6EEgMhGY07E.jpg" />
                <span>Tomato</span>
            </div>
        </div>
        <div class="description">
            Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts ...
            <a href="#">detail</a>
        </div>
        <button class="add-to-cart" onclick="addToCart()">Add to Cart</button>
    </div>
    <script>
        let quantity = 1;

        function increaseQuantity() {
            quantity++;
            document.getElementById('quantity').innerText = quantity;
        }

        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                document.getElementById('quantity').innerText = quantity;
            }
        }

        function addToCart() {
            alert('Added ' + quantity + ' Beef Burger(s) to cart.');
        }
    </script>
</body>
</html>