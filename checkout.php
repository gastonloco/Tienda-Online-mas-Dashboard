<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order_btn'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, 'flat no. '. $_POST['flat'].', '. $_POST['street'].', '. $_POST['city'].', '. $_POST['country'].' - '. $_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ',$cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if($cart_total == 0){
      $message[] = 'Tu carrito esta vacío';
   }else{
      if(mysqli_num_rows($order_query) > 0){
         $message[] = 'pedido ya realizado!'; 
      }else{
         mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
         $message[] = '¡pedido realizado con éxito!';
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      }
   }
   
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>verificar</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>verificar</h3>
   <p> <a href="home.php">inicio</a> / verificar </p>
</div>

<section class="display-order">

   <?php  
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
   ?>
   <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo '$'.$fetch_cart['price'].'/-'.' x '. $fetch_cart['quantity']; ?>)</span> </p>
   <?php
      }
   }else{
      echo '<p class="empty">Tu carrito esta vacío</p>';
   }
   ?>
   <div class="grand-total"> total : <span>$<?php echo $grand_total; ?>/-</span> </div>

</section>

<section class="checkout">

   <form action="" method="post">
      <h3>Haga su pedido</h3>
      <div class="flex">
         <div class="inputBox">
            <span>tu nombre :</span>
            <input type="text" name="name" required placeholder="introduzca su nombre">
         </div>
         <div class="inputBox">
            <span>tu número :</span>
            <input type="number" name="number" required placeholder="ingrese tu numero">
         </div>
         <div class="inputBox">
            <span>tu email :</span>
            <input type="email" name="email" required placeholder="Introduce tu correo electrónico">
         </div>
         <div class="inputBox">
            <span>   método de pago :</span>
            <select name="method">
               <option value="contra reembolso">contra reembolso</option>
               <option value="tarjeta de crédito">tarjeta de crédito</option>
               <option value="efectivo">efectivo</option>
               <option value="cuenta corriente">cuenta corriente</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Nº de casa :</span>
            <input type="number" min="0" name="flat" required placeholder="Ej. 234.">
         </div>
         <div class="inputBox">
            <span>Dirección :</span>
            <input type="text" name="street" required placeholder="Ej. Mendoza">
         </div>
         <div class="inputBox">
            <span>ciudad :</span>
            <input type="text" name="city" required placeholder="Ej. Córdoba">
         </div>
         <div class="inputBox">
            <span>estado :</span>
            <input type="text" name="state" required placeholder="Ej. Córdoba">
         </div>
         <div class="inputBox">
            <span>país :</span>
            <input type="text" name="country" required placeholder="Ej. Argentina">
         </div>
         <div class="inputBox">
            <span>Código Postal :</span>
            <input type="number" min="0" name="pin_code" required placeholder="Ej. 50000">
         </div>
      </div>
      <input type="submit" value="ordenar ahora" class="btn" name="order_btn">
   </form>

</section>









<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>